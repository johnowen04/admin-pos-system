<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Product')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Product</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Product List</h4>
                            <div class="d-flex gap-1 ms-auto">
                                <button type="button" class="btn btn-secondary btn-round" data-bs-toggle="modal"
                                    data-bs-target="#exportModal">
                                    <i class="fa fa-plus"></i>
                                    Import Product
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold" id="exportModalLabel">Import Product From
                                                    Excel</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <!-- Modal Body -->
                                            <div class="modal-body">
                                                <!-- Import Form -->
                                                <form action="{{ route('import.products') }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="file" class="form-label fw-semibold">Select
                                                            File</label>
                                                        <input type="file" class="form-control" id="file"
                                                            name="file" required>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-primary">Import</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button class="btn btn-primary btn-round ms-auto"
                                    onclick="window.location='{{ route('product.create') }}'">
                                    <i class="fa fa-plus"></i>
                                    Add Product
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="product-table" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>SKU</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Base Price</th>
                                        <th>Buy Price</th>
                                        <th>Sell Price</th>
                                        <th>Shown in menu?</th>
                                        <th style="width: 10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>Rp{{ $product->base_price }}</td>
                                            <td>Rp{{ $product->buy_price }}</td>
                                            <td>Rp{{ $product->sell_price }}</td>
                                            <td>
                                                @if ($product->is_shown)
                                                    <span class="badge bg-success text-white">Active</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-button-action">
                                                    <a href="{{ route('product.edit', $product->id) }}"
                                                        class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('product.destroy', $product->id) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger"
                                                            data-toggle="tooltip" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this product?')">
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
            $('#product-table').DataTable({
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
