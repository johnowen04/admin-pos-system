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
            <!-- Left Column: Products -->
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
                        <!-- Product Grid -->
                        <div id="productGrid" class="row g-4" style="max-height: 350px; overflow-y: auto;">
                            @foreach ($products as $product)
                                <div class="col-6 col-md-4 col-lg-3 product-item" data-id="{{ $product['id'] }}"
                                    data-name="{{ $product['name'] }}" data-sku="{{ $product['sku'] }}"
                                    data-category="{{ $product['categories_id'] }}"
                                    data-initial-stock="{{ $product['quantity'] }}">
                                    <div class="border p-3 text-center rounded bg-light position-relative">
                                        <div class="fw-bold">{{ $product['sku'] }}</div>
                                        <div>{{ $product['name'] }}</div>
                                        <div class="text-muted unit-price">
                                            Rp {{ number_format($product['sell_price'], 0, ',', '.') }}
                                        </div>
                                        <div class="text-muted mt-1">
                                            Stock: <span class="fw-bold stock-count">{{ $product['quantity'] }}</span>
                                        </div>

                                        <button id="addToCart"
                                            class="btn btn-sm mt-1 add-to-cart {{ $product['quantity'] > 0 ? 'btn-primary' : 'btn-danger' }}"
                                            {{ $product['quantity'] <= 0 ? 'disabled' : '' }}>
                                            {{ $product['quantity'] > 0 ? 'Add' : 'Out of Stock' }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Cart -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Current Cart</h4>
                                <span class="d-block fw-bold">{{ $invoiceNumber }}</span>
                            </div>
                            <button id="clearCartButton" class="btn btn-danger btn-sm">Clear Cart</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Cart Items List -->
                        <ul id="cartItems" class="list-group list-group-flush">
                            @if (count($cart) > 0)
                                @foreach ($cart as $id => $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{ $item['name'] }}</div>
                                            <div>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-sm btn-outline-primary minus-item"
                                                data-id="{{ $id }}">-</button>
                                            <input type="number" class="form-control form-control-sm mx-2 quantity-input"
                                                style="width: 60px;" value="{{ $item['quantity'] }}" min="1"
                                                data-id="{{ $id }}">
                                            <button class="btn btn-sm btn-outline-primary plus-item"
                                                data-id="{{ $id }}">+</button>
                                            <button class="btn btn-sm btn-outline-danger ms-2 remove-item"
                                                data-id="{{ $id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <li class="list-group-item text-center">No items in cart</li>
                            @endif
                        </ul>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <h5>Total:</h5>
                            <h5 id="cartTotal">Rp
                                {{ number_format(
                                    array_sum(
                                        array_map(function ($item) {
                                            return $item['quantity'] * $item['unit_price'];
                                        }, $cart),
                                    ),
                                    0,
                                    ',',
                                    '.',
                                ) }}
                            </h5>
                        </div>
                        <form action="{{ route('pos.payment') }}" method="GET" id="paymentForm">
                            @csrf
                            <input type="hidden" name="cart" id="cartInput" value="{{ json_encode($cart) }}">
                            <button type="submit" id="checkoutButton" class="btn btn-success w-100 mt-2"
                                {{ count($cart) === 0 ? 'disabled' : '' }}>
                                Checkout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const productGrid = document.getElementById('productGrid');
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');
            const checkoutButton = document.getElementById('checkoutButton');
            const clearCartButton = document.getElementById('clearCartButton');
            const cartInput = document.getElementById('cartInput');
            const productSearch = document.getElementById('productSearch');
            const categoryFilter = document.getElementById('categoryFilter');

            // Initialize cart from server data
            let cart = @json($cart);

            // =============== PRODUCT FILTERING ===============

            /**
             * Filter products based on search term and category
             */
            function filterProducts() {
                const searchTerm = productSearch.value.toLowerCase();
                const selectedCategory = categoryFilter.value;

                Array.from(productGrid.querySelectorAll('.product-item')).forEach(productElement => {
                    const sku = productElement.dataset.sku.toLowerCase();
                    const name = productElement.dataset.name.toLowerCase();
                    const category = productElement.dataset.category.toString();

                    const matchesSearch = name.includes(searchTerm) || sku.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category === selectedCategory;

                    productElement.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
                });
            }

            // =============== STOCK MANAGEMENT ===============

            /**
             * Update the stock UI for all products based on the cart contents
             */
            function updateStockFromCart(cart) {
                Array.from(productGrid.querySelectorAll('.product-item')).forEach(productElement => {
                    const productId = productElement.dataset.id;
                    const initialStock = parseInt(productElement.dataset.initialStock);
                    const stockElement = productElement.querySelector('.stock-count');
                    const addToCartButton = productElement.querySelector('.add-to-cart');

                    // Calculate remaining stock
                    const cartItem = cart[productId];
                    const quantityInCart = cartItem ? cartItem.quantity : 0;
                    const remainingStock = initialStock - quantityInCart;

                    // Update stock count display
                    stockElement.textContent = remainingStock;

                    // Update button state and UI
                    if (remainingStock <= 0) {
                        addToCartButton.disabled = true;
                        addToCartButton.textContent = 'Out of Stock';
                        addToCartButton.classList.add('btn-danger');
                        addToCartButton.classList.remove('btn-primary');
                    } else {
                        addToCartButton.disabled = false;
                        addToCartButton.textContent = 'Add';
                        addToCartButton.classList.add('btn-primary');
                        addToCartButton.classList.remove('btn-danger');
                    }
                });
            }

            // =============== CART MANAGEMENT ===============

            /**
             * Add a product to the cart
             */
            function addToCart(productId) {
                const productElement = document.querySelector(`.product-item[data-id="${productId}"]`);
                const name = productElement.dataset.name;
                const unitPrice = parseInt(productElement.querySelector('.unit-price').textContent
                    .replace(/\D/g, ''));

                // Send request to server
                fetch('{{ route('pos.addToCart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: productId,
                            name,
                            unit_price: unitPrice,
                            quantity: 1,
                            type: 'increment',
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        cart = data.cart;
                        updateCartUI(cart);
                        updateStockFromCart(cart);
                    })
                    .catch(error => {
                        console.error('Error adding to cart:', error);
                    });
            }

            /**
             * Update cart quantity
             */
            function updateCartQuantity(productId, quantity, type = 'increment') {
                fetch('{{ route('pos.addToCart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: productId,
                            quantity,
                            type: type,
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        cart = data.cart;
                        updateCartUI(cart);
                        updateStockFromCart(cart);
                    })
                    .catch(error => {
                        console.error('Error updating cart:', error);
                    });
            }

            /**
             * Remove item from cart
             */
            function removeFromCart(productId) {
                fetch('{{ route('pos.removeFromCart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: productId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        cart = data.cart;
                        updateCartUI(cart);
                        updateStockFromCart(cart);
                    })
                    .catch(error => {
                        console.error('Error removing from cart:', error);
                    });
            }

            /**
             * Clear the entire cart
             */
            function clearCart() {
                fetch('{{ route('pos.clearCart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        cart = {};
                        updateCartUI(cart);
                        updateStockFromCart(cart);
                    })
                    .catch(error => {
                        console.error('Error clearing cart:', error);
                    });
            }

            /**
             * Update the cart UI
             */
            function updateCartUI(cart) {
                // Clear cart items
                cartItems.innerHTML = '';

                // Calculate total
                let total = 0;

                // Check if cart is empty
                if (Object.keys(cart).length === 0) {
                    const li = document.createElement('li');
                    li.className = 'list-group-item text-center';
                    li.textContent = 'No items in cart';
                    cartItems.appendChild(li);
                    checkoutButton.disabled = true;
                } else {
                    // Add each item to cart UI
                    Object.entries(cart).forEach(([id, item]) => {
                        const subtotal = item.unit_price * item.quantity;
                        total += subtotal;

                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.innerHTML = `
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">${item.name}</div>
                        <div>Rp ${new Intl.NumberFormat('id-ID').format(item.unit_price)}</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-primary minus-item" data-id="${id}">-</button>
                        <input type="number" class="form-control form-control-sm mx-2 quantity-input" 
                               style="width: 60px;" value="${item.quantity}" min="1" data-id="${id}">
                        <button class="btn btn-sm btn-outline-primary plus-item" data-id="${id}">+</button>
                        <button class="btn btn-sm btn-outline-danger ms-2 remove-item" data-id="${id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                        cartItems.appendChild(li);
                    });

                    checkoutButton.disabled = false;
                }

                // Update total display
                cartTotal.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;

                // Update hidden input for form submission
                cartInput.value = JSON.stringify(cart);
            }

            // =============== EVENT LISTENERS ===============

            // Filter products on search input or category change
            productSearch.addEventListener('input', filterProducts);
            categoryFilter.addEventListener('change', filterProducts);

            // Add product to cart when "Add" button is clicked
            productGrid.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-to-cart') && !e.target.disabled) {
                    const productId = e.target.closest('.product-item').dataset.id;
                    addToCart(productId);
                }
            });

            // Handle cart item quantity changes
            cartItems.addEventListener('click', function(e) {
                const cartItem = e.target.closest('li');
                if (!cartItem) return;

                const productId = e.target.dataset.id;
                const productElement = document.querySelector(`.product-item[data-id="${productId}"]`);


                if (e.target.classList.contains('plus-item')) {
                    const stockElement = productElement.querySelector('.stock-count');
                    const remainingStock = parseInt(stockElement.textContent);

                    if (remainingStock > 0) {
                        updateCartQuantity(productId, 1); // Increment quantity by 1
                    } else {
                        alert('No more stock available for this product.');
                    }
                } else if (e.target.classList.contains('minus-item')) {
                    const input = cartItem.querySelector('.quantity-input');
                    const newQuantity = parseInt(input.value) - 1;

                    if (newQuantity <= 0) {
                        removeFromCart(productId);
                    } else {
                        updateCartQuantity(productId, -1);
                    }
                } else if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                    removeFromCart(productId);
                }
            });

            let lastValue = null; // Variable to store the last value

            cartItems.addEventListener('focusin', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    lastValue = parseInt(e.target
                        .value); // Store the current value when the input gains focus
                }
            });

            // Handle direct input of quantity
            cartItems.addEventListener('change', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    const productId = e.target.dataset.id;
                    const productElement = document.querySelector(`.product-item[data-id="${productId}"]`);
                    const stockElement = productElement.querySelector('.stock-count');
                    const remainingStock = parseInt(stockElement.textContent);
                    const newQuantity = parseInt(e.target.value);

                    if (newQuantity <= 0) {
                        removeFromCart(productId);
                    } else {
                        console.log(remainingStock, newQuantity, lastValue);
                        if (remainingStock >= (newQuantity - lastValue)) {
                            updateCartQuantity(productId, newQuantity, 'update');
                        } else {
                            alert('Not enough stock available for this product.');
                            e.target.value = lastValue; // Reset to 1 if stock is insufficient
                        }
                    }
                }
            });

            // Clear entire cart
            clearCartButton.addEventListener('click', clearCart);

            // Initialize stock UI based on cart contents
            updateStockFromCart(cart);
        });
    </script>
@endpush
