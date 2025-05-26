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
                        <div id="inventory-table" wire:key="inventory-table">
                            @livewire('inventory-table', ['selectedOutletId' => $selectedOutletId])
                        </div>
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
            $('.view-details-btn').on('click', function() {
                const product = $(this).data('product');
                const stock = $(this).data('stock');

                $('#modalProductSKU').text(product.sku);
                $('#modalProductName').text(product.name);
                $('#modalProductCategory').text(product.category);

                const $tbody = $('#modalStockTableBody');
                $tbody.empty();

                $.each(stock, function(_, outlet) {
                    const end = outlet.initial + outlet.purchase - outlet.sale + (outlet
                        .adjustment ?? 0);

                    const $row = $('<tr>').append(
                        $('<td>').text(outlet.name),
                        $('<td>').text(outlet.initial),
                        $('<td>').text(outlet.purchase),
                        $('<td>').text(outlet.sale),
                        $('<td>').text(outlet.return ?? 0),
                        $('<td>').text(outlet.refund ?? 0),
                        $('<td>').text(end),
                        $('<td>').text(outlet.unit)
                    );

                    $tbody.append($row);
                });
                $('#productDetailsModal').modal('show');
            });
        });
    </script>
@endpush

</html>
