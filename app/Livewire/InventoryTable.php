<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\StockMovement;
use App\ViewModels\InventoryViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
    public $startDate = '';
    public $endDate = '';

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection', 'page'];

    public function mount($selectedOutletId)
    {
        $this->selectedOutletId = $selectedOutletId;
        $this->startDate = now()->startOfDay()->toDateString();
        $this->endDate = now()->endOfDay()->toDateString();
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

    public function resetFilters()
    {
        $this->reset([
            'search',
            'startDate',
            'endDate',
            'sortField',
            'sortDirection',
            'page',
        ]);

        $this->startDate = now()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        $query = Product::with([
            'category',
            'unit',
            'stockMovements' => function ($q) use ($startDate, $endDate) {
                if ($this->selectedOutletId && $this->selectedOutletId !== 'all') {
                    $q->where('outlet_id', $this->selectedOutletId);
                }
                $q->whereBetween('created_at', [$startDate, $endDate]);
            },
            'initialStockPerOutlet' => function ($q) use ($startDate) {
                $q->select('product_id', 'outlet_id', DB::raw("
                    SUM(
                        CASE 
                            WHEN movement_type = 'purchase' THEN quantity
                            WHEN movement_type = 'sale' THEN -quantity
                            ELSE quantity
                        END
                    ) AS quantity
                "))
                    ->where('created_at', '<', $startDate)
                    ->groupBy('product_id', 'outlet_id');
            }
        ])->addSelect([
            'initial_quantity' => StockMovement::select(DB::raw("
                SUM(
                    CASE 
                        WHEN movement_type = 'purchase' THEN quantity
                        WHEN movement_type = 'sale' THEN -quantity
                        ELSE quantity
                    END
                )
            "))
                ->whereColumn('product_id', 'products.id')
                ->where('created_at', '<', $startDate)
                ->when($this->selectedOutletId && $this->selectedOutletId !== 'all', function ($q) {
                    $q->where('outlet_id', $this->selectedOutletId);
                }),
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
