<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Inventory</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Inventory List</h4>
                            {{-- <button class="btn btn-primary btn-round ms-auto"
                                onclick="window.location='{{ route('product.create') }}'">
                                <i class="fa fa-plus"></i>
                                Add Product
                            </button> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="inventory-table" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Initial Stock</th>
                                        <th>Bought</th>
                                        <th>Sold</th>
                                        <th>End Stock</th>
                                        <th>Units</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>Rp{{ $product->base_price }}</td>
                                            <td>Rp{{ $product->buy_price }}</td>
                                            <td>Rp{{ $product->buy_price }}</td>
                                            <td>Rp{{ $product->buy_price }}</td>
                                            <td>{{ $product->unit->name }}</td>
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
            $('#inventory-table').DataTable({
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
