<?php

namespace App\Http\Controllers;

use App\Services\AccessControlService;
use App\Services\SalesInvoiceService;
use App\Services\CategoryService;
use App\Services\InventoryService;
use App\Services\OutletService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class POSController extends Controller
{
    protected $salesInvoiceService;
    protected $categoryService;
    protected $inventoryService;
    protected $outletService;
    protected $productService;
    protected $accessControlService;

    /**
     * Constructor to inject the services.
     */
    public function __construct(
        SalesInvoiceService $salesInvoiceService,
        CategoryService $categoryService,
        InventoryService $inventoryService,
        OutletService $outletService,
        ProductService $productService
    ) {
        $this->middleware('permission:pos.view|pos.*')->only(['index', 'payment', 'receipt', 'getCart']);
        $this->middleware('permission:pos.create|pos.*')->only(['addToCart', 'removeFromCart', 'clearCart', 'processPayment']);

        $this->salesInvoiceService = $salesInvoiceService;
        $this->categoryService = $categoryService;
        $this->inventoryService = $inventoryService;
        $this->outletService = $outletService;
        $this->productService = $productService;

        $this->accessControlService = app(AccessControlService::class);
    }

    /**
     * Display the POS interface.
     */
    public function index()
    {
        $selectedOutletId = session('selected_outlet_id');

        if ($this->accessControlService->isSuperUser()) {
            $accessibleOutlets = $this->outletService->getAllOutlets();
        } else {
            $accessibleOutlets = $this->accessControlService->getUser()->employee->outlets;
        }

        if ($accessibleOutlets->isEmpty()) {
            return view('pos.index')->with('error', 'You do not have any outlets assigned. Please contact an administrator.');
        }

        if ($selectedOutletId == 'all') {
            $products = $this->inventoryService->getStocksAllOutlet();
        } elseif ($selectedOutletId && $accessibleOutlets->contains('id', $selectedOutletId)) {
            $products = $this->inventoryService->getStocksByOutlet($selectedOutletId);
        } else {
            $selectedOutletId = $accessibleOutlets[0]->id;
            session(['selected_outlet_id' => $selectedOutletId]);

            $products = $this->inventoryService->getStocksByOutlet($selectedOutletId);
        }

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
        $employee_id = $this->accessControlService->getUser()->employee->id ?? null;
        $created_by = $this->accessControlService->getUser()->id;
        $outletId = session('selected_outlet_id');

        $productIds = array_keys($cart);

        $dbProducts = $this->productService->getProductsByIds($productIds);

        $productLookup = [];
        foreach ($dbProducts as $product) {
            $productLookup[$product->id] = $product;
        }

        $products = [];
        foreach ($cart as $id => $product) {
            $product['id'] = $id;

            if (isset($productLookup[$id]) && isset($productLookup[$id]->base_price)) {
                $product['base_price'] = $productLookup[$id]->base_price;
            } else {
                Log::warning("Product ID {$id} in cart not found in database or missing base price");
                $product['base_price'] = 0;
            }

            $products[] = $product;
        }

        $validatedData = [
            'invoice_number' => $nextInvoiceNumber,
            'grand_total' => $grandTotal,
            'description' => 'POS Transaction',
            'outlet_id' => $outletId,
            'products' => $products,
            'employee_id' => $employee_id,
            'created_by' => $created_by,
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
            $outletName = session('selected_outlet');
            $receiptCreator = $this->accessControlService->getUser()->employee ?? $this->accessControlService->getUser();
            $cart = [];
            $grandTotal = 0;
            $invoiceNumber = '';
            $date = now()->format('d/m/Y H:i');
            $amountPaid = null;
            $paymentMethod = null;

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

            return view('pos.receipt', [
                'outletName' => $outletName,
                'receiptCreator' => $receiptCreator,
                'date' => $date,
                'invoiceNumber' => $invoiceNumber,
                'cart' => $cart,
                'grandTotal' => $grandTotal,
                'amountPaid' => $amountPaid,
                'isVoided' => $isVoided ?? false,
                'previousRoute' => $previousRoute,
            ]);
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
                'outletName' => $sales->outlet->name,
                'receiptCreator' => $sales->employee->name ?? $sales->creator->name,
                'date' => $sales->created_at->format('d/m/Y H:i'),
                'invoiceNumber' => $sales->invoice_number,
                'cart' => $cart,
                'grandTotal' => $sales->grand_total,
                'amountPaid' => $sales->amount_paid ?? $sales->grand_total,
                'isVoided' => $sales->is_voided ?? false,
                'paymentMethod' => $sales->payment_method ?? 'Cash',
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
