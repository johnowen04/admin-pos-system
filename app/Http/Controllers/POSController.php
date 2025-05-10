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
        $categories = Category::whereHas('outlets', function ($query) use ($accessibleOutletIds) {
            $query->whereIn('outlets_id', $accessibleOutletIds);
        })->get();

        // Pass the filtered data to the view
        return view('pos.index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function payment(Request $request)
    {
        $cart = json_decode($request->input('cart'), true);
        $grandTotal = $request->input('grand_total');

        return view('pos.payment', [
            'cart' => $cart,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function receipt(Request $request)
    {
        // Decode the cart data from the request
        $cart = json_decode($request->input('cart'), true);
        $grandTotal = $request->input('grand_total');
        $outletId = $request->input('outlet_id');

        // Generate a unique invoice number
        $lastInvoice = SalesInvoice::orderBy('id', 'desc')->first();
        $lastInvoiceNumber = $lastInvoice ? intval(preg_replace('/[^0-9]/', '', $lastInvoice->invoice_number)) : 0;
        $newInvoiceNumber = 'INV-' . str_pad($lastInvoiceNumber + 1, 4, '0', STR_PAD_LEFT);

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
