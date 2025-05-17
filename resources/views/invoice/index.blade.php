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
                            <button class="btn btn-primary btn-round ms-auto"
                                onclick="window.location=@if ($invoiceType === 'Purchase') '{{ route('purchase.create') }}' @else '{{ route('sales.create') }}' @endif">
                                <i class="fa fa-plus"></i>
                                Add {{ $invoiceType }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
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
                                                            class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                                            title="Receipt">
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
