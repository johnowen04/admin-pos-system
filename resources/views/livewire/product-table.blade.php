<div>
    <div class="row mb-3 g-0">
        <div class="col-12 col-sm-4 col-md-auto" style="width: 150px;">
            <label for="productPerPage" class="form-label mb-1 fw-bold">Items per page</label>
            <select id="productPerPage" wire:model="perPage" wire:change="resetPage" class="form-control form-select">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <div class="col-12 col-sm-8 col-md">
            <label for="productSearch" class="form-label mb-1 fw-bold">Search Products</label>
            <input type="text" id="productSearch" wire:model="search" wire:keyup='resetPage'
                placeholder="Search products..." class="form-control" />
        </div>

        <div class="col-12 col-sm-6 col-md-auto mb-3 mb-md-0 me-md-2">
            <label for="categoryFilter" class="form-label mb-1 fw-bold">Filter by Category</label>
            <select id="categoryFilter" wire:model="categoryFilter" wire:change="resetPage"
                class="form-control form-select">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-sm-6 col-md-auto mt-3 mt-md-0">
            <label class="form-label mb-1 fw-bold">Filter by Shown In Menu?</label>
            <div class="d-flex justify-content-center">
                <div class="btn-group w-100">
                    <button type="button" class="btn {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="setFilter('all')">
                        All
                    </button>
                    <button type="button" class="btn {{ $filter === 'shown' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="setFilter('shown')">
                        Shown
                    </button>
                    <button type="button"
                        class="btn {{ $filter === 'not_shown' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="setFilter('not_shown')">
                        Not Shown
                    </button>
                </div>
            </div>
        </div>

        <div class="col-auto col-sm-1 d-flex align-items-end mb-2" style="margin-bottom: 1px;">
            <button title="Reset Filter" type="button" class="btn btn-outline-danger" wire:click="resetFilters">
                <i class="fa fa-filter me-1"></i>
            </button>
        </div>
    </div>

    @if ($products->isEmpty())
        <div class="empty-state text-center py-5">
            <div class="empty-state-icon">
                <i class="fa fa-box-open fa-3x text-muted"></i>
            </div>
            <h4 class="mt-4">No Products Available</h4>
            <p class="text-muted">
                There are no products in the system yet.
                <br>Add your first product or import products from Excel.
            </p>
            <div class="mt-3">
                <a href="{{ route('product.create') }}" class="btn btn-primary me-2">
                    <i class="fa fa-plus me-1"></i> Add Product
                </a>
                <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal"
                    data-bs-target="#exportModal">
                    <i class="fa fa-file-import me-1"></i> Import from Excel
                </button>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
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
                        <th>Base Price</th>
                        <th>Buy Price</th>
                        <th>Sell Price</th>
                        <th>Shown</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>Rp{{ number_format($product->base_price, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($product->buy_price, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($product->sell_price, 0, ',', '.') }}</td>
                            <td>
                                @if ($product->is_shown)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a hidden href="{{ route('product.show', $product->id) }}"
                                        class="btn btn-link btn-primary btn-lg" data-toggle="tooltip" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('product.edit', $product->id) }}"
                                        class="btn btn-link btn-primary btn-lg" data-toggle="tooltip" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
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
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
