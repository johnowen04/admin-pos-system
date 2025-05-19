<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\SalesInvoiceService;
use App\Services\CategoryService;
use App\Services\OutletService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $this->middleware('permission:pos.view')->only(['index', 'show']);
        $this->middleware('permission:pos.create')->only(['create', 'store']);
        $this->middleware('permission:pos.edit')->only(['edit', 'update']);
        $this->middleware('permission:pos.delete')->only(['destroy']);

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
        $user = Auth::user();
        $accessibleOutletIds = $user->employee->outlets->pluck('id')->toArray();
        $products = $this->outletService->getProductsWithStocksFromOutlet($accessibleOutletIds[0]);
        $categories = $this->categoryService->getAllCategories();
        $nextInvoiceNumber = $this->salesInvoiceService->generateSalesInvoiceNumber();
        $cart = session()->get('cart', []);

        return view('pos.index', [
            'products' => $products,
            'categories' => $categories,
            'invoiceNumber' => $nextInvoiceNumber,
            'cart' => $cart,
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function addToCart(Request $request)
    {
        $type = $request->input('type');
        $product = $request->only(['id', 'name', 'unit_price', 'quantity']);
        $cart = session()->get('cart', []);
        $productId = $product['id'];

        if (isset($cart[$productId])) {
            if (in_array($product['quantity'], [1, -1]) && $type === 'increment') {
                $cart[$productId]['quantity'] += $product['quantity'];
            } else {
                $cart[$productId]['quantity'] = $product['quantity'];
            }
        } else {
            $cart[$productId] = [
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
            ];
        }

        session()->put('cart', $cart);
        return response()->json(['success' => true, 'cart' => $cart]);
    }

    /**
     * Remove a product from the cart.
     */
    public function removeFromCart(Request $request)
    {
        $id = $request->input('id');
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        session()->put('cart', $cart);
        return response()->json(['success' => true, 'cart' => $cart]);
    }

    /**
     * Get the cart.
     */
    public function getCart()
    {
        $cart = session()->get('cart', []);
        $grandTotal = 0;

        foreach ($cart as $id => $item) {
            $cart[$id]['total_price'] = $item['quantity'] * $item['unit_price'];
            $grandTotal += $cart[$id]['total_price'];
        }

        return response()->json([
            'cart' => $cart,
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

        $nextInvoiceNumber = $this->salesInvoiceService->generateSalesInvoiceNumber();
        $cart = session()->get('cart', []);
        $grandTotal = array_reduce($cart, function ($total, $item) {
            return $total + ($item['quantity'] * $item['unit_price']);
        }, 0);

        if (!$cart) {
            return redirect()->route('pos.index')
                ->with('error', 'Receipt data not found. Please complete a transaction first.');
        }

        return view('pos.payment', [
            'cart' => $cart,
            'grandTotal' => $grandTotal,
            'invoiceNumber' => $nextInvoiceNumber,
        ]);
    }

    /**
     * Process the payment.
     */
    public function processPayment(Request $request)
    {
        $cart = session()->get('cart', []);
        $grandTotal = array_reduce($cart, function ($total, $item) {
            return $total + ($item['quantity'] * $item['unit_price']);
        }, 0);

        $nextInvoiceNumber = $this->salesInvoiceService->generateSalesInvoiceNumber();
        $employee_id = Auth::user()->employee->id;
        $outletId = Auth::user()->employee->outlets[0]->id;

        $products = [];
        foreach ($cart as $id => $product) {
            $product['id'] = $id;
            $products[] = $product;
        }

        $validatedData = [
            'invoice_number' => $nextInvoiceNumber,
            'grand_total' => $grandTotal,
            'description' => 'POS Transaction',
            'outlet_id' => $outletId,
            'products' => $products,
            'employee_id' => $employee_id,
        ];

        if ($request->filled('amount_paid')) {
            session()->put('amountPaid', $request->amount_paid);
        }

        if ($request->filled('payment_method')) {
            session()->put('paymentMethod', $request->payment_method);
        }

        $this->salesInvoiceService->createSalesInvoice($validatedData);
        session()->put('grandTotal', $grandTotal);
        session()->put('invoiceNumber', $nextInvoiceNumber);

        return redirect()->route('pos.receipt');
    }

    /**
     * Display the transaction receipt.
     *
     * @param int|null $id Optional sales invoice ID for retrieving historical receipts
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function receipt($id = null)
    {
        try {
            $previousRoute = $this->getPreviousRoute();
            $cart = [];
            $grandTotal = 0;
            $invoiceNumber = '';
            $date = now()->format('d/m/Y H:i');
            $amountPaid = null;
            $paymentMethod = null;

            $employee = Auth::user()->employee;
            $outlet = $employee->outlets->first();

            if ($previousRoute === 'pos.payment') {
                $receiptData = $this->getReceiptDataFromSession();

                if (!$receiptData) {
                    return redirect()->route('pos.index')
                        ->with('error', 'Receipt data not found. Please complete a transaction first.');
                }

                extract($receiptData);
                $this->clearSessionData();
            } else if ($id) {
                $receiptData = $this->getReceiptDataFromDatabase($id);

                if (!$receiptData) {
                    return redirect()->route('pos.index')
                        ->with('error', 'Receipt not found. Please check the invoice ID and try again.');
                }

                extract($receiptData);
            } else {
                return redirect()->route('pos.index')
                    ->with('error', 'Invalid receipt access. Please complete a transaction first.');
            }

            return view('pos.receipt', compact(
                'cart',
                'grandTotal',
                'date',
                'employee',
                'outlet',
                'invoiceNumber',
                'previousRoute',
                'amountPaid',
                'paymentMethod'
            ));
        } catch (\Exception $e) {
            Log::error('Error generating receipt: ' . $e->getMessage());
            return redirect()->route('pos.index')
                ->with('error', 'An error occurred while generating the receipt.');
        }
    }

    /**
     * Get the name of the previous route.
     *
     * @return string|null
     */
    private function getPreviousRoute()
    {
        try {
            return app('router')->getRoutes()->match(
                app('request')->create(url()->previous())
            )->getName();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get receipt data from session.
     *
     * @return array|null
     */
    private function getReceiptDataFromSession()
    {
        $cart = session()->get('cart', []);
        $grandTotal = session()->get('grandTotal', 0);
        $invoiceNumber = session()->get('invoiceNumber', '');
        $amountPaid = session()->get('amountPaid', $grandTotal);
        $paymentMethod = session()->get('paymentMethod', 'Cash');

        if (empty($cart) || empty($invoiceNumber)) {
            return null;
        }

        return [
            'cart' => $cart,
            'grandTotal' => $grandTotal,
            'invoiceNumber' => $invoiceNumber,
            'amountPaid' => $amountPaid,
            'paymentMethod' => $paymentMethod
        ];
    }

    /**
     * Get receipt data from database by invoice ID.
     *
     * @param int $id
     * @return array|null
     */
    private function getReceiptDataFromDatabase($id)
    {
        try {
            $sales = $this->salesInvoiceService->getSalesInvoiceById($id);

            if (!$sales) {
                return null;
            }

            $cart = [];
            foreach ($sales->products as $product) {
                $cart[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $product->pivot->quantity,
                    'unit_price' => $product->pivot->unit_price
                ];
            }

            return [
                'cart' => $cart,
                'grandTotal' => $sales->grand_total,
                'invoiceNumber' => $sales->invoice_number,
                'date' => $sales->created_at->format('d/m/Y H:i'),
                'employee' => $sales->employee,
                'outlet' => $sales->employee->outlets->first(),
                'amountPaid' => $sales->amount_paid ?? $sales->grand_total,
                'paymentMethod' => $sales->payment_method ?? 'Cash'
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching sales invoice: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear session data after processing the receipt.
     *
     * @return void
     */
    private function clearSessionData()
    {
        session()->forget([
            'cart',
            'grandTotal',
            'invoiceNumber',
            'amountPaid',
            'paymentMethod'
        ]);
    }
}
