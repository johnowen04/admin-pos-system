<div class="form-group">
    @if ($method !== 'PUT' && count($productRows) > 0)
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-danger" wire:click="removeAllProducts"
                wire:confirm="Are you sure you want to remove all products from the invoice?">
                <i class="fas fa-trash me-1"></i> Remove All Products
            </button>
        </div>
    @endif

    <table class="table table-bordered table-responsive">
        <thead>
            <tr>
                @if ($method !== 'PUT')
                    <th style="width: 14%;" class="text-center">Actions</th>
                @endif
                <th style="width: 24%;">Product</th>
                <th style="width: 15%;" class="text-end">Base Price</th>
                <th style="width: 10%;" class="text-center">Quantity</th>
                @if ($invoiceType === 'Purchase')
                    <th style="width: 15%;" class="text-end">Buy Price</th>
                @else
                    <th style="width: 15%;" class="text-end">Sell Price</th>
                @endif
                <th style="width: 22%;" class="text-end">Total Price</th>
            </tr>
        </thead>
        <tbody>
        <tbody>
            @forelse ($productRows as $index => $row)
                <tr wire:key="product-row-{{ $index }}">
                    @if ($method !== 'PUT')
                        <td>
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click="removeProduct({{ $index }})">
                                <i class="fas fa-trash me-1"></i> Remove
                            </button>
                        </td>
                    @endif
                    <td>
                        <input type="hidden" name="products[{{ $index }}][id]" value="{{ $row['id'] }}">
                        {{ $row['name'] }}
                    </td>
                    <td>
                        <input readonly type="number" name="products[{{ $index }}][base_price]"
                            class="form-control text-end" value="{{ $row['base_price'] }}" min="0"
                            step="0.01">
                    </td>
                    <td>
                        <input @if ($method === 'PUT') disabled @endif type="number"
                            name="products[{{ $index }}][quantity]" class="form-control text-center"
                            value="{{ $row['quantity'] }}"
                            wire:change="updateQuantity({{ $index }}, $event.target.value)" min="1">
                    </td>
                    <td>
                        <input @if ($method === 'PUT') disabled @endif type="number"
                            name="products[{{ $index }}][unit_price]" class="form-control text-end"
                            value="{{ $row['unit_price'] }}"
                            wire:change="updateUnitPrice({{ $index }}, $event.target.value)" min="0"
                            step="0.01">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control"
                                name="products[{{ $index }}][total_price]" value="{{ $row['total_price'] }}"
                                readonly>
                        </div>
                    </td>
                </tr>
            @empty
                <tr id="noProductRow">
                    <td colspan="6" class="text-center py-3">
                        <div class="text-muted">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <p>No products added yet</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                @if ($method !== 'PUT')
                    <td></td>
                @endif
                <td colspan="2" style="text-align: end; font-weight: bold;">GRAND TOTAL</td>
                <td>{{ $totalQuantity }}</td>
                <td></td>
                <td class="fw-bold">Rp {{ number_format($totalPrice, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    <input type="hidden" name="grand_total" value="{{ $totalPrice }}">
</div>
