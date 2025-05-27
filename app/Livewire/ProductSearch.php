<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductSearch extends Component
{
    public $search = "";
    public $outletId = null;
    public $searchResults = [];
    public $showResults = false;

    public function mount($outletId = null)
    {
        $this->outletId = $outletId;
    }

    #[On('outletChanged')]
    public function outletChanged($outletId)
    {
        $this->outletId = $outletId;
        $this->search = "";
        $this->searchResults = [];
        $this->showResults = false;
    }

    public function updatedSearch()
    {
        if (empty($this->search) || strlen($this->search) < 2) {
            $this->searchResults = [];
            $this->showResults = false;
            return;
        }

        $this->searchResults = Product::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('sku', 'like', '%' . $this->search . '%');
        })
            ->when($this->outletId, function ($query) {
                return $query->whereHas('inventories', function ($q) {
                    $q->where('outlet_id', $this->outletId);
                });
            })
            ->with(['category', 'inventories'])
            ->limit(10)
            ->get()
            ->map(function ($product) {
                $stock = 0;
                if ($this->outletId) {
                    $inventory = $product->inventories->where('outlet_id', $this->outletId)->first();
                    $stock = $inventory ? $inventory->quantity : 0;
                } else {
                    $stock = $product->inventories->sum('quantity');
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'stock' => $stock,
                    'price' => $product->sell_price
                ];
            })
            ->toArray();

        $this->showResults = count($this->searchResults) > 0;
    }

    public function hideResults()
    {
        $this->showResults = false;
    }

    public function selectProduct($productId)
    {
        $selectedProduct = collect($this->searchResults)->firstWhere('id', $productId);

        if ($selectedProduct) {
            $this->dispatch('productSelected', $selectedProduct);
            $this->hideResults();
            $this->search = '';
        }
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}
