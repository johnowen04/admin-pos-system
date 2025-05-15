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
            <div class="card-title">Purchase Invoice Information</div>
        </div>
        <div class="card-body">
            <div class="row-md-4">

                <div class="form-group">
                    <label for="purchaseOutlet">Set Outlet</label>
                    <select class="form-select form-control" id="purchaseOutlet" name="outlets[]">
                        <option value="" disabled>Select outlet</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}"
                                {{ old('outlets_id', $purchaseInvoice->outlets_id ?? '') == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-datepicker id="createdAt" name="created_at" label="Entry Date" placeholder="Select a date and time"
                    value="{{ $purchaseInvoice->created_at ?? now() }}" required readonly />


                <div class="form-group">
                    <label for="purchaseInvoiceNumber">Purchase Invoice Number</label>
                    <input type="text" class="form-control" id="purchaseInvoiceNumber" name="invoice_number"
                        placeholder="Ex: PO/251225/001, PO/010125/005, etc"
                        value="{{ old('invoice_number', $purchaseInvoice->invoice_number ?? $nextInvoiceNumber) }}" required readonly />
                </div>

                <div class="form-group">
                    <label for="purchaseDescription">Purchase Invoice Description</label>
                    <input type="text" class="form-control" id="purchaseDescription" name="description"
                        placeholder="Ex: lorem ipsum dolor sit amet etc"
                        value="{{ old('description', $purchaseInvoice->description ?? '') }}" />
                </div>

                <!-- Hidden input for NIP -->
                <input type="hidden" name="employee_id" value="{{ Auth::user()->employee->id }}">

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Products Purchased</div>
        </div>
        <div class="card-body">
            <div class="row-md-4">
                <div class="form-group d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-primary" id="addProductRow" data-bs-toggle="modal"
                        data-bs-target="#productModal">Add Product</button>
                    <button type="button" class="btn btn-danger" id="removeAllProducts">Remove All Products</button>
                </div>
                <x-product-table :invoice="$purchaseInvoice" :products="$products" />
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsTable = document.querySelector('#invoiceProductsTable tbody');
            const removeAllProductsButton = document.querySelector('#removeAllProducts');

            // Remove all product rows
            removeAllProductsButton.addEventListener('click', function() {
                productsTable.innerHTML = ''; // Clear all rows in the table body
            });
        });
    </script>
@endpush
