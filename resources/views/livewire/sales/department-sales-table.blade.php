<div>
    @if ($report->rows()->isEmpty())
        <div class="empty-state text-center py-5">
            <div class="empty-state-icon">
                <i class="fa fa-chart-bar fa-3x text-muted"></i>
            </div>
            <h4 class="mt-4">No Sales Data Available</h4>
            <p class="text-muted">
                There are no department sales records to display in the selected period.
                <br>Try selecting a different date range or create some sales first.
            </p>
            <div class="mt-3">
                <a href="{{ route('pos.index') }}" class="btn btn-primary me-2">
                    <i class="fa fa-plus me-1"></i> Create Sales
                </a>
                @if ($search || $startDate || $endDate)
                    <button type="button" class="btn btn-secondary me-2" wire:click="resetFilters">
                        <i class="fa fa-filter me-1"></i> Reset Filters
                    </button>
                @endif
            </div>
        </div>
    @else
        <div class="row mb-3 g-0">
            <div class="col-12 col-sm-4 col-md-auto" style="width: 150px;">
                <label for="departmentPerPage" class="form-label mb-1 fw-bold">Items per page</label>
                <select id="departmentPerPage" wire:model="perPage" wire:change="resetPage"
                    class="form-control form-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            <div class="col-12 col-sm-8 col-md">
                <label for="departmentSearch" class="form-label mb-1 fw-bold">Search Department</label>
                <input type="text" id="departmentSearch" wire:model="search" wire:keyup='resetPage'
                    placeholder="Search department..." class="form-control" />
            </div>

            <div class="col-12 col-sm-6 col-md-auto" style="min-width: 180px;">
                <label for="startDate" class="form-label mb-1 fw-bold">Start Date</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" id="startDate" wire:model="startDate" wire:change="resetPage"
                        class="form-control" placeholder="Start date">
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-auto" style="min-width: 180px;">
                <label for="endDate" class="form-label mb-1 fw-bold">End Date</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" id="endDate" wire:model="endDate" wire:change="resetPage"
                        class="form-control" placeholder="End date">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th wire:click="sortBy('d.name')" style="cursor: pointer">
                            Department @if ($sortField === 'd.name')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('sold_quantity')" style="cursor: pointer">
                            Quantity Sold @if ($sortField === 'sold_quantity')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('percentage_qty')" style="cursor: pointer">
                            Total Qty (%) @if ($sortField === 'percentage_qty')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('total_sold')" style="cursor: pointer">
                            Total Sold (Rp) @if ($sortField === 'total_sold')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('percentage_revenue')" style="cursor: pointer">
                            Total Sold (%) @if ($sortField === 'percentage_revenue')
                                <i class="fa fa-sort-{{ $sortDirection }}"></i>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report->rows() as $department)
                        <tr>
                            <td>{{ $department['name'] }}</td>
                            <td>{{ $department['sold_quantity'] }}</td>
                            <td>Rp{{ $department['total_sold'] }}</td>
                            <td>{{ $department['percentage_qty'] }}%</td>
                            <td>{{ $department['percentage_revenue'] }}%</td>
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
