<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Reports\SalesReportService;
use App\ViewModels\ProductSalesReportViewModel;
use Carbon\Carbon;

class ProductSalesTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'p.sku';
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
        $this->startDate = '';
        $this->endDate = '';
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

        $this->startDate = '';
        $this->endDate = '';
    }

    public function render()
    {
        $salesReportService = app(SalesReportService::class);

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();
            $query = $salesReportService->getProductSalesReportQuery($startDate, $endDate);
        } else {
            $query = $salesReportService->getProductSalesReportQuery();
        }

        if ($this->selectedOutletId && $this->selectedOutletId != 'all') {
            $query = $salesReportService->getProductSalesReportQuery(
                !empty($this->startDate) && !empty($this->endDate) ? $startDate : null,
                !empty($this->startDate) && !empty($this->endDate) ? $endDate : null,
                $this->selectedOutletId
            );
        }

        $reportPaginator = $query->orderBy($this->sortField, $this->sortDirection == "up" ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $reportCollection = $reportPaginator->getCollection();

        $viewModel = new ProductSalesReportViewModel($reportCollection);

        return view('livewire.sales.product-sales-table', [
            'report' => $viewModel,
            'reportPaginator' => $reportPaginator,
        ]);
    }
}
