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
                                        <th>Units</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>{{ $product->unit->name }}</td>

                                            <td>
                                                <div class="form-button-action">
                                                    <button class="btn btn-link btn-lg view-details-btn"
                                                        data-product="{{ json_encode($product) }}"
                                                        data-stock="{{ json_encode($product->stocks) }}">
                                                        <i class="fas fa-boxes"></i>
                                                    </button>
                                                    <a href="{{ route('inventory.edit', $product->id) }}"
                                                        class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('inventory.destroy', $product->id) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger"
                                                            data-toggle="tooltip" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this inventory?')">
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

    <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productDetailsModalLabel">Stock All Outlet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Product Information</h5>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-end" style="width: 30%;">SKU:</th>
                                <td class="text-start"><span id="modalProductSKU"></span></td>
                            </tr>
                            <tr>
                                <th class="text-end">Name:</th>
                                <td class="text-start"><span id="modalProductName"></span></td>
                            </tr>
                            <tr>
                                <th class="text-end">Category:</th>
                                <td class="text-start"><span id="modalProductCategory"></span></td>
                            </tr>
                        </tbody>
                    </table>

                    <h5>Stock Information</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Outlet</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody id="modalStockTableBody">
                                <!-- Stock rows will be dynamically added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } // Disable sorting on the Action column
                ]
            });

            // Handle row click to show modal
            $('.view-details-btn').on('click', function() {
                const product = $(this).data('product');
                const stockDetails = $(this).data('stock');

                // Populate product details
                $('#modalProductSKU').text(product.sku);
                $('#modalProductName').text(product.name);
                $('#modalProductCategory').text(product.category.name);

                // Populate stock table
                const stockTableBody = $('#modalStockTableBody');
                stockTableBody.empty(); // Clear previous rows
                if (stockDetails.length === 0) {
                    // Show "No inventories available" message if stockDetails is empty
                    stockTableBody.append(`
                        <tr>
                            <td colspan="3" class="text-center">No inventories available</td>
                        </tr>
                    `);
                } else {
                    // Populate stock rows
                    stockDetails.forEach(stock => {
                        stockTableBody.append(`
                            <tr>
                                <td>${stock.outlet_name}</td>
                                <td>${stock.quantity}</td>
                                <td>${product.unit.name}</td>
                            </tr>
                        `);
                    });
                }

                // Show the modal
                $('#productDetailsModal').modal('show');
            });
        });
    </script>
@endpush

</html>
