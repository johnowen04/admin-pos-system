<div>
    <!-- Product Table -->
    <div class="form-group">
        <div class="d-flex justify-content-end gap-2 mb-3">
            <!-- Custom Actions Slot -->
            {{ $slot }}
        </div>
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
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input select-product-checkbox"
                                            data-id="{{ $product['id'] }}" data-sku="{{ $product['sku'] }}"
                                            data-name="{{ $product['name'] }}">
                                    </td>
                                    <td>{{ $product['sku'] }}</td>
                                    <td>{{ $product['name'] }}</td>
                                    <td>{{ $product['quantity'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary add-single-product"
                                            data-id="{{ $product['id'] }}" data-sku="{{ $product['sku'] }}"
                                            data-name="{{ $product['name'] }}">
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
            // DOM Elements
            const productsTable = document.querySelector('#invoiceProductsTable tbody');
            const totalQuantityElement = document.querySelector('#totalQuantity');
            const totalPriceElement = document.querySelector('#totalPrice');
            const grandTotalInput = document.querySelector('#grandTotal');
            const noProductRow = document.getElementById('noProductRow');
            const outletSelect = document.getElementById('outlet');
            const productModalTableBody = document.querySelector('#productTable tbody');
            const confirmSelectionButton = document.querySelector('#confirmSelection');
            const productModal = document.querySelector('#productModal');
            const removeAllProductsButton = document.querySelector('#removeAllProducts');

            // Initialize DataTable
            $('#productTable').DataTable({
                pageLength: 5,
                order: [
                    [0, "asc"]
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }]
            });

            // ======== FUNCTIONS ========

            // Clear table rows
            function clearTableRows() {
                // Clear all product rows but keep the "No products added yet" row
                Array.from(productsTable.querySelectorAll('tr')).forEach(row => {
                    if (row.id !== 'noProductRow') {
                        row.remove(); // Remove all rows except the one with id="noProductRow"
                    }
                });

                // Reset totals
                calculateTotals();
                toggleNoProductRow();
            }

            // Toggle the "No products" row visibility
            function toggleNoProductRow() {
                const productRows = productsTable.querySelectorAll('tr:not(#noProductRow)');
                console.log(productRows.length);
                noProductRow.style.display = productRows.length > 0 ? 'none' : '';
            }

            // Calculate totals for quantity and price
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

            // Check if a product already exists in the table
            function isProductInTable(id) {
                return Array.from(productsTable.querySelectorAll('input[name*="[id]"]'))
                    .some(input => input.value === id);
            }

            // Add a new product row to the table
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
                                value="0" readonly>
                        </div>    
                    </td>
                </tr>
            `;
                productsTable.insertAdjacentHTML('beforeend', newRow);
                calculateTotals();
                toggleNoProductRow();
            }

            // Update the enabled state of the add selected button
            function updateAddSelectedButtonState() {
                const checkboxes = productModalTableBody.querySelectorAll('.select-product-checkbox');
                const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                confirmSelectionButton.disabled = !anyChecked;
            }

            // ======== EVENT LISTENERS ========

            // Remove all product rows
            removeAllProductsButton.addEventListener('click', function() {
                clearTableRows();
            });

            // Fetch products when outlet changes
            outletSelect.addEventListener('change', function() {
                // Clear added product on outlet change
                clearTableRows();

                const outletId = this.value;

                fetch(`/api/outlets/${outletId}/products`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Clear previous rows
                        productModalTableBody.innerHTML = '';

                        if (!data.products || data.products.length === 0) {
                            productModalTableBody.innerHTML = `
                            <tr>
                                <td colspan="4" class="text-center">No products available for this outlet.</td>
                            </tr>
                        `;
                        } else {
                            data.products.forEach(product => {
                                const row = `
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input select-product-checkbox"
                                            data-id="{{ $product['id'] }}" data-sku="{{ $product['sku'] }}"
                                            data-name="{{ $product['name'] }}">
                                    </td>
                                    <td>{{ $product['sku'] }}</td>
                                    <td>{{ $product['name'] }}</td>
                                    <td>{{ $product['quantity'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary add-single-product"
                                            data-id="{{ $product['id'] }}" data-sku="{{ $product['sku'] }}"
                                            data-name="{{ $product['name'] }}">
                                            Add
                                        </button>
                                    </td>
                                </tr>
                            `;
                                productModalTableBody.insertAdjacentHTML('beforeend', row);
                            });

                            // Update button state
                            updateAddSelectedButtonState();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        productModalTableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-danger">Error loading products. Please try again.</td>
                        </tr>
                    `;
                    });
            });

            // Event delegation for quantity and price changes
            productsTable.addEventListener('input', function(event) {
                if (event.target.classList.contains('product-quantity') ||
                    event.target.classList.contains('product-unit-price')) {
                    calculateTotals();
                }
            });

            // Event delegation for removing products
            productsTable.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-product-row')) {
                    event.target.closest('tr').remove();
                    calculateTotals();
                    toggleNoProductRow();
                }
            });

            // Event delegation for checkboxes in modal
            productModalTableBody.addEventListener('change', function(event) {
                if (event.target.classList.contains('select-product-checkbox')) {
                    updateAddSelectedButtonState();
                }
            });

            // Event delegation for single product add button
            productModalTableBody.addEventListener('click', function(event) {
                if (event.target.classList.contains('add-single-product')) {
                    const id = event.target.getAttribute('data-id');
                    const name = event.target.getAttribute('data-name');
                    addProductRow(id, name);

                    // Optional: close modal after adding a single product
                    // const modalInstance = bootstrap.Modal.getInstance(productModal);
                    // if (modalInstance) modalInstance.hide();
                }
            });

            // Add selected products button
            confirmSelectionButton.addEventListener('click', function() {
                const selectedCheckboxes = productModalTableBody.querySelectorAll(
                    '.select-product-checkbox:checked');

                selectedCheckboxes.forEach(checkbox => {
                    const id = checkbox.getAttribute('data-id');
                    const name = checkbox.getAttribute('data-name');
                    addProductRow(id, name);
                    checkbox.checked = false;
                });

                // Update button state
                updateAddSelectedButtonState();

                // Close modal
                const modalInstance = bootstrap.Modal.getInstance(productModal);
                if (modalInstance) modalInstance.hide();
            });

            // Initialize on page load
            calculateTotals();
            toggleNoProductRow();
        });
    </script>
@endpush
