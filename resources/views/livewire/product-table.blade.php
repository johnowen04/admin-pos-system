<div>
    <div class="d-flex mb-3">
        <input type="text" wire:model="search" wire:keyup='resetPage' placeholder="Search products..."
            class="form-control me-2" />
    </div>

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
                            <!-- Action buttons -->
                            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-sm btn-primary">Edit</a>
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
            {!! $products->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>
