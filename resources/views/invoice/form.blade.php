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
            <div class="card-title">{{ $invoiceType }} Invoice Information</div>
        </div>
        <div class="card-body">
            <div class="row-md-4">

                <div class="form-group">
                    <label for="outlet">Set Outlet</label>
                    <select class="form-select form-control" id="outlet" name="outlet_id" required>
                        <option value="" disabled>Select outlet</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}"
                                {{ old('outlet_id', $invoice->outlet_id ?? '') == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-datepicker id="createdAt" name="created_at" label="Entry Date" placeholder="Select a date and time"
                    value="{{ $invoice->created_at ?? now() }}" required readonly />


                <div class="form-group">
                    <label for="invoiceNumber">{{ $invoiceType }} Invoice Number</label>
                    <input type="text" class="form-control" id="invoiceNumber" name="invoice_number"
                        placeholder="Ex: PO/251225/001, PO/010125/005, etc"
                        value="{{ old('invoice_number', $invoice->invoice_number ?? $nextInvoiceNumber) }}" required
                        readonly />
                </div>

                <div class="form-group">
                    <label for="description">{{ $invoiceType }} Invoice Description</label>
                    <input type="text" class="form-control" id="description" name="description"
                        placeholder="Ex: lorem ipsum dolor sit amet etc"
                        value="{{ old('description', $invoice->description ?? '') }}" />
                </div>

                @if ($method === 'PUT')
                    <div class="form-group">
                        <label for="creator">{{ $invoiceType }} Invoice Creator</label>
                        <input readonly type="text" class="form-control" id="creator" name="creator"
                            value="{{ old('creator', $invoice->creator->name ?? '') }}" />
                    </div>
                @endif

                <!-- Hidden input for NIP -->
                <input type="hidden" name="employee_id" value="{{ Auth::user()->employee->id ?? null }}">
                <input type="hidden" name="created_by" value="{{ Auth::user()->id }}">

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Products {{ $invoiceType === 'Purchase' ? 'Purchased' : 'Sold' }}</div>
        </div>
        <div class="card-body">
            <div class="row-md-4">
                <div class="form-group d-flex justify-content-end gap-2">

                </div>
                <div id="productTableContainer">
                    <x-product-table :invoiceType="$invoiceType" :invoice="$invoice" :products="$products">
                        <button type="button" class="btn btn-primary" id="addProductRow" data-bs-toggle="modal"
                            data-bs-target="#productModal">Add Product</button>
                        <button type="button" class="btn btn-danger" id="removeAllProducts">Remove All
                            Products</button>
                    </x-product-table>
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsTable = document.querySelector('#invoiceProductsTable tbody');
        });
    </script>
@endpush
