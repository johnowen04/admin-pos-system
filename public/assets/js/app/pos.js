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

    // Initialize cart from global variable (will be defined in the Blade template)
    let cart = window.initialCart || {};

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
        fetch(window.routes.addToCart, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
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
        fetch(window.routes.addToCart, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
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
        fetch(window.routes.removeFromCart, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
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
        fetch(window.routes.clearCart, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
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
            lastValue = parseInt(e.target.value); // Store the current value when the input gains focus
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
                    e.target.value = lastValue; // Reset to previous value if stock is insufficient
                }
            }
        }
    });

    // Clear entire cart
    clearCartButton.addEventListener('click', clearCart);

    // Initialize stock UI based on cart contents
    updateStockFromCart(cart);
});