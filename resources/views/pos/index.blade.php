@extends('layouts.app')

@section('title', 'POS')

@section('content')
    <div class="page-inner">

        <!-- Search and Product Grid -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex gap-2">
                            <!-- Category Dropdown -->
                            <select id="categoryFilter" class="form-select" style="width: 200px;">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <!-- Search Box -->
                            <input type="text" id="productSearch" class="form-control" placeholder="Search Products...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="productGrid" class="row g-4 style="max-height: 350px; overflow-y: auto;">
                            @foreach ($products as $product)
                                <div class="col-6 col-md-4 col-lg-3 product-item" data-name="{{ $product->name }}"
                                    data-sku="{{ $product->sku }}" data-category="{{ $product->categories_id }}">
                                    <div class="border p-3 text-center rounded bg-light">
                                        <div class="fw-bold">{{ $product->sku }}</div>
                                        <div>{{ $product->name }}</div>
                                        <div class="text-muted">Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                        </div>
                                        <button class="btn btn-sm btn-primary mt-2">Add</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block fw-bold mb-1">{{ $invoiceNumber }}</span>
                            <span>Cart</span>
                        </div>
                        <button id="clearCartButton" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> <!-- Font Awesome Trash Icon -->
                        </button>
                    </div>
                    <div class="card-body">
                        <ul id="cartItems" class="list-group mb-3"
                            style="min-height: 60vh; max-height: 350px; overflow-y: auto;">
                            <!-- Example cart item -->
                            <!-- This will be dynamically populated -->
                            <!--
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Fruitea 500 Strawberry</strong>
                                                <div>Qty: 2 x Rp 7.000</div>
                                            </div>
                                            <div>
                                                <span>Rp 14.000</span>
                                                <button class="btn btn-sm btn-danger ms-2 remove-item">X</button>
                                            </div>
                                        </li>
                                        -->
                        </ul>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="cartTotal">Rp 0</strong>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <form action="{{ route('pos.payment') }}" method="POST" id="paymentForm">
                            @csrf
                            <input type="hidden" name="cart" id="cartInput">
                            <input type="hidden" name="grandTotal" id="grandTotalInput">
                            <input type="hidden" name="invoiceNumber" id="grandTotalInput" value="{{ $invoiceNumber }}">
                            <button type="submit" id="checkoutButton" class="btn btn-success" disabled>Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const productGrid = $('#productGrid');

            // Search functionality
            $('#productSearch').on('keyup', function() {
                filterProducts();
            });

            // Category filter functionality
            $('#categoryFilter').on('change', function() {
                filterProducts();
            });

            // Filter products based on search and category
            function filterProducts() {
                const searchTerm = $('#productSearch').val().toLowerCase();
                const selectedCategory = $('#categoryFilter').val();

                $('.product-item').each(function() {
                    const sku = $(this).data('sku').toLowerCase();
                    const name = $(this).data('name').toLowerCase();
                    const category = $(this).data('category').toString();

                    const matchesSearch = name.includes(searchTerm) || sku.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category === selectedCategory;

                    if (matchesSearch && matchesCategory) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            const cart = {};
            const cartItems = $('#cartItems');
            const cartTotal = $('#cartTotal');
            const checkoutButton = $('#checkoutButton');
            const clearCartButton = $('#clearCartButton');
            const paymentForm = $('#paymentForm');
            const cartInput = $('#cartInput');
            const grandTotalInput = $('#grandTotalInput');

            // Clear all items from the cart
            clearCartButton.on('click', function() {
                Object.keys(cart).forEach((sku) => delete cart[sku]);
                updateCart();
            });

            // Add item to cart
            $('.product-item button').on('click', function() {
                const productElement = $(this).closest('.product-item');
                const name = productElement.data('name');
                const sku = productElement.data('sku');
                const price = parseFloat(productElement.find('.text-muted').text().replace(/[^\d]/g, ''));

                if (!cart[sku]) {
                    cart[sku] = {
                        name,
                        price,
                        quantity: 1
                    };
                } else {
                    cart[sku].quantity++;
                }

                updateCart();
            });

            // Update cart UI
            function updateCart() {
                cartItems.empty();
                let total = 0;

                Object.keys(cart).forEach((sku) => {
                    const item = cart[sku];
                    const subtotal = item.price * item.quantity;
                    total += subtotal;

                    const cartItem = $(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${item.name}</strong>
                            <div>
                                Qty: 
                                <input type="number" class="form-control form-control-sm quantity-input" 
                                    data-sku="${sku}" value="${item.quantity}" min="1" style="width: 60px; display: inline-block;">
                                x Rp ${item.price.toLocaleString('id-ID')}
                            </div>
                        </div>
                        <div>
                            <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
                            <button class="btn btn-sm btn-warning ms-2 minus-item" data-sku="${sku}">-</button>
                            <button class="btn btn-sm btn-danger ms-2 remove-item" data-sku="${sku}">X</button>
                        </div>
                    </li>
                    `);

                    cartItems.append(cartItem);
                });

                cartTotal.text(`Rp ${total.toLocaleString('id-ID')}`);
                grandTotalInput.val(total); // Update grand total input
                cartInput.val(JSON.stringify(cart)); // Update cart input
                checkoutButton.prop('disabled', total === 0);
            }

            // Handle quantity change
            cartItems.on('change', '.quantity-input', function() {
                const sku = $(this).data('sku');
                const newQuantity = parseInt($(this).val());

                if (cart[sku] && newQuantity > 0) {
                    cart[sku].quantity = newQuantity;
                } else if (newQuantity <= 0) {
                    delete cart[sku];
                }

                updateCart();
            });

            // Reduce quantity with minus button
            cartItems.on('click', '.minus-item', function() {
                const sku = $(this).data('sku');
                if (cart[sku]) {
                    cart[sku].quantity--;
                    if (cart[sku].quantity <= 0) {
                        delete cart[sku];
                    }
                }
                updateCart();
            });

            // Remove item entirely with X button
            cartItems.on('click', '.remove-item', function() {
                const sku = $(this).data('sku');
                if (cart[sku]) {
                    delete cart[sku];
                }
                updateCart();
            });
        });
    </script>
@endpush
