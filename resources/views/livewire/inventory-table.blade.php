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
                <a href="{{ route('pos.index') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-shopping-cart me-1"></i> Go To POS
                </a>
                @if ($search || $startDate || $endDate)
                    <button type="button" class="btn btn-secondary me-2" wire:click="resetFilters">
                        <i class="fa fa-filter me-1"></i> Reset Filters
                    </button>
                @endif
            </div>
        </div>
    @else
        <div class="d-flex row mb-3 gap-0">
            <div class="col-12 col-sm-4 col-md-auto mb-2" style="width: 150px;">
                <label for="productPerPage" class="form-label mb-1 fw-bold">Items per page</label>
                <select id="productPerPage" wire:model="perPage" wire:change="resetPage"
                    class="form-control form-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            <div class="col-12 col-sm-8 col-md mb-2">
                <label for="productSearch" class="form-label mb-1 fw-bold">Search Products</label>
                <input type="text" id="productSearch" wire:model="search" wire:keyup='resetPage'
                    placeholder="Search products..." class="form-control" />
            </div>

            <div class="col-12 col-sm-5 col-md-auto mb-2" style="min-width: 180px;">
                <label for="startDate" class="form-label mb-1 fw-bold">Start Date</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" id="startDate" wire:model="startDate" wire:change="resetPage"
                        class="form-control" placeholder="Start date">
                </div>
            </div>

            <div class="col-12 col-sm-5 col-md-auto mb-2" style="min-width: 180px;">
                <label for="endDate" class="form-label mb-1 fw-bold">End Date</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" id="endDate" wire:model="endDate" wire:change="resetPage"
                        class="form-control" placeholder="End date">
                </div>
            </div>
            <div class="col-auto col-sm-1 d-flex align-items-end mb-2" style="margin-bottom: 1px;">
                <button title="Reset Filter" type="button" class="btn btn-outline-danger" wire:click="resetFilters">
                    <i class="fa fa-filter me-1"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="display table table-striped table-hover" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th wire:click="sortBy('p.id')" style="cursor: pointer; width: 80px;">
                            ID @if ($sortField === 'p.id')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('p.sku')" style="cursor: pointer; width: 100px;">
                            SKU @if ($sortField === 'p.sku')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('p.name')" style="cursor: pointer; width: 200px;">
                            Name @if ($sortField === 'p.name')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('c.name')" style="cursor: pointer; width: 150px;">
                            Category @if ($sortField === 'c.name')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th title="Initial" class="text-truncate" style="text-align: right;">Initial</th>
                        <th title="Purchased" class="text-truncate" style="text-align: right;">Purchased</th>
                        <th title="Sold" class="text-truncate" style="text-align: right;">Sold</th>
                        <th title="Adjustment" class="text-truncate" style="text-align: right;">Adjustment</th>
                        <th title="Balance" wire:click="sortBy('balance')"
                            style="cursor: pointer; text-align: right;" class="text-truncate">
                            Balance @if ($sortField === 'balance')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventory->rows() as $product)
                        <tr>
                            <td class="text-center">{{ $product['id'] }}</td>
                            <td class="text-truncate" title="{{ $product['sku'] }}">{{ $product['sku'] }}</td>
                            <td class="text-truncate" title="{{ $product['name'] }}">{{ $product['name'] }}</td>
                            <td class="text-truncate" title="{{ $product['category'] }}">{{ $product['category'] }}
                            </td>
                            <td class="text-end" title="{{ $product['initial'] }}">{{ $product['initial'] }}</td>
                            <td class="text-end" title="{{ $product['purchase'] }}">{{ $product['purchase'] }}</td>
                            <td class="text-end" title="{{ $product['sale'] }}">{{ $product['sale'] }}</td>
                            <td class="text-end" title="{{ $product['adjustment'] }}">{{ $product['adjustment'] }}
                            </td>
                            <td class="text-end" title="{{ $product['balance'] }}">{{ $product['balance'] }}</td>
                            <td title="{{ $product['unit'] }}">{{ $product['unit'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No results found</td>
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
