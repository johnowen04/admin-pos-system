<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class InvoiceProductTable extends Component
{
    public $method = 'POST';
    public $invoiceType = 'Purchase';
    public $productRows = [];
    public $totalQuantity = 0;
    public $totalPrice = 0;
    public $outletId = null;
    public $invoice = null;

    public function mount($method = 'POST', $invoiceType = 'Purchase', $invoice = null, $outletId = null)
    {
        $this->method = $method;
        $this->invoiceType = $invoiceType;
        $this->outletId = $outletId;
        $this->invoice = $invoice;

        if ($this->invoice && $this->invoice->products) {
            foreach ($this->invoice->products as $index => $product) {
                $this->productRows[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'base_price' => $product->pivot->base_price,
                    'quantity' => $product->pivot->quantity,
                    'unit_price' => $product->pivot->unit_price,
                    'total_price' => $product->pivot->quantity * $product->pivot->unit_price
                ];
            }

            $this->calculateTotals();
        }
    }

    #[On('outletChanged')]
    public function outletChanged($outletId)
    {
        $this->outletId = $outletId;
        if (!empty($this->productRows)) {
            $this->productRows = [];
            $this->calculateTotals();
        }
    }

    #[On('productSelected')]
    public function handleSelectedProduct($product)
    {
        foreach ($this->productRows as $row) {
            if ($row['id'] == $product['id']) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'Product already added to invoice'
                ]);
                return;
            }
        }

        $this->productRows[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'base_price' => $product['base_price'] ?? $product['price'],
            'quantity' => 1,
            'unit_price' => $product['price'],
            'total_price' => $product['price']
        ];

        $this->calculateTotals();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Product added to invoice'
        ]);
    }

    public function removeProduct($index)
    {
        if (isset($this->productRows[$index])) {
            $productName = $this->productRows[$index]['name'];
            array_splice($this->productRows, $index, 1);
            $this->calculateTotals();
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => "Removed {$productName} from invoice"
            ]);
        }
    }

    public function removeAllProducts()
    {
        $this->productRows = [];
        $this->calculateTotals();

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'All products have been removed from the invoice'
        ]);
    }

    public function updateQuantity($index, $value)
    {
        $this->productRows[$index]['quantity'] = max(1, (int)$value);
        $this->updateRowTotal($index);
    }

    public function updateUnitPrice($index, $value)
    {
        $this->productRows[$index]['unit_price'] = max(0, (float)$value);
        $this->updateRowTotal($index);
    }

    protected function updateRowTotal($index)
    {
        if (isset($this->productRows[$index])) {
            $row = $this->productRows[$index];
            $quantity = $row['quantity'];
            $unitPrice = $row['unit_price'];
            $this->productRows[$index]['total_price'] = $quantity * $unitPrice;
            $this->calculateTotals();
        }
    }

    public function calculateTotals()
    {
        $this->totalQuantity = 0;
        $this->totalPrice = 0;

        foreach ($this->productRows as $row) {
            $this->totalQuantity += $row['quantity'];
            $this->totalPrice += $row['total_price'];
        }
    }

    public function render()
    {
        return view('livewire.invoice-product-table');
    }
}
