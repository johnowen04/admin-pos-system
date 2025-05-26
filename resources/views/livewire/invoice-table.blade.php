<div>
    @if ($invoices->isEmpty())
        <div class="empty-state text-center py-5">
            <div class="empty-state-icon">
                <i
                    class="fa {{ $invoiceType === 'Purchase' ? 'fa-file-invoice-dollar' : 'fa-receipt' }} fa-3x text-muted"></i>
            </div>
            <h4 class="mt-4">No {{ $invoiceType }} Invoices Available</h4>
            <p class="text-muted">
                There are no {{ strtolower($invoiceType) }} invoices in the system yet.
                <br>Click the button below to create your first {{ strtolower($invoiceType) }} invoice.
            </p>
            <div class="mt-3">
                <a href="{{ $invoiceType === 'Purchase' ? route('purchase.create') : route('pos.index') }}"
                    class="btn btn-primary">
                    <i class="fa fa-plus me-1"></i>
                    @if ($invoiceType === 'Purchase')
                        Create Purchase Invoice
                    @else
                        Start Selling Products in POS
                    @endif
                </a>

                <button type="button" class="btn btn-secondary" wire:click="resetFilters">
                    <i class="fa fa-filter me-1"></i> Reset Filters
                </button>
            </div>
        </div>
    @else
        <div class="row mb-3 g-0">
            <div class="col-12 col-sm-4 col-md-auto" style="width: 150px;">
                <label for="invoicePerPage" class="form-label mb-1 fw-bold">Items per page</label>
                <select id="invoicePerPage" wire:model="perPage" wire:change="resetPage"
                    class="form-control form-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            <div class="col-12 col-sm-8 col-md">
                <label for="invoiceSearch" class="form-label mb-1 fw-bold">Search Invoice Number</label>
                <input type="text" id="invoiceSearch" wire:model="search" wire:keyup='resetPage'
                    placeholder="Search invoice number..." class="form-control" />
            </div>

            <div class="col-12 col-sm-6 col-md-auto" style="min-width: 180px;">
                <label for="startDate" class="form-label mb-1 fw-bold">Start Date</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" id="startDate" wire:model="startDate" wire:change="resetPage"
                        class="form-control" placeholder="Start date">
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-auto" style="min-width: 180px;">
                <label for="endDate" class="form-label mb-1 fw-bold">End Date</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" id="endDate" wire:model="endDate" wire:change="resetPage"
                        class="form-control" placeholder="End date">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th wire:click="sortBy('id')" style="cursor: pointer">
                            ID @if ($sortField === 'id')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('created_at')" style="cursor: pointer">
                            Created At @if ($sortField === 'created_at')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th>Invoice Number</th>
                        <th>Grand Total</th>
                        <th>Outlet</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->id }}</td>
                            <td>{{ $invoice->created_at }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->grand_total }}</td>
                            <td>{{ $invoice->outlet->name }}</td>
                            <td>
                                <div class="form-button-action">
                                    @if ($invoiceType === 'Sales')
                                        <a href="{{ route('pos.receipt', $invoice->id) }}"
                                            class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                            title="Receipt">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                    @endif
                                    <a href="@if ($invoiceType === 'Purchase') {{ route('purchase.edit', $invoice->id) }} @else {{ route('sales.edit', $invoice->id) }} @endif"
                                        class="btn btn-link btn-primary btn-lg" data-toggle="tooltip" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form
                                        action="@if ($invoiceType === 'Purchase') {{ route('purchase.destroy', $invoice->id) }} @else {{ route('sales.destroy', $invoice->id) }} @endif"
                                        method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-danger" data-toggle="tooltip"
                                            title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this invoice?')">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No results found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $invoices->links() }}
            </div>
        </div>
    @endif
</div>
