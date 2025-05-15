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
                                <input type="hidden" name="products[{{ $loop->index }}][id]"
                                    value="{{ $product->id }}">
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
                                        name="products[{{ $loop->index }}][total_price]"
                                        value="{{ $product->pivot->quantity * $product->pivot->unit_price }}" readonly>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr id="noProductRow">
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
                                            data-id="{{ $product->id }}" data-sku="{{ $product->sku }}"
                                            data-name="{{ $product->name }}">
                                    </td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary add-single-product"
                                            data-id="{{ $product->id }}" data-sku="{{ $product->sku }}"
                                            data-name="{{ $product->name }}">
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
            $('#productTable').DataTable({
                "pageLength": 5,
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } // Disable sorting on the Action column
                ]
            });

            const productsTable = document.querySelector('#invoiceProductsTable tbody');
            const confirmSelectionButton = document.querySelector('#confirmSelection');
            const productCheckboxes = document.querySelectorAll('.select-product-checkbox');
            const singleProductButtons = document.querySelectorAll('.add-single-product');
            const totalQuantityElement = document.querySelector('#totalQuantity');
            const totalPriceElement = document.querySelector('#totalPrice');
            const grandTotalInput = document.querySelector('#grandTotal');
            const noProductRow = document.getElementById('noProductRow');

            // Function to toggle the "No products" row
            function toggleNoProductRow() {
                const productRows = productsTable.querySelectorAll('tr:not(#noProductRow)');
                noProductRow.style.display = productRows.length > 0 ? 'none' : '';
            }

            // Function to calculate totals
            function calculateTotals() {
                let totalQuantity = 0;
                let totalPrice = 0;

                productsTable.querySelectorAll('tr').forEach(row => {
                    const quantityInput = row.querySelector('.product-quantity');
                    const unitPriceInput = row.querySelector('.product-unit-price');
                    const totalPriceInput = row.querySelector('.product-total-price');

                    if (quantityInput && unitPriceInput) {
                        const quantity = parseFloat(quantityInput.value) || 0;
                        const unitPrice = parseFloat(unitPriceInput.value) || 0;
                        const total = quantity * unitPrice;

                        if (totalPriceInput) {
                            totalPriceInput.value = total.toFixed(2);
                        }

                        totalQuantity += quantity;
                        totalPrice += total;
                    }
                });

                totalQuantityElement.textContent = totalQuantity;
                totalPriceElement.textContent = `Rp${totalPrice.toLocaleString('id-ID')}`;
                grandTotalInput.value = totalPrice.toFixed(2);
            }

            // Function to check if a product already exists in the table
            function isProductInTable(id) {
                return Array.from(productsTable.querySelectorAll('input[name*="[id]"]'))
                    .some(input => input.value === id);
            }

            // Function to add a new product row
            function addProductRow(id, name) {
                if (isProductInTable(id)) {
                    alert(`The product "${name}" is already added to the table.`);
                    return;
                }

                const rowCount = productsTable.querySelectorAll('tr').length;
                const newRow = `
                    <tr>
                        <td><button type="button" class="btn btn-danger btn-sm remove-product-row">Remove</button></td>
                        <td>${name} <input type="hidden" name="products[${rowCount}][id]" value="${id}"></td>
                        <td><input type="number" class="form-control product-quantity" name="products[${rowCount}][quantity]" value="1" min="1" required></td>
                        <td><input type="number" class="form-control product-unit-price" name="products[${rowCount}][unit_price]" value="0" min="0" step="0.01" required></td>
                        <td>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control product-total-price"
                                    name="products[${rowCount}][total_price]"
                                    value="" readonly>
                            </div>    
                        </td>
                    </tr>
                `;
                productsTable.insertAdjacentHTML('beforeend', newRow);

                attachRowEventListeners(productsTable.lastElementChild);
                calculateTotals();
                toggleNoProductRow();
            }

            // Function to attach event listeners to a row
            function attachRowEventListeners(row) {
                const quantityInput = row.querySelector('.product-quantity');
                const unitPriceInput = row.querySelector('.product-unit-price');
                const removeButton = row.querySelector('.remove-product-row');

                if (quantityInput) {
                    quantityInput.addEventListener('input', calculateTotals);
                }

                if (unitPriceInput) {
                    unitPriceInput.addEventListener('input', calculateTotals);
                }

                if (removeButton) {
                    removeButton.addEventListener('click', function() {
                        row.remove();
                        calculateTotals();
                        toggleNoProductRow();
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
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    addProductRow(id, name);
                });
            });

            // Event listener for "Add Selected Products" button
            confirmSelectionButton.addEventListener('click', function() {
                const selectedProducts = document.querySelectorAll('.select-product-checkbox:checked');

                selectedProducts.forEach(checkbox => {
                    const id = checkbox.dataset.id;
                    const name = checkbox.dataset.name;
                    addProductRow(id, name);
                });

                const modal = bootstrap.Modal.getInstance(document.querySelector('#productModal'));
                modal.hide();

                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                toggleButtons();
            });

            // Function to toggle button states
            function toggleButtons() {
                const anyChecked = Array.from(productCheckboxes).some(checkbox => checkbox.checked);
                confirmSelectionButton.disabled = !anyChecked;
            }

            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleButtons);
            });

            calculateTotals();
            toggleNoProductRow();
        });
    </script>
@endpush
