<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Purchase')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Purchase</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Purchase List</h4>
                            <button class="btn btn-primary btn-round ms-auto"
                                onclick="window.location='{{ route('purchase-invoice.create') }}'">
                                <i class="fa fa-plus"></i>
                                Add Purchase
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="purchase-table" class="display table table-striped table-hover">
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
                                    @foreach ($purchaseInvoices as $purchaseInvoice)
                                        <tr>
                                            <td>{{ $purchaseInvoice->id }}</td>
                                            <td>{{ $purchaseInvoice->created_at }}</td>
                                            <td>{{ $purchaseInvoice->invoice_number }}</td>
                                            <td>{{ $purchaseInvoice->grand_total }}</td>
                                            <td>{{ $purchaseInvoice->outlet->name }}</td>
                                            <td>
                                                <div class="form-button-action">
                                                    <a href="{{ route('purchase-invoice.edit', $purchaseInvoice->id) }}"
                                                        class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('purchase-invoice.destroy', $purchaseInvoice->id) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger"
                                                            data-toggle="tooltip" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this purchase?')">
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
            $('#purchase-table').DataTable({
                "pageLength": 10,
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": -1 } // Disable sorting on the Action column
                ]
            });
        });
    </script>
@endpush

</html>
