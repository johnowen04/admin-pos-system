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
            <div class="card-title">Sales Invoice Information</div>
        </div>
        <div class="card-body">
            <div class="row-md-4">

                <div class="form-group">
                    <label for="salesOutlet">Set Outlet</label>
                    <select class="form-select form-control" id="salesOutlet" name="outlets[]">
                        <option value="" disabled>Select outlet</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}"
                                {{ old('outlets_id', $salesInvoice->outlets_id ?? '') == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-datepicker id="createdAt" name="created_at" label="Entry Date" placeholder="Select a date and time"
                    value="{{ $salesInvoice->created_at ?? now() }}" required readonly />


                <div class="form-group">
                    <label for="salesInvoiceNumber">Sales Invoice Number</label>
                    <input type="text" class="form-control" id="salesInvoiceNumber" name="invoice_number"
                        placeholder="Ex: SO/251225/001, SO/010125/005, etc"
                        value="{{ old('invoice_number', $salesInvoice->invoice_number ?? $nextInvoiceNumber) }}" required readonly/>
                </div>

                <div class="form-group">
                    <label for="salesDescription">Sales Invoice Description</label>
                    <input type="text" class="form-control" id="salesDescription" name="description"
                        placeholder="Ex: lorem ipsum dolor sit amet etc"
                        value="{{ old('description', $salesInvoice->description ?? '') }}" />
                </div>

                <!-- Hidden input for NIP -->
                <input type="hidden" name="employee_id" value="{{ Auth::user()->employee->id }}">

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Products Sold</div>
        </div>
        <div class="card-body">
            <div class="row-md-4">
                <div class="form-group d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-primary" id="addProductRow" data-bs-toggle="modal"
                        data-bs-target="#productModal">Add Product</button>
                    <button type="button" class="btn btn-danger" id="removeAllProducts">Remove All Products</button>
                </div>
                <x-product-table :invoice="$salesInvoice" :products="$products" />
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
