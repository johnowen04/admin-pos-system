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
        @if (Auth::user()->employee && (!Auth::user()->employee->outlets || Auth::user()->employee->outlets->isEmpty()))
            <div class="card">
                <div class="card-body">
                    <div class="empty-state text-center py-5">
                        <div class="empty-state-icon">
                            <i class="fa fa-store-alt-slash fa-3x text-muted"></i>
                        </div>
                        <h4 class="mt-4">No Outlet Available</h4>
                        <p class="text-muted">
                            You don't have any outlets assigned to your account.
                            <br>Please contact an administrator to assign you to an outlet.
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary me-2">
                                <i class="fa fa-home me-1"></i> Return to Dashboard
                            </a>
                            @can('outlet.view')
                                <a href="{{ route('outlet.index') }}" class="btn btn-outline-primary">
                                    <i class="fa fa-store me-1"></i> Manage Outlets
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @elseif (session('selected_outlet_id') === 'all')
            <!-- Outlet selection message when "All Outlets" is selected -->
            <div class="card">
                <div class="card-body">
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fa fa-store fa-3x text-primary"></i>
                        </div>
                        <h4>Please Select an Outlet</h4>
                        <p class="text-muted mb-4">
                            You currently have "All Outlets" selected. For POS operations,
                            please select a specific outlet to proceed.
                        </p>

                        <form action="{{ route('select-outlet.select') }}" method="POST" class="d-inline">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-6 col-lg-4">
                                    <div class="input-group mb-3 gap-3">
                                        <select name="id" class="form-select" required>
                                            <option value="" selected disabled>-- Select an Outlet --</option>
                                            @foreach ($outlets ?? [] as $outlet)
                                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-check-circle me-1"></i> Select
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="mt-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Return to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex gap-2">
                                <select id="categoryFilter" class="form-select" style="width: 200px;">
                                    <option value="">All Categories</option>
                                    @if (!isset($categories) || $categories->isEmpty())
                                        <option value="" disabled>No categories available</option>
                                    @else
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                <input type="text" id="productSearch" class="form-control"
                                    placeholder="Search Products...">
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="productGrid" class="row g-4" style="max-height: 80vh; overflow-y: auto;">
                                @if (!isset($products) || empty($products))
                                    <div class="col-12">
                                        <div class="empty-state text-center py-5">
                                            <div class="empty-state-icon">
                                                <i class="fa fa-search fa-3x text-muted"></i>
                                            </div>
                                            <h4 class="mt-4">No Products Available</h4>
                                            <p class="text-muted">
                                                There are no products available in this outlet.
                                                @if (session('selected_outlet_id') != 'all')
                                                    <br>Try selecting a different outlet or check inventory levels.
                                                @endif
                                            </p>
                                            @can('product.create')
                                                <div class="mt-3">
                                                    <a href="{{ route('product.create') }}" class="btn btn-primary me-2"
                                                        target="_blank">
                                                        <i class="fa fa-plus me-1"></i> Add New Product
                                                    </a>
                                                    <a href="{{ route('inventory.create') }}" class="btn btn-secondary"
                                                        target="_blank">
                                                        <i class="fa fa-warehouse me-1"></i> Add Stock
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                @else
                                    @foreach ($products as $product)
                                        <div class="col-6 col-md-4 col-lg-3 product-item" data-id="{{ $product['id'] }}"
                                            data-name="{{ $product['name'] }}" data-sku="{{ $product['sku'] }}"
                                            data-category="{{ $product['category_id'] }}"
                                            data-initial-stock="{{ $product['quantity'] }}">
                                            <div class="border p-3 text-center rounded bg-light position-relative">
                                                <div class="fw-bold">{{ $product['sku'] }}</div>
                                                <div>{{ $product['name'] }}</div>
                                                <div class="text-muted unit-price">
                                                    Rp {{ number_format($product['sell_price'], 0, ',', '.') }}
                                                </div>
                                                <div class="text-muted mt-1">
                                                    Stock: <span
                                                        class="fw-bold stock-count">{{ $product['quantity'] }}</span>
                                                </div>

                                                <button id="addToCart"
                                                    class="btn btn-sm mt-1 add-to-cart {{ $product['quantity'] > 0 ? 'btn-primary' : 'btn-danger' }}"
                                                    {{ $product['quantity'] <= 0 ? 'disabled' : '' }}>
                                                    {{ $product['quantity'] > 0 ? 'Add' : 'Out of Stock' }}
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Current Cart</h4>
                                    @if (isset($invoiceNumber) && $invoiceNumber)
                                        <span class="d-block fw-bold">{{ $invoiceNumber }}</span>
                                    @else
                                        <span class="d-block text-muted"><em>Invoice number will be generated at
                                                checkout</em></span>
                                    @endif
                                </div>
                                <button id="clearCartButton" class="btn btn-danger btn-sm">Clear Cart</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <ul id="cartItems" class="list-group list-group-flush">
                                @if (!empty($cart) && count($cart) > 0)
                                    @foreach ($cart as $id => $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">{{ $item['name'] }}</div>
                                                <div>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-sm btn-outline-primary minus-item"
                                                    data-id="{{ $id }}">-</button>
                                                <input type="number"
                                                    class="form-control form-control-sm mx-2 quantity-input"
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
                                        empty($cart)
                                            ? 0
                                            : array_sum(
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
                                <input type="hidden" name="cart" id="cartInput"
                                    value="{{ json_encode($cart ?? []) }}">
                                <button type="submit" id="checkoutButton" class="btn btn-success w-100 mt-2"
                                    {{ empty($cart) || count($cart) === 0 ? 'disabled' : '' }}>
                                    Checkout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @if (session()->get('selected_outlet_id'))
        <script>
            window.initialCart = @json($cart);
            window.csrfToken = '{{ csrf_token() }}';
            window.routes = {
                addToCart: '{{ route('pos.addToCart') }}',
                removeFromCart: '{{ route('pos.removeFromCart') }}',
                clearCart: '{{ route('pos.clearCart') }}'
            };
        </script>
        <script src="{{ asset('assets/js/app/pos.js') }}"></script>
    @endif
@endpush
