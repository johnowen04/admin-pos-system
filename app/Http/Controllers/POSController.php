<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

use \App\Models\Product;
use App\Models\SalesInvoice;
use Illuminate\Support\Facades\Auth;

class POSController extends Controller
{
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get the outlet(s) the user can access
        $accessibleOutletIds = $user->employee->outlets->pluck('id')->toArray();

        // Filter products by accessible outlets
        $products = Product::whereHas('outlets', function ($query) use ($accessibleOutletIds) {
            $query->whereIn('outlets_id', $accessibleOutletIds);
        })->get();

        // Filter categories by accessible outlets
        $categories = Category::all();

        // Generate a unique invoice number
        $currentDate = now()->format('Ymd'); // Get the current date in YYYYMMDD format
        $lastInvoice = SalesInvoice::where('invoice_number', 'like', "INV-$currentDate-%")
            ->orderBy('id', 'desc')
            ->first();

        $lastInvoiceNumber = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -3)) : 0; // Extract the last 3 digits
        $newInvoiceNumber = 'INV-' . $currentDate . '-' . str_pad($lastInvoiceNumber + 1, 3, '0', STR_PAD_LEFT);

        // Pass the filtered data to the view
        return view('pos.index', [
            'products' => $products,
            'categories' => $categories,
            'invoiceNumber' => $newInvoiceNumber,
        ]);
    }

    public function payment(Request $request)
    {
        $cart = json_decode($request->input('cart'), true);
        $grandTotal = $request->input('grandTotal');
        $newInvoiceNumber = $request->input('invoiceNumber');

        return view('pos.payment', [
            'cart' => $cart,
            'grandTotal' => $grandTotal,
            'invoiceNumber' => $newInvoiceNumber,
        ]);
    }

    public function receipt(Request $request)
    {
        // Decode the cart data from the request
        $cart = json_decode($request->input('cart'), true);
        $grandTotal = $request->input('grandTotal');
        $outletId = $request->input('outletId');
        $newInvoiceNumber = $request->input('invoiceNumber');
        
        // Create the sales invoice
        $salesInvoice = SalesInvoice::create([
            'outlets_id' => $outletId,
            'invoice_number' => $newInvoiceNumber,
            'grand_total' => $grandTotal,
            'description' => 'POS Transaction',
            'nip' => Auth::user()->employee->nip, // Assuming the authenticated user has an employee relationship
        ]);

        // Attach products to the sales invoice
        foreach ($cart as $item) {
            $salesInvoice->products()->attach(key($cart), [
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total_price' => $item['quantity'] * $item['price'],
            ]);

            // Update the stock for the product in the outlet
            $product = Product::where('sku', key($cart))->first();
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

        // Return the receipt view with the data
        return view('pos.receipt', [
            'cart' => $cart,
            'grandTotal' => $grandTotal,
            'outletId' => $outletId,
            'invoiceNumber' => $newInvoiceNumber,
        ]);
    }
}
