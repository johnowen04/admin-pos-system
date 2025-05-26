<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Reports\SalesReportService;
use App\ViewModels\DepartmentSalesReportViewModel;
use Carbon\Carbon;

class DepartmentSalesTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'd.name';
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

        $salesReportService = app(SalesReportService::class);

        $query = $salesReportService->getDepartmentSalesReportQuery($startDate, $endDate);

        if (!$this->selectedOutletId || $this->selectedOutletId === 'all') {
            $query = $salesReportService->getDepartmentSalesReportQuery($startDate, $endDate, $this->selectedOutletId);
        }

        $reportPaginator = $query->orderBy($this->sortField, $this->sortDirection == "up" ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $reportCollection = $reportPaginator->getCollection();

        $viewModel = new DepartmentSalesReportViewModel($reportCollection);

        return view('livewire.sales.department-sales-table', [
            'report' => $viewModel,
            'reportPaginator' => $reportPaginator,
        ]);
    }
}
