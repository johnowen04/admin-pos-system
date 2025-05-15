@extends('layouts.app')

@section('title', 'POS')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                        <div id="productGrid" class="row g-4" style="max-height: 350px; overflow-y: auto;">
                            @foreach ($products as $product)
                                <div class="col-6 col-md-4 col-lg-3 product-item" data-name="{{ $product->name }}"
                                    data-sku="{{ $product->sku }}" data-category="{{ $product->categories_id }}">
                                    <div class="border p-3 text-center rounded bg-light">
                                        <div class="fw-bold">{{ $product->sku }}</div>
                                        <div>{{ $product->name }}</div>
                                        <div class="text-muted">Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                        </div>
                                        <button class="btn btn-sm btn-primary mt-2 add-to-cart">Add</button>
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
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <ul id="cartItems" class="list-group mb-3"
                            style="min-height: 60vh; max-height: 350px; overflow-y: auto;">
                            @php
                                $cart = session('cart', []);
                                $grandTotal = 0;
                            @endphp

                            @foreach ($cart as $sku => $item)
                                @php
                                    $totalPrice = $item['quantity'] * $item['unit_price'];
                                    $grandTotal += $totalPrice;
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $item['name'] }}</strong>
                                        <div>
                                            Qty:
                                            <input type="number" class="form-control form-control-sm quantity-input"
                                                data-sku="{{ $sku }}" value="{{ $item['quantity'] }}"
                                                min="1" style="width: 60px; display: inline-block;">
                                            x Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div>
                                        <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                        <button class="btn btn-sm btn-warning ms-2 minus-item"
                                            data-sku="{{ $sku }}">-</button>
                                        <button class="btn btn-sm btn-danger ms-2 remove-item"
                                            data-sku="{{ $sku }}">X</button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="cartTotal">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <form action="{{ route('pos.payment') }}" method="GET" id="paymentForm">
                            @csrf
                            <input type="hidden" name="cart" id="cartInput" value="{{ json_encode($cart) }}">
                            <button type="submit" id="checkoutButton" class="btn btn-success" disabled>Checkout</button>
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
            const cartItems = $('#cartItems');
            const cartTotal = $('#cartTotal');
            const checkoutButton = $('#checkoutButton');
            const clearCartButton = $('#clearCartButton');
            const cartInput = $('#cartInput');
            const grandTotalInput = $('#grandTotalInput');

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

                    $(this).toggle(matchesSearch && matchesCategory);
                });
            }

            // Function to update the checkout button state
            function updateCheckoutButton() {
                const cart = JSON.parse(cartInput.val() || '{}'); // Parse the cart from the hidden input
                const isCartEmpty = Object.keys(cart).length === 0; // Check if the cart is empty
                checkoutButton.prop('disabled', isCartEmpty); // Enable/disable the button
            }

            // Update cart UI
            function updateCartUI(cart) {
                cartItems.empty();
                let total = 0;

                Object.keys(cart).forEach((sku) => {
                    const item = cart[sku];
                    const subtotal = item.unit_price * item.quantity;
                    total += subtotal;

                    const cartItem = $(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${item.name}</strong>
                                <div>
                                    Qty: 
                                    <input type="number" class="form-control form-control-sm quantity-input" 
                                        data-sku="${sku}" value="${item.quantity}" min="1" style="width: 60px; display: inline-block;">
                                    x Rp ${item.unit_price.toLocaleString('id-ID')}
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
                cartInput.val(JSON.stringify(cart)); // Update cart input
                updateCheckoutButton(); // Update the checkout button state
            }

            // Add product to cart
            function addToCart(productElement) {
                const name = productElement.data('name');
                const sku = productElement.data('sku');
                const unitPrice = parseFloat(productElement.find('.text-muted').text().replace(/[^\d]/g, ''));

                $.post('{{ route('pos.addToCart') }}', {
                    _token: '{{ csrf_token() }}',
                    sku,
                    name,
                    unit_price: unitPrice,
                    quantity: 1,
                }).done((response) => {
                    updateCartUI(response.cart);
                });
            }

            // Clear all items from the cart
            function clearCart() {
                $.post('{{ route('pos.clearCart') }}', {
                    _token: '{{ csrf_token() }}',
                }).done(() => {
                    updateCartUI({});
                });
            }

            // Event listeners
            $('#productSearch').on('keyup', filterProducts);
            $('#categoryFilter').on('change', filterProducts);

            $('#productGrid').on('click', '.add-to-cart', function() {
                const productElement = $(this).closest('.product-item');
                addToCart(productElement);
            });

            clearCartButton.on('click', clearCart);

            cartItems.on('change', '.quantity-input', function() {
                const sku = $(this).data('sku');
                const newQuantity = parseInt($(this).val());

                if (newQuantity > 0) {
                    $.post('{{ route('pos.addToCart') }}', {
                        _token: '{{ csrf_token() }}',
                        sku,
                        quantity: newQuantity,
                    }).done((response) => {
                        updateCartUI(response.cart);
                    });
                } else {
                    $.post('{{ route('pos.removeFromCart') }}', {
                        _token: '{{ csrf_token() }}',
                        sku,
                    }).done((response) => {
                        updateCartUI(response.cart);
                    });
                }
            });

            cartItems.on('click', '.minus-item', function() {
                const sku = $(this).data('sku');
                const quantityInput = $(this).closest('li').find('.quantity-input');
                const currentQuantity = parseInt(quantityInput.val());

                if (currentQuantity === 1) {
                    // If quantity is 1, remove the item from the cart
                    $.post('{{ route('pos.removeFromCart') }}', {
                        _token: '{{ csrf_token() }}',
                        sku,
                    }).done((response) => {
                        updateCartUI(response.cart);
                    });
                } else {
                    // Otherwise, decrement the quantity
                    $.post('{{ route('pos.addToCart') }}', {
                        _token: '{{ csrf_token() }}',
                        sku,
                        quantity: -1,
                    }).done((response) => {
                        updateCartUI(response.cart);
                    });
                }
            });

            cartItems.on('click', '.remove-item', function() {
                const sku = $(this).data('sku');
                $.post('{{ route('pos.removeFromCart') }}', {
                    _token: '{{ csrf_token() }}',
                    sku,
                }).done((response) => {
                    updateCartUI(response.cart);
                });
            });

            updateCheckoutButton(); // Update the checkout button state
        });
    </script>
@endpush
