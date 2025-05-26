<div>
    @if ($report->rows()->isEmpty())
        <div class="empty-state text-center py-5">
            <div class="empty-state-icon">
                <i class="fa fa-chart-bar fa-3x text-muted"></i>
            </div>
            <h4 class="mt-4">No Sales Data Available</h4>
            <p class="text-muted">
                There are no product sales records to display in the selected period.
                <br>Try selecting a different date range or create some sales first.
            </p>
            <div class="mt-3">
                <a href="{{ route('pos.index') }}" class="btn btn-primary me-2">
                    <i class="fa fa-cash-register me-1"></i> Create Sales
                </a>
                <button type="button" class="btn btn-secondary" wire:click="resetFilters">
                    <i class="fa fa-filter me-1"></i> Reset Filters
                </button>
            </div>
        </div>
    @else
        <div class="d-flex flex-column flex-xl-row mb-3 gap-3 align-items-end">
            <div class="flex-grow-1 me-md-2">
                <label for="productSearch" class="form-label mb-1 fw-bold">Search Products</label>
                <input type="text" id="productSearch" wire:model="search" wire:keyup='resetPage'
                    placeholder="Search products..." class="form-control" />
            </div>

            <div class="me-md-2" style="min-width: 180px;">
                <label for="startDate" class="form-label mb-1 fw-bold">Start Date</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" id="startDate" wire:model="startDate" wire:change="resetPage"
                        class="form-control" placeholder="Start date">
                </div>
            </div>

            <div class="me-md-2" style="min-width: 180px;">
                <label for="endDate" class="form-label mb-1 fw-bold">End Date</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" id="endDate" wire:model="endDate" wire:change="resetPage"
                        class="form-control" placeholder="End date">
                </div>
            </div>

            <div style="width: 150px;">
                <label for="productPerPage" class="form-label mb-1 fw-bold">Items per page</label>
                <select id="productPerPage" wire:model="perPage" wire:change="resetPage" class="form-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th wire:click="sortBy('p.name')" style="cursor: pointer">
                            Product @if ($sortField === 'p.name')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('p.sku')" style="cursor: pointer">
                            SKU @if ($sortField === 'p.sku')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th>Category</th>
                        <th>Outlet</th>
                        <th wire:click="sortBy('sold_quantity')" style="cursor: pointer">
                            Quantity Sold @if ($sortField === 'sold_quantity')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('total_sold')" style="cursor: pointer">
                            Total Sold (Rp) @if ($sortField === 'total_sold')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('refund_quantity')" style="cursor: pointer">
                            Quantity Refund @if ($sortField === 'refund_quantity')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('total_refund')" style="cursor: pointer">
                            Total Refund (Rp) @if ($sortField === 'total_refund')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('percentage_qty')" style="cursor: pointer">
                            Total Qty (%) @if ($sortField === 'percentage_qty')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('percentage_revenue')" style="cursor: pointer">
                            Total Sold (%) @if ($sortField === 'percentage_revenue')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('total_sold_cogs')" style="cursor: pointer">
                            COGS (Rp) @if ($sortField === 'total_sold_cogs')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('total_refund_cogs')" style="cursor: pointer">
                            Refund COGS (Rp) @if ($sortField === 'total_refund_cogs')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('gross_profit')" style="cursor: pointer">
                            Gross Profit (Rp) @if ($sortField === 'gross_profit')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report->rows() as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['sku'] }}</td>
                            <td>{{ $product['category'] }}</td>
                            <td>{{ $product['outlet'] }}</td>
                            <td>{{ $product['sold_quantity'] }}</td>
                            <td>Rp{{ $product['total_sold'] }}</td>
                            <td>{{ $product['refund_quantity'] }}</td>
                            <td>Rp{{ $product['total_refund'] }}</td>
                            <td>{{ $product['percentage_qty'] }}%</td>
                            <td>{{ $product['percentage_revenue'] }}%</td>
                            <td>Rp{{ $product['total_sold_cogs'] }}</td>
                            <td>Rp{{ $product['total_refund_cogs'] }}</td>
                            <td>Rp{{ $product['gross_profit'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No results found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $reportPaginator->links() }}
            </div>
        </div>
    @endif
</div>
