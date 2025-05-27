<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\ViewModels\InventoryViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'p.id';
    public $sortDirection = 'up';
    public $page = 1;
    public $perPage = 10;
    public $selectedOutletId;
    public $startDate = '';
    public $endDate = '';

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection', 'page', 'startDate', 'endDate'];

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
        $this->reset(['search', 'sortField', 'sortDirection', 'page']);
        $this->startDate = now()->startOfDay()->toDateString();
        $this->endDate = now()->endOfDay()->toDateString();
    }

    public function render()
    {
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        $initialStock = DB::table('stock_movements')
            ->selectRaw(
                'product_id,SUM(CASE
                    WHEN movement_type = "purchase" THEN quantity 
                    WHEN movement_type = "sale" THEN -quantity 
                    ELSE quantity END) AS initial_quantity'
            );

        if ($startDate) {
            $initialStock->where('created_at', '<', $startDate);
        }
        if ($this->selectedOutletId && $this->selectedOutletId != 'all') {
            $initialStock->where('outlet_id', $this->selectedOutletId);
        }

        $initialStock->groupBy('product_id');

        $movementTotals = DB::table('stock_movements')
            ->selectRaw(
                'product_id,
                SUM(CASE WHEN movement_type = "purchase" THEN quantity ELSE 0 END) AS purchase,
                SUM(CASE WHEN movement_type = "sale" THEN quantity ELSE 0 END) AS sale,
                SUM(CASE WHEN movement_type = "adjustment" THEN quantity ELSE 0 END) AS adjustment'
            );

        if ($startDate && $endDate) {
            $movementTotals->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $movementTotals->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $movementTotals->where('created_at', '<=', $endDate);
        }

        if ($this->selectedOutletId && $this->selectedOutletId != 'all') {
            $movementTotals->where('outlet_id', $this->selectedOutletId);
        }

        $movementTotals->groupBy('product_id');

        $query = DB::table('products as p')
            ->leftJoinSub($initialStock, 'i', 'p.id', '=', 'i.product_id')
            ->leftJoinSub($movementTotals, 'm', 'p.id', '=', 'm.product_id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftJoin('units as u', 'p.unit_id', '=', 'u.id')
            ->select([
                'p.id',
                'p.name',
                'p.sku',
                'c.name as category',
                'u.name as unit',
                DB::raw('COALESCE(i.initial_quantity, 0) AS initial_quantity'),
                DB::raw('COALESCE(m.purchase, 0) AS purchase'),
                DB::raw('COALESCE(m.sale, 0) AS sale'),
                DB::raw('COALESCE(m.adjustment, 0) AS adjustment'),
                DB::raw('(COALESCE(i.initial_quantity, 0) + COALESCE(m.purchase, 0) - COALESCE(m.sale, 0) + COALESCE(m.adjustment, 0)) AS balance'),
            ]);

        if ($this->search) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('p.id', 'like', "%{$search}%")
                    ->orWhere('p.name', 'like', "%{$search}%")
                    ->orWhere('p.sku', 'like', "%{$search}%");
            });
        }

        $productPaginator = $query
            ->orderBy(
                $this->sortField,
                $this->sortDirection === 'up' ? 'asc' : 'desc'
            )
            ->paginate($this->perPage);

        $viewModel = new InventoryViewModel($productPaginator->getCollection() ?? collect([]));

        return view('livewire.inventory-table', [
            'inventory' => $viewModel,
            'productsPaginator' => $productPaginator,
        ]);
    }
}
