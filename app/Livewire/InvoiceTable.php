<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class InvoiceTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'up';
    public $page = 1;
    public $perPage = 10;
    public $selectedOutletId;
    public $invoiceType;
    public $startDate = '';
    public $endDate = '';

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection', 'page', 'startDate', 'endDate'];

    public function mount($selectedOutletId, $invoiceType)
    {
        $this->selectedOutletId = $selectedOutletId;
        $this->invoiceType = $invoiceType;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        $this->sortDirection = ($this->sortField === $field) 
            ? ($this->sortDirection === 'up' ? 'down' : 'up')
            : 'up';
        $this->sortField = $field;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'startDate', 'endDate', 'sortField', 'sortDirection', 'page']);
    }

    public function render()
    {
        $invoices = $this->getInvoices();
        return view('livewire.invoice-table', ['invoices' => $invoices]);
    }

    protected function getInvoices()
    {
        $model = $this->invoiceType === 'Purchase' ? PurchaseInvoice::class : SalesInvoice::class;
        
        $query = $model::with(['employee', 'products', 'outlet']);
        
        $this->applyDateFilters($query);
        
        if ($this->selectedOutletId && $this->selectedOutletId !== 'all') {
            $query->where('outlet_id', $this->selectedOutletId);
        }
        
        if ($this->search) {
            $query->where(function (Builder $q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->orderBy(
            $this->sortField, 
            $this->sortDirection === 'up' ? 'asc' : 'desc'
        )->paginate($this->perPage);
    }
    
    protected function applyDateFilters(Builder $query)
    {
        if (!empty($this->startDate) && !empty($this->endDate)) {
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query;
    }
}
