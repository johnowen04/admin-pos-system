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

    @if ($method === 'PUT')
        @if ($invoice->is_voided)
            <div class="alert alert-warning">
                <strong>Warning:</strong> This invoice has been voided and cannot be edited.
            </div>
        @endif
    @endif

    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ $invoiceType }} Invoice Information</div>
        </div>
        <div class="card-body">
            <div class="row-md-4">

                <div class="form-group">
                    <label for="outlet">Set Outlet</label>
                    <select @if ($method === 'PUT') disabled @endif class="form-select form-control"
                        id="outlet" name="outlet_id" required>
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
                    value="{{ $invoice->created_at ?? now() }}" required readonly disabled />
                <input type="hidden" name="created_at" value="{{ $invoice->created_at ?? now() }}">

                <div class="form-group">
                    <label for="invoiceNumber">{{ $invoiceType }} Invoice Number</label>
                    <input type="text" class="form-control" id="invoiceNumber" name="invoice_number"
                        placeholder="Ex: PO/251225/001, PO/010125/005, etc"
                        value="{{ old('invoice_number', $invoice->invoice_number ?? $nextInvoiceNumber) }}" required
                        readonly />
                </div>

                <div class="form-group">
                    <label for="description">{{ $invoiceType }} Invoice Description</label>
                    <input @if ($method === 'PUT') readonly @endif type="text" class="form-control"
                        id="description" name="description" placeholder="Ex: lorem ipsum dolor sit amet etc"
                        value="{{ old('description', $invoice->description ?? '') }}" />
                </div>

                @if ($method === 'PUT')
                    <div class="form-group">
                        <label for="creator">{{ $invoiceType }} Invoice Creator</label>
                        <input readonly type="text" class="form-control" id="creator" name="creator"
                            value="{{ old('creator', $invoice->creator->name ?? '') }}" />
                    </div>
                @endif

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
                    <x-detail-invoice-product-table :method="$method" :invoiceType="$invoiceType" :invoice="$invoice" :products="$products">
                        <button @if ($method === 'PUT') disabled @endif type="button" class="btn btn-primary"
                            id="addProductRow" data-bs-toggle="modal" data-bs-target="#productModal">Add
                            Product</button>
                        <button @if ($method === 'PUT') disabled @endif type="button" class="btn btn-danger"
                            id="removeAllProducts">Remove All
                            Products</button>
                    </x-detail-invoice-product-table>
                </div>
            </div>
        </div>

        <!-- Void Confirmation Modal -->
        @if (str_contains($action, 'void'))
            <div class="modal fade" id="voidConfirmModal" tabindex="-1" aria-labelledby="voidConfirmModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="voidConfirmModalLabel">Confirm Void</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if (isset($invoice) && $invoice->is_voided)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    This invoice has already been voided and cannot be voided again.
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    This action cannot be undone. Are you sure you want to void this invoice?
                                </div>
                                <div class="mb-3">
                                    <label for="void_reason" class="form-label">Reason for Voiding <span
                                            class="text-danger">*</span></label>
                                    <textarea name="void_reason" id="void_reason" class="form-control" rows="3" required></textarea>
                                    <div class="invalid-feedback">Please provide a reason for voiding.</div>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            @if (!(isset($invoice) && $invoice->is_voided))
                                <button type="submit" class="btn btn-danger">Confirm Void</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
