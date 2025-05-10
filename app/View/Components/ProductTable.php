<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductTable extends Component
{
    public $products;
    public $invoice;
    
    /**
     * Create a new component instance.
     */
    public function __construct($products = [], $invoice)
    {
        $this->products = $products;
        $this->invoice = $invoice;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-table');
    }
}
