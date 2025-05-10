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
                <!-- Existing rows will be dynamically populated -->
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

            // Initialize DataTables
            $('#productTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                "columnDefs": [{
                        "orderable": false,
                        "targets": 0
                    } // Disable sorting on the Action column
                ],
            });

            // Function to calculate the total price for a row
            function calculateRowTotal(row) {
                const quantityInput = row.querySelector('input[name*="[quantity]"]');
                const unitPriceInput = row.querySelector('input[name*="[unit_price]"]');
                const totalPriceInput = row.querySelector('input[name*="[total_price]"]');

                if (quantityInput && unitPriceInput && totalPriceInput) {
                    const quantity = parseFloat(quantityInput.value) || 0;
                    const unitPrice = parseFloat(unitPriceInput.value) || 0;
                    const totalPrice = quantity * unitPrice;

                    // Update the total price input
                    totalPriceInput.value = totalPrice.toFixed(2); // Keep 2 decimal places
                }
            }

            const totalQuantityElement = document.querySelector('#totalQuantity');
            const totalPriceElement = document.querySelector('#totalPrice');

            // Function to calculate the grand total
            function calculateGrandTotal() {
                let totalQuantity = 0;
                let totalPrice = 0;

                // Loop through all rows in the table
                productsTable.querySelectorAll('tr').forEach(row => {
                    const quantityInput = row.querySelector('input[name*="[quantity]"]');
                    const unitPriceInput = row.querySelector('input[name*="[unit_price]"]');

                    if (quantityInput && unitPriceInput) {
                        const quantity = parseFloat(quantityInput.value) || 0;
                        const unitPrice = parseFloat(unitPriceInput.value) || 0;

                        totalQuantity += quantity;
                        totalPrice += quantity * unitPrice;
                    }
                });

                // Update the footer with the calculated totals
                totalQuantityElement.textContent = totalQuantity;
                totalPriceElement.textContent = `Rp${totalPrice.toLocaleString('id-ID')}`;
                
                // Update the hidden grand total input field
                document.querySelector('#grandTotal').value = totalPrice.toFixed(2);
            }

            // Event listener for changes in quantity or unit price inputs
            productsTable.addEventListener('input', function(e) {
                if (e.target.name.includes('[quantity]') || e.target.name.includes('[unit_price]')) {
                    const row = e.target.closest('tr');
                    calculateRowTotal(row); // Update the total price for the row
                    calculateGrandTotal();
                }
            });

            // Event listener for removing a product row
            productsTable.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-product')) {
                    e.target.closest('tr').remove();
                    calculateGrandTotal(); // Recalculate totals after removing a row
                }
            });

            const confirmSelectionButton = document.querySelector('#confirmSelection');
            const productCheckboxes = document.querySelectorAll('.select-product-checkbox');
            const singleProductButtons = document.querySelectorAll('.add-single-product');

            // Function to check if a product already exists in the table
            function isProductInTable(sku) {
                const existingProducts = Array.from(productsTable.querySelectorAll('input[name*="[sku]"]'));
                return existingProducts.some(input => input.value === sku);
            }

            // Function to toggle the "Add Selected Products" button and disable single product buttons
            function toggleButtons() {
                const anyChecked = Array.from(productCheckboxes).some(checkbox => checkbox.checked);

                // Enable/Disable "Add Selected Products" button
                confirmSelectionButton.disabled = !anyChecked;

                // Enable/Disable "Add Single Product" buttons
                singleProductButtons.forEach(button => {
                    button.disabled =
                        anyChecked; // Disable single product buttons if any checkbox is checked
                });
            }

            // Add event listeners to all checkboxes
            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleButtons);
            });

            // Handle "Add Single Product" button click
            singleProductButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const sku = this.dataset.sku;
                    const name = this.dataset.name;

                    // Check if the product is already in the table
                    if (isProductInTable(sku)) {
                        alert(`The product "${name}" is already added to the table.`);
                        return;
                    }

                    // Add product to the table
                    const rowCount = productsTable.rows.length;
                    const newRow = `
                    <tr>
                        <td><button type="button" class="btn btn-danger remove-product">Remove</button></td>
                        <td>${name} <input type="hidden" name="products[${rowCount}][sku]" value="${sku}"></td>
                        <td><input type="number" class="form-control" name="products[${rowCount}][quantity]" value="1" required /></td>
                        <td>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="products[${rowCount}][unit_price]" value="0" required />
                            </div>
                        </td>
                        <td>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="products[${rowCount}][total_price]" value="0" readonly />
                            </div>
                        </td>
                    </tr>
                `;
                    productsTable.insertAdjacentHTML('beforeend', newRow);

                    // Recalculate totals after adding products
                    calculateGrandTotal();

                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.querySelector(
                        '#productModal'));
                    modal.hide();
                });
            });

            // Handle "Add Selected Products" button click
            confirmSelectionButton.addEventListener('click', function() {
                // Get all selected checkboxes
                const selectedProducts = document.querySelectorAll('.select-product-checkbox:checked');

                selectedProducts.forEach((checkbox) => {
                    const sku = checkbox.dataset.sku;
                    const name = checkbox.dataset.name;

                    // Check if the product is already in the table
                    if (isProductInTable(sku)) {
                        alert(`The product "${name}" is already added to the table.`);
                        return;
                    }

                    // Add product to the table
                    const rowCount = productsTable.rows.length;
                    const newRow = `
                    <tr>
                        <td><button type="button" class="btn btn-danger remove-product">Remove</button></td>
                        <td>${name} <input type="hidden" name="products[${rowCount}][sku]" value="${sku}"></td>
                        <td><input type="number" class="form-control" name="products[${rowCount}][quantity]" value="1" required /></td><td>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="products[${rowCount}][unit_price]" value="0" required />
                            </div>
                        </td>
                        <td>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="products[${rowCount}][total_price]" value="0" readonly />
                            </div>
                        </td>
                    </tr>
                `;
                    productsTable.insertAdjacentHTML('beforeend', newRow);
                });

                // Recalculate totals after adding products
                calculateGrandTotal();

                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.querySelector('#productModal'));
                modal.hide();

                // Clear all checkboxes
                document.querySelectorAll('.select-product-checkbox').forEach((checkbox) => {
                    checkbox.checked = false;
                });

                // Reset button states
                toggleButtons();
            });

            // Remove a product row
            productsTable.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-product')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
@endpush
