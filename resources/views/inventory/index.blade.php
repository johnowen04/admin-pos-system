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
                        </div>
                    </div>
                    <div class="card-body">
                        @if (!$hasInventoryData)
                            <div class="empty-state text-center py-5">
                                <div class="empty-state-icon">
                                    <i class="fa fa-boxes fa-3x text-muted"></i>
                                </div>
                                <h4 class="mt-4">No Inventory Data Available</h4>
                                <p class="text-muted">
                                    There are no inventory records in the system.
                                    <br>Begin by adding initial stock or creating purchase/sales invoices.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('inventory.create') }}" class="btn btn-primary me-2">
                                        <i class="fa fa-plus me-1"></i> Add Initial Stock
                                    </a>
                                    <a href="{{ route('purchase.create') }}" class="btn btn-outline-primary me-2">
                                        <i class="fa fa-tag me-1"></i> Create Purchase
                                    </a>
                                    <a href="{{ route('pos.index') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-shopping-cart me-1"></i> Go To POS
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="inventory-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Name</th>
                                            <th hidden>Category</th>
                                            <th>Initial</th>
                                            <th>Purchased</th>
                                            <th>Sold</th>
                                            <th>Adjustment</th>
                                            <th>End</th>
                                            <th>Unit</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td>{{ $product->sku }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td hidden>{{ $product->category->name }}</td>
                                                <td>{{ array_sum(array_map(fn($items) => array_sum(array_column($items, 'quantity')), $groupedGlobal[$product->id]['initial'] ?? [])) }}
                                                </td>
                                                <td>{{ array_sum(array_map(fn($items) => array_sum(array_column($items, 'quantity')), $groupedGlobal[$product->id]['purchase'] ?? [])) }}
                                                </td>
                                                <td>{{ array_sum(array_map(fn($items) => array_sum(array_column($items, 'quantity')), $groupedGlobal[$product->id]['sale'] ?? [])) }}
                                                </td>
                                                <td>{{ array_sum(array_map(fn($items) => array_sum(array_column($items, 'quantity')), $groupedGlobal[$product->id]['adjustment'] ?? [])) }}
                                                </td>
                                                <td>{{ array_sum(
                                                    array_map(fn($items) => array_sum(array_column($items, 'quantity')), $groupedGlobal[$product->id]['initial'] ?? []),
                                                ) +
                                                    array_sum(
                                                        array_map(
                                                            fn($items) => array_sum(array_column($items, 'quantity')),
                                                            $groupedGlobal[$product->id]['purchase'] ?? [],
                                                        ),
                                                    ) -
                                                    array_sum(
                                                        array_map(
                                                            fn($items) => array_sum(array_column($items, 'quantity')),
                                                            $groupedGlobal[$product->id]['sale'] ?? [],
                                                        ),
                                                    ) +
                                                    array_sum(
                                                        array_map(
                                                            fn($items) => array_sum(array_column($items, 'quantity')),
                                                            $groupedGlobal[$product->id]['adjustment'] ?? [],
                                                        ),
                                                    ) }}

                                                </td>
                                                <td>{{ $product->unit->name }}</td>

                                                <td>
                                                    <div class="form-button-action">
                                                        <button class="btn btn-link btn-lg view-details-btn"
                                                            data-product="{{ json_encode($product) }}"
                                                            data-stock="{{ json_encode($groupedDetail[$product->id]) }}">
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
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
                                    <th>Initial</th>
                                    <th>Purchased</th>
                                    <th>Sold</th>
                                    <th>Returned</th>
                                    <th>Refunded</th>
                                    <th>End</th>
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

            // Use event delegation to handle dynamically loaded buttons
            $(document).on('click', '.view-details-btn', function() {
                const button = $(this);
                const product = JSON.parse(button.attr('data-product'));
                const stockDetails = JSON.parse(button.attr('data-stock'));

                // Populate product details
                $('#modalProductSKU').text(product.sku);
                $('#modalProductName').text(product.name);
                $('#modalProductCategory').text(product.category.name);

                // Populate stock table
                const stockTableBody = $('#modalStockTableBody');

                function getInitialQuantity(movementsArray) {
                    if (movementsArray.length > 0) {
                        return parseFloat(movementsArray[0].quantity);
                    }
                    return 0;
                }

                function getPurchasedOrSoldQuantity(movementsArray) {
                    if (movementsArray.length > 0) {
                        return movementsArray.reduce((total, item) => total + parseFloat(item.quantity), 0);
                    }
                    return 0;
                }

                function getReturnedQuantity(movementsArray) {
                    if (movementsArray.length === 0) return 0;

                    // Convert negative values to positive for display purposes
                    return Math.abs(movementsArray
                        .filter(item => parseFloat(item.quantity) < 0)
                        .reduce((total, item) => total + parseFloat(item.quantity), 0));
                }

                function getRefundedQuantity(movementsArray) {
                    if (movementsArray.length === 0) return 0;

                    return movementsArray
                        .filter(item => parseFloat(item.quantity) > 0)
                        .reduce((total, item) => total + parseFloat(item.quantity), 0);
                }

                function getEndQuantity(initialArray, purchasedArray, soldArray, adjustmentArray) {
                    return getInitialQuantity(initialArray) +
                        getPurchasedOrSoldQuantity(purchasedArray) -
                        getPurchasedOrSoldQuantity(soldArray) -
                        getReturnedQuantity(adjustmentArray) +
                        getRefundedQuantity(adjustmentArray);
                }

                stockTableBody.empty(); // Clear previous rows
                if (Object.keys(stockDetails).length === 0) {
                    // Show "No inventories available" message if stockDetails is empty
                    stockTableBody.append(`
                <tr>
                    <td colspan="6" class="text-center">No inventories available</td>
                </tr>
            `);
                } else {
                    // Populate stock rows
                    Object.entries(stockDetails).forEach(([outletId, outletData]) => {
                        stockTableBody.append(`
                    <tr>
                        <td>${outletData.name}</td>
                        <td>${getInitialQuantity(outletData.initial)}</td>
                        <td>${getPurchasedOrSoldQuantity(outletData.purchase || [])}</td>
                        <td>${getPurchasedOrSoldQuantity(outletData.sale || [])}</td>
                        <td>${getReturnedQuantity(outletData.adjustment || [])}</td>
                        <td>${getRefundedQuantity(outletData.adjustment || [])}</td>
                        <td>${getEndQuantity(outletData.initial, outletData.purchase || [], outletData.sale || [], outletData.adjustment || [])}</td>
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
