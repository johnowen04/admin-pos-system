<div class="form-group">
    <div class="position-relative">
        <form class="d-flex w-100" wire:submit.prevent>
            <div class="input-group w-100">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input x-data="{}" x-init="$nextTick(() => $el.focus())"
                    x-on:livewire:update="setTimeout(() => $el.focus(), 10)" wire:model.live.debounce.300ms="search"
                    class="form-control" placeholder="Search products by name or SKU..." aria-label="Search"
                    type="search">
            </div>
        </form>

        <!-- Dropdown will be positioned below the search input with same width -->
        @if ($showResults)
            <div class="dropdown-menu w-100 show" style="margin-top: 2px; max-height: 350px; overflow-y: auto;">
                <div class="px-3 py-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Product Results ({{ count($searchResults) }})</span>
                        <button type="button" class="btn-close btn-sm" wire:click="hideResults"></button>
                    </div>
                </div>

                @forelse($searchResults as $product)
                    <div class="px-3 py-2 border-bottom product-item" wire:key="product-{{ $product['id'] }}"
                        wire:click="selectProduct({{ $product['id'] }})"
                        style="cursor: pointer; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor=''">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="fw-medium">{{ $product['name'] }}</span>
                                <small class="d-block text-muted">{{ $product['category'] }} | SKU:
                                    {{ $product['sku'] }}</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-primary">Rp
                                    {{ number_format($product['price'], 0, ',', '.') }}</span>
                                <small class="d-block text-muted">Stock: {{ $product['stock'] }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-3 py-3 text-center text-muted">
                        <p class="mb-0">No products found matching "{{ $search }}"</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>
