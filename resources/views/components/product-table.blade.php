<div>
    <!-- Product Table -->
    <div class="form-group">
        <label for="invoiceProducts">Products</label>
        <table class="table table-bordered" id="invoiceProductsTable">
            <thead>
                <tr>
                    <th style="width: 14%;">Actions</th>
                    <th style="width: 32%;">Product</th>
                    <th style="width: 10%;">Quantity</th>
                    <th style="width: 22%;">Unit Price</th>
                    <th style="width: 22%;">Total Price</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($invoice) && $invoice->products->isNotEmpty())
                    @foreach ($invoice->products as $product)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-product-row">Remove</button>
                            </td>
                            <td>
                                <input type="hidden" name="products[{{ $loop->index }}][sku]"
                                    value="{{ $product->sku }}">
                                {{ $product->name }}
                            </td>
                            <td>
                                <input type="number" name="products[{{ $loop->index }}][quantity]"
                                    class="form-control product-quantity" value="{{ $product->pivot->quantity }}"
                                    min="1">
                            </td>
                            <td>
                                <input type="number" name="products[{{ $loop->index }}][unit_price]"
                                    class="form-control product-unit-price" value="{{ $product->pivot->unit_price }}"
                                    min="0" step="0.01">
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control product-total-price"
                                        name="products[${rowCount}][total_price]" value="{{ number_format($product->pivot->quantity * $product->pivot->unit_price, 2) }}" readonly>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">No products added yet.</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: end; font-weight: bold;">GRAND TOTAL</td>
                    <td id="totalQuantity">0</td>
                    <td></td>
                    <td id="totalPrice">Rp0</td>
                </tr>
            </tfoot>
        </table>
        <input type="hidden" id="grandTotal" name="grand_total" value="0">
    </div>

    <!-- Product Selection Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Select Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="productTable">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input select-product-checkbox"
                                            data-sku="{{ $product->sku }}" data-name="{{ $product->name }}">
                                    </td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary add-single-product"
                                            data-sku="{{ $product->sku }}" data-name="{{ $product->name }}">
                                            Add
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirmSelection" disabled>Add Selected
                        Products</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsTable = document.querySelector('#invoiceProductsTable tbody');
            const confirmSelectionButton = document.querySelector('#confirmSelection');
            const productCheckboxes = document.querySelectorAll('.select-product-checkbox');
            const singleProductButtons = document.querySelectorAll('.add-single-product');
            const totalQuantityElement = document.querySelector('#totalQuantity');
            const totalPriceElement = document.querySelector('#totalPrice');
            const grandTotalInput = document.querySelector('#grandTotal');

            // Function to calculate totals
            function calculateTotals() {
                let totalQuantity = 0;
                let totalPrice = 0;

                productsTable.querySelectorAll('tr').forEach(row => {
                    const quantityInput = row.querySelector('input[name*="[quantity]"]');
                    const unitPriceInput = row.querySelector('input[name*="[unit_price]"]');
                    const totalPriceInput = row.querySelector('input[name*="[total_price]"]');

                    if (quantityInput && unitPriceInput) {
                        const quantity = parseFloat(quantityInput.value) || 0;
                        const unitPrice = parseFloat(unitPriceInput.value) || 0;
                        const total = quantity * unitPrice;

                        // Update the total price for the row
                        if (totalPriceInput) {
                            totalPriceInput.value = total.toFixed(2);
                        }

                        // Accumulate totals
                        totalQuantity += quantity;
                        totalPrice += total;
                    }
                });

                // Update totals in the footer
                totalQuantityElement.textContent = totalQuantity;
                totalPriceElement.textContent = `Rp${totalPrice.toLocaleString('id-ID')}`;
                grandTotalInput.value = totalPrice.toFixed(2);
            }

            // Function to check if a product already exists in the table
            function isProductInTable(sku) {
                return Array.from(productsTable.querySelectorAll('input[name*="[sku]"]'))
                    .some(input => input.value === sku);
            }

            // Function to add a new product row
            function addProductRow(sku, name) {
                if (isProductInTable(sku)) {
                    alert(`The product "${name}" is already added to the table.`);
                    return;
                }

                const rowCount = productsTable.querySelectorAll('tr').length;
                const newRow = `
            <tr>
                <td><button type="button" class="btn btn-danger remove-product">Remove</button></td>
                <td>${name} <input type="hidden" name="products[${rowCount}][sku]" value="${sku}"></td>
                <td><input type="number" class="form-control product-quantity" name="products[${rowCount}][quantity]" value="1" min="1" required></td>
                <td>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control product-unit-price" name="products[${rowCount}][unit_price]" value="0" min="0" step="0.01" required>
                    </div>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control product-total-price" name="products[${rowCount}][total_price]" value="0" readonly>
                    </div>
                </td>
            </tr>
        `;
                productsTable.insertAdjacentHTML('beforeend', newRow);

                // Attach event listeners to the new row
                attachRowEventListeners(productsTable.lastElementChild);

                // Recalculate totals after adding a product
                calculateTotals();
            }

            // Function to attach event listeners to a row
            function attachRowEventListeners(row) {
                const quantityInput = row.querySelector('input[name*="[quantity]"]');
                const unitPriceInput = row.querySelector('input[name*="[unit_price]"]');
                const removeButton = row.querySelector('.remove-product');

                if (quantityInput) {
                    quantityInput.addEventListener('input', calculateTotals);
                }

                if (unitPriceInput) {
                    unitPriceInput.addEventListener('input', calculateTotals);
                }

                if (removeButton) {
                    removeButton.addEventListener('click', function() {
                        row.remove();
                        calculateTotals(); // Recalculate totals after removing a row
                    });
                }
            }

            // Attach event listeners to all pre-populated rows
            productsTable.querySelectorAll('tr').forEach(row => {
                attachRowEventListeners(row);
            });

            // Event listener for "Add Single Product" buttons
            singleProductButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const sku = this.dataset.sku;
                    const name = this.dataset.name;
                    addProductRow(sku, name);
                });
            });

            // Event listener for "Add Selected Products" button
            confirmSelectionButton.addEventListener('click', function() {
                const selectedProducts = document.querySelectorAll('.select-product-checkbox:checked');

                selectedProducts.forEach(checkbox => {
                    const sku = checkbox.dataset.sku;
                    const name = checkbox.dataset.name;
                    addProductRow(sku, name);
                });

                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.querySelector('#productModal'));
                modal.hide();

                // Clear all checkboxes
                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                // Reset button states
                toggleButtons();
            });

            // Function to toggle button states
            function toggleButtons() {
                const anyChecked = Array.from(productCheckboxes).some(checkbox => checkbox.checked);
                confirmSelectionButton.disabled = !anyChecked;
            }

            // Add event listeners to checkboxes
            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleButtons);
            });

            // Initial calculation of totals for pre-populated rows
            calculateTotals();
        });
    </script>
@endpush
