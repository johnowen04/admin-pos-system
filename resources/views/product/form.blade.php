<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="card-title">Product Information</div>
        </div>
        <div class="card-body">
            <div class="row-md-4">

                <div class="form-group">
                    <label for="outlet-select">Set Outlet</label>
                    <div id="outlet-select" wire:key="outlet-select">
                        @livewire('outlet-select', ['selectedOutletIds' => old('outlets', $selectedOutlets ?? [])])
                    </div>
                </div>

                <div class="form-group">
                    <label for="productName">Product Name</label>
                    <input type="text" class="form-control" id="productName" name="name"
                        placeholder="Ex: Fanta, Coca Cola, etc" value="{{ old('name', $product->name ?? '') }}"
                        required tabindex="1"/>
                </div>

                <div class="form-group">
                    <label for="productDescription">Product Description</label>
                    <input type="text" class="form-control" id="productDescription" name="description"
                        placeholder="Ex: Best seller, top, etc"
                        value="{{ old('description', $product->description ?? '') }}" tabindex="2" />
                </div>

                <div class="form-group">
                    <label for="productCategory">Product Category</label>
                    <select class="form-select form-control" id="productCategory" name="category_id" tabindex="3">
                        <option value="" disabled selected>Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Show in menu?</label>
                    <div class="d-flex align-items-center" x-data="{ isShown: {{ old('is_shown', $category->is_shown ?? 1) }} }">
                        <div class="form-check form-switch mb-1 d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" role="switch" id="showInMenuToggle"
                                name="is_shown" value="1" x-model="isShown"
                                {{ old('is_shown', $category->is_shown ?? 1) == 1 ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label m-0 d-flex align-items-center" for="showInMenuToggle">
                            <span x-text="isShown == 1 ? 'Yes' : 'No'">
                            </span>
                        </label>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Price and Units</div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 col-lg-6">

                    <div class="form-group">
                        <label for="productUnit">Product Unit</label>
                        <select class="form-select form-control" id="productUnit" name="unit_id" tabindex="6">
                            <option value="" disabled selected>Select unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" data-conversion="{{ $unit->conversion_unit }}"
                                    {{ old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="productConversion">Conversion</label>
                        <input type="number" class="form-control" id="productConversion" placeholder="0" disabled
                            value="{{ old('conversion_unit', $product->unit->conversion_unit ?? '') }}" tabindex="8"/>
                    </div>

                    <div class="form-group">
                        <label for="productBuyPrice">Buy Price</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon3">Rp</span>

                            <input readonly type="number" class="form-control" id="productBuyPrice" placeholder="0"
                                name="buy_price" value="{{ old('buy_price', $product->buy_price ?? '') }}" tabindex="10"/>
                        </div>
                    </div>

                </div>
                <div class="col-md-2 col-lg-6">

                    <div class="form-group">
                        <label for="productSKU">SKU</label>
                        <input type="text" class="form-control" id="productSKU" placeholder="Ex: P001" name="sku"
                            value="{{ old('sku', $product->sku ?? '') }}" tabindex="7"/>
                    </div>

                    <div class="form-group">
                        <label for="productMinOrder">Minimum Order</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="productMinOrder"
                                placeholder="Ex: 1, 2, 3" name="min_qty"
                                value="{{ old('min_qty', $product->min_qty ?? '') }}" tabindex="9"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="productSellPrice">Sell Price</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon3">Rp</span>

                            <input type="number" class="form-control" id="productSellPrice" placeholder="0"
                                name="sell_price" value="{{ old('sell_price', $product->sell_price ?? '') }}" tabindex="11"/>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const unitSelect = document.getElementById('productUnit');
            const conversionInput = document.getElementById('productConversion');

            const minimumOrderUnit = document.getElementById('minimumOrderUnit');

            // Listen for changes on the unit dropdown
            unitSelect.addEventListener('change', function() {
                // Get the selected option
                const selectedOption = unitSelect.options[unitSelect.selectedIndex];

                // Get the conversion value from the data attribute
                const conversionValue = selectedOption.getAttribute('data-conversion');
                const unitName = selectedOption.value != 0 ? selectedOption.textContent : '';


                // Update the conversion input field
                conversionInput.value = conversionValue || '';
            });

            // Trigger the change event on page load to set the initial values
            unitSelect.dispatchEvent(new Event('change'));
        });
    </script>
@endpush
