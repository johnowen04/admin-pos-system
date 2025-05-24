<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductTable extends Component
{
    public $method;
    public $products;
    public $invoice;
    public $invoiceType;
    
    /**
     * Create a new component instance.
     */
    public function __construct($method = "POST", $products = [], $invoice, $invoiceType)
    {
        $this->method = $method;
        $this->products = $products;
        $this->invoice = $invoice;
        $this->invoiceType = $invoiceType;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-table');
    }
}
