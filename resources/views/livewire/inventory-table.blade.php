<div>
    @if ($inventory->rows()->isEmpty())
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
        <div class="d-flex flex-row mb-3 gap-2 align-items-center">
            <div class="flex-grow-1">
                <label for="productSearch" class="form-label fw-bold">Search Products</label>
                <input type="text" id="productSearch" wire:model="search" wire:keyup='resetPage'
                    placeholder="Search products..." class="form-control" />
            </div>

            <div class="d-flex flex-column" style="min-width: 150px;">
                <label for="productPerPage" class="form-label fw-bold">Items per page</label>
                <select id="productPerPage" wire:model="perPage" wire:change="resetPage" class="form-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="display table table-striped table-hover">
                <thead>
                    <tr>
                        <th wire:click="sortBy('id')" style="cursor: pointer">
                            ID @if ($sortField === 'id')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('sku')" style="cursor: pointer">
                            SKU @if ($sortField === 'sku')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('name')" style="cursor: pointer">
                            Name @if ($sortField === 'name')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th>Category</th>
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
                    @forelse ($inventory->rows() as $product)
                        <tr>
                            <td>{{ $product['id'] }}</td>
                            <td>{{ $product['sku'] }}</td>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['category'] }}</td>
                            <td>{{ $product['initial'] }}</td>
                            <td>{{ $product['purchase'] }}</td>
                            <td>{{ $product['sale'] }}</td>
                            <td>{{ $product['adjustment'] }}</td>
                            <td>{{ $product['balance'] }}</td>
                            <td>{{ $product['unit'] }}</td>
                            <td>
                                <div class="form-button-action">
                                    <button class="btn btn-link btn-lg view-details-btn"
                                        data-product='@json($product)'
                                        data-stock='@json($product['stock'])'>
                                        <i class="fas fa-eye"></i>
                                    </button>
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
                {{ $productsPaginator->links() }}
            </div>
        </div>
    @endif
</div>
