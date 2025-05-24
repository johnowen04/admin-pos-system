<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@if ($invoiceType === 'Purchase')
    @section('title', 'Purchase Invoice')
@else
    @section('title', 'Sales Invoice')
@endif

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">{{ $invoiceType }}</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">{{ $invoiceType }} List</h4>
                            <div class="d-flex gap-1 ms-auto">
                                @if ($invoiceType === 'Sales')
                                    <!-- Button to trigger modal -->
                                    <button type="button" class="btn btn-secondary btn-round" data-bs-toggle="modal"
                                        data-bs-target="#exportModal">
                                        Export Product Sales Report
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="exportModal" tabindex="-1"
                                        aria-labelledby="exportModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold" id="exportModalLabel">Export Product
                                                        Sales Report</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <!-- Modal Body -->
                                                <div class="modal-body">
                                                    <!-- Export Form -->
                                                    <form action="{{ route('export.product-sales') }}" method="GET">
                                                        <div class="mb-3">
                                                            <label for="start_date" class="form-label fw-semibold">Start
                                                                Date</label>
                                                            <input type="date" class="form-control" id="start_date"
                                                                name="start_date" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="end_date" class="form-label fw-semibold">End
                                                                Date</label>
                                                            <input type="date" class="form-control" id="end_date"
                                                                name="end_date" required>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <button type="submit" class="btn btn-primary">Export</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <button class="btn btn-primary btn-round ms-auto"
                                    onclick="window.location=@if ($invoiceType === 'Purchase') '{{ route('purchase.create') }}' @else '{{ route('sales.create') }}' @endif">
                                    <i class="fa fa-plus"></i>
                                    Add {{ $invoiceType }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
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
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="invoice-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Invoice Number</th>
                                            <th>Grand Total</th>
                                            <th>Outlet</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoices as $invoice)
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
                                                                class="btn btn-link btn-primary btn-lg"
                                                                data-toggle="tooltip" title="Receipt">
                                                                <i class="fas fa-receipt"></i>
                                                            </a>
                                                        @endif
                                                        <a href="@if ($invoiceType === 'Purchase') {{ route('purchase.edit', $invoice->id) }} @else {{ route('sales.edit', $invoice->id) }} @endif"
                                                            class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form
                                                            action="@if ($invoiceType === 'Purchase') {{ route('purchase.destroy', $invoice->id) }} @else {{ route('sales.destroy', $invoice->id) }} @endif"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link btn-danger"
                                                                data-toggle="tooltip" title="Delete"
                                                                onclick="return confirm('Are you sure you want to delete this invoice?')">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#invoice-table').DataTable({
                "pageLength": 10,
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } // Disable sorting on the Action column
                ]
            });
        });
    </script>
@endpush

</html>
