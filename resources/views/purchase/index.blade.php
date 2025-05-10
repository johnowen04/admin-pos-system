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
                                onclick="window.location='{{ route('purchase.create') }}'">
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchases as $purchase)
                                        <tr>
                                            <td>{{ $purchase->id }}</td>
                                            <td>{{ $purchase->created_at }}</td>
                                            <td>{{ $purchase->invoice_number }}</td>
                                            <td>{{ $purchase->grand_total }}</td>
                                            <td>{{ $purchase->outlet->name }}</td>
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
