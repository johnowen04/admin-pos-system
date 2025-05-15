<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Models\Product;
use App\Models\SalesInvoice;
use App\Services\SalesInvoiceService;
use App\Services\CategoryService;
use App\Services\OutletService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;

class POSController extends Controller
{
    protected $salesInvoiceService;
    protected $categoryService;
    protected $outletService;
    protected $productService;

    /**
     * Constructor to inject the services.
     */
    public function __construct(
        SalesInvoiceService $salesInvoiceService,
        CategoryService $categoryService,
        OutletService $outletService,
        ProductService $productService
    ) {
        $this->salesInvoiceService = $salesInvoiceService;
        $this->categoryService = $categoryService;
        $this->outletService = $outletService;
        $this->productService = $productService;
    }

    /**
     * Display the POS interface.
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get the outlet(s) the user can access
        $accessibleOutletIds = $user->employee->outlets->pluck('id')->toArray();

        // Filter products by accessible outlets
        $products = $this->productService->getProductsByOutlets($accessibleOutletIds);

        // Get all categories
        $categories = $this->categoryService->getAllCategories();

        // Get next invoice number
        $nextInvoiceNumber = $this->salesInvoiceService->generateSalesInvoiceNumber();

        // Pass the filtered data to the view
        return view('pos.index', [
            'products' => $products,
            'categories' => $categories,
            'invoiceNumber' => $nextInvoiceNumber,
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function addToCart(Request $request)
    {
        $product = $request->only(['sku', 'name', 'unit_price', 'quantity']);

        // Retrieve the cart from the session or initialize it as an empty array
        $cart = session()->get('cart', []);

        // Check if the product already exists in the cart
        if (isset($cart[$product['sku']])) {
            // If increment or decrement by one item
            if ($product['quantity'] == 1 || $product['quantity'] == -1) {
                // Update the quantity of the existing product
                $cart[$product['sku']]['quantity'] += $product['quantity'];
            } else {
                // Update the quantity and total price
                $cart[$product['sku']]['quantity'] = $product['quantity'];
            }
        } else {
            // Add the product to the cart
            $cart[$product['sku']] = [
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
            ];
        }

        // Save the cart back to the session
        session()->put('cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    /**
     * Remove a product from the cart.
     */
    public function removeFromCart(Request $request)
    {
        $sku = $request->input('sku');

        // Retrieve the cart from the session
        $cart = session()->get('cart', []);

        // Remove the product from the cart
        if (isset($cart[$sku])) {
            unset($cart[$sku]);
        }

        // Save the updated cart back to the session
        session()->put('cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    /**
     * Get the cart.
     */
    public function getCart()
    {
        $cart = session()->get('cart', []);

        // Calculate total price dynamically
        $cartWithTotals = [];
        $grandTotal = 0;

        foreach ($cart as $sku => $item) {
            $item['total_price'] = $item['quantity'] * $item['unit_price'];
            $cartWithTotals[$sku] = $item;
            $grandTotal += $item['total_price'];
        }

        return response()->json([
            'cart' => $cartWithTotals,
            'grand_total' => $grandTotal,
        ]);
    }

    /**
     * Clear the cart.
     */
    public function clearCart()
    {
        session()->forget('cart');
        return response()->json(['success' => true]);
    }

    /**
     * Display the payment page.
     */
    public function payment()
    {
        // Get next invoice number
        $nextInvoiceNumber = $this->salesInvoiceService->generateSalesInvoiceNumber();

        // Retrieve the cart from the session
        $cart = session()->get('cart', []);
        $grandTotal = array_reduce($cart, function ($total, $item) {
            return $total + ($item['quantity'] * $item['unit_price']);
        }, 0);

        return view('pos.payment', [
            'cart' => $cart,
            'grandTotal' => $grandTotal,
            'invoiceNumber' => $nextInvoiceNumber,
        ]);
    }

    /**
     * Process the payment.
     */
    public function processPayment()
    {
        // Retrieve the cart and grand total from the session
        $cart = session()->get('cart', []);
        $grandTotal = array_reduce($cart, function ($total, $item) {
            return $total + ($item['quantity'] * $item['unit_price']);
        }, 0);

        // Perform payment processing logic here (e.g., save to database, generate invoice, etc.)

        // Get next invoice number
        $nextInvoiceNumber = $this->salesInvoiceService->generateSalesInvoiceNumber();

        // Get the outlet ID from the authenticated user
        $outletId = Auth::user()->employee->outlets[0]->id;

        // Create the sales invoice
        $salesInvoice = SalesInvoice::create([
            'outlets_id' => $outletId,
            'invoice_number' => $nextInvoiceNumber,
            'grand_total' => $grandTotal,
            'description' => 'POS Transaction',
            'nip' => Auth::user()->employee->nip, // Assuming the authenticated user has an employee relationship
        ]);

        // Attach products to the sales invoice and update stock
        foreach ($cart as $sku => $item) {
            // Attach the product to the sales invoice
            $salesInvoice->products()->attach($sku, [
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);

            // Update the stock for the product in the outlet
            $product = Product::where('sku', $sku)->first();
            if ($product) {
                $existingQuantity = $product->outlets()
                    ->where('outlets_id', $outletId)
                    ->first()
                    ->pivot
                    ->quantity ?? 0;

                $product->outlets()->syncWithoutDetaching([
                    $outletId => [
                        'quantity' => $existingQuantity - $item['quantity'], // Reduce stock
                    ],
                ]);
            }
        }

        // Save the grand total to the session
        session()->put('grandTotal', $grandTotal);

        // Save the invoice number to the session
        session()->put('invoiceNumber', $nextInvoiceNumber);

        // Redirect to a receipt or success page
        return redirect()->route('pos.receipt');
    }

    /**
     * Display the receipt.
     */
    public function receipt()
    {
        // Retrieve the cart and other data from the session
        $cart = session()->get('cart', []);
        $grandTotal = session()->get('grandTotal', 0);
        $invoiceNumber = session()->get('invoiceNumber', '');

        // Validate that the invoice number is set
        if (empty($invoiceNumber)) {
            return redirect()->route('pos.index')->with('error', 'No invoice number found. Please complete the payment first.');
        }

        // Validate that the cart is not empty
        if (empty($cart)) {
            return redirect()->route('pos.index')->with('error', 'The cart is empty. Please add items to the cart.');
        }

        // Clear the cart after processing the receipt
        session()->forget('cart');
        session()->forget('grandTotal');
        session()->forget('invoice_number');

        // Redirect to the receipt page with the sales invoice details
        return view('pos.receipt', [
            'cart' => $cart,
            'grandTotal' => $grandTotal,
            'invoiceNumber' => $invoiceNumber,
        ]);
    }
}
