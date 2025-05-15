@extends('layouts.app')

@section('title', 'POS Payment')

@section('content')
    <div class="page-inner">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Payment</h4>
        </div>

        <!-- Payment Summary and Method -->
        <div class="row">
            <!-- Left Section: Payment Info -->
            <div class="col-md-8">
                <div class="row mb-3">
                    <!-- Total Tagihan -->
                    <div class="col-md-12">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="card-title"><strong>Total Bill</strong></div>
                                <div class="card-text fs-4">Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sisa Tagihan -->
                    <div class="col-md-6" hidden>
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="card-title text-danger"><strong>Remaining Bill</strong></div>
                                <div class="card-text fs-4 text-danger"><strong>Rp
                                        {{ number_format($grandTotal, 0, ',', '.') }}</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Kembalian -->
                    <div class="col-md-4" hidden>
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="card-title"><strong>Kembalian</strong></div>
                                <div class="card-text fs-4">Rp 0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="row" hidden>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button class="btn btn-light border">Tunai</button>
                            <button class="btn btn-light border">Nontunai</button>
                            <button class="btn btn-light border">Transfer</button>
                            <button class="btn btn-light border">Lainnya</button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-grid gap-2">
                            <button class="btn btn-light border">Uang Pas</button>
                            <button class="btn btn-light border">Rp 10.000</button>
                            <button class="btn btn-light border">Rp 100.000</button>
                            <button class="btn btn-light border">Rp 200.000</button>
                            <button class="btn btn-light border">Lainnya</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section: Cart Overview -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>Cart</span>
                        <span class="fw-bold">{{ $invoiceNumber }}</span>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($cart as $sku => $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item['name'] }}</strong>
                                    <div>Qty: {{ $item['quantity'] }} x Rp
                                        {{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                                </div>
                                <div>
                                    <span>Rp
                                        {{ number_format($item['quantity'] * $item['unit_price'], 0, ',', '.') }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="card-footer d-flex justify-content-between">
                        <strong>Total {{ array_sum(array_column($cart, 'quantity')) }} Produk</strong>
                        <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <form action="{{ route('pos.processPayment') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 mt-3">Process</button>
                </form>
            </div>
        </div>
    </div>
@endsection
