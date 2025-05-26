<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Carbon\Carbon;

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

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection', 'page'];

    public function mount($selectedOutletId, $invoiceType)
    {
        $this->selectedOutletId = $selectedOutletId;
        $this->invoiceType = $invoiceType;
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

        if ($this->invoiceType === 'Purchase') {
            return $this->renderPurchaseInvoices($startDate, $endDate);
        } elseif ($this->invoiceType === 'Sales') {
            return $this->renderSalesInvoices($startDate, $endDate);
        }
    }

    protected function renderPurchaseInvoices($startDate, $endDate)
    {
        $query = PurchaseInvoice::with([
            'employee',
            'products',
            'outlet' => function ($q) {
                if ($this->selectedOutletId && $this->selectedOutletId !== 'all') {
                    $q->where('id', $this->selectedOutletId);
                }
            }
        ]);

        $query->whereBetween('created_at', [$startDate, $endDate]);

        if ($this->selectedOutletId && $this->selectedOutletId !== 'all') {
            $query->where('outlet_id', $this->selectedOutletId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            });
        }

        $invoices = $query->orderBy($this->sortField, $this->sortDirection == "up" ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.invoice-table', ['invoices' => $invoices]);
    }

    protected function renderSalesInvoices($startDate, $endDate)
    {
        $query = SalesInvoice::with([
            'employee',
            'products',
            'outlet' => function ($q) {
                if ($this->selectedOutletId && $this->selectedOutletId !== 'all') {
                    $q->where('id', $this->selectedOutletId);
                }
            }
        ]);
        
        $query->whereBetween('created_at', [$startDate, $endDate]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            });
        }

        $invoices = $query->orderBy($this->sortField, $this->sortDirection == "up" ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.invoice-table', ['invoices' => $invoices]);
    }
}
