<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\ViewModels\InventoryViewModel;

class InventoryTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'up';
    public $page = 1;
    public $perPage = 10;
    public $selectedOutletId;

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection', 'page'];

    public function mount($selectedOutletId)
    {
        $this->selectedOutletId = $selectedOutletId;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'up' ? 'down' : 'up';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'up';
        }
    }

    public function render()
    {
        $query = Product::with([
            'category',
            'unit',
            'stockMovements' => function ($q) {
                if ($this->selectedOutletId && $this->selectedOutletId !== 'all') {
                    $q->where('outlet_id', $this->selectedOutletId);
                }
            }
        ]);

        if ($this->selectedOutletId && $this->selectedOutletId !== 'all') {
            $query->whereHas('stockMovements', function ($q) {
                $q->where('outlet_id', $this->selectedOutletId);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        $productsPaginator = $query->orderBy($this->sortField, $this->sortDirection == "up" ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $productsCollection = $productsPaginator->getCollection();

        $viewModel = new InventoryViewModel($productsCollection, 'all');

        return view('livewire.inventory-table', [
            'inventory' => $viewModel,
            'productsPaginator' => $productsPaginator,
        ]);
    }
}
