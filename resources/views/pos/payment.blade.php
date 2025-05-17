@extends('layouts.app')

@section('title', 'POS Payment')

@section('content')
    <div class="page-inner">
        <!-- Payment and Order Summary -->
        <div class="row">
            <!-- Left Section: Payment Processing -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Payment Information</h4>
                    </div>

                    <div class="card-body">
                        <!-- Payment Summary Cards -->
                        <div class="row mb-4">
                            <!-- Total Bill Card -->
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Bill</h5>
                                        <h3 class="card-text mb-0">Rp {{ number_format($grandTotal, 0, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount Paid Card -->
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Amount Paid</h5>
                                        <h3 class="card-text mb-0" id="amountPaidDisplay">Rp 0</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Change Card -->
                            <div class="col-md-12">
                                <div class="card shadow-sm bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Change</h5>
                                        <h2 class="card-text mb-0" id="changeAmount">Rp 0</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <form id="paymentForm" action="{{ route('pos.processPayment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="cart" value="{{ json_encode($cart) }}">
                            <input type="hidden" name="grand_total" value="{{ $grandTotal }}">

                            <!-- Payment Method Selection -->
                            <div class="form-group mb-4">
                                <label class="form-label">Payment Method</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="cashPayment" value="cash" checked>
                                        <label class="form-check-label" for="cashPayment">Cash</label>
                                    </div>
                                    @if (false)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="cardPayment" value="card">
                                            <label class="form-check-label" for="cardPayment">Card</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="transferPayment" value="transfer">
                                            <label class="form-check-label" for="transferPayment">Transfer</label>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Amount Input -->
                            <div class="form-group mb-4">
                                <label for="amountPaid" class="form-label">Amount Paid</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control form-control-lg" id="amountPaid"
                                        name="amount_paid" min="{{ $grandTotal }}" value="{{ $grandTotal }}" required>
                                </div>
                            </div>

                            <!-- Quick Amounts -->
                            <div class="mb-4">
                                <label class="form-label">Quick Amounts</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-primary quick-amount"
                                        data-amount="{{ $grandTotal }}">Exact</button>
                                    <button type="button" class="btn btn-outline-primary quick-amount"
                                        data-amount="{{ $grandTotal + 1000 }}">+1K</button>
                                    <button type="button" class="btn btn-outline-primary quick-amount"
                                        data-amount="{{ $grandTotal + 5000 }}">+5K</button>
                                    <button type="button" class="btn btn-outline-primary quick-amount"
                                        data-amount="{{ $grandTotal + 10000 }}">+10K</button>
                                    <button type="button" class="btn btn-outline-primary quick-amount"
                                        data-amount="{{ $grandTotal + 20000 }}">+20K</button>
                                    <button type="button" class="btn btn-outline-primary quick-amount"
                                        data-amount="{{ $grandTotal + 50000 }}">+50K</button>
                                    <button type="button" class="btn btn-outline-primary quick-amount"
                                        data-amount="{{ ceil($grandTotal / 100000) * 100000 }}">Round</button>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('pos.index') }}" class="btn btn-danger">Cancel</a>
                                <button type="submit" class="btn btn-success btn-lg">Complete Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Section: Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($cart as $id => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $item['name'] }}</div>
                                        <div class="text-muted">
                                            {{ $item['quantity'] }} x Rp
                                            {{ number_format($item['unit_price'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <span>Rp
                                        {{ number_format($item['quantity'] * $item['unit_price'], 0, ',', '.') }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center">No items in cart</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Total:</h5>
                            <h5>Rp {{ number_format($grandTotal, 0, ',', '.') }}</h5>
                        </div>
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
            const amountPaidInput = document.getElementById('amountPaid');
            const amountPaidDisplay = document.getElementById('amountPaidDisplay');
            const changeAmountDisplay = document.getElementById('changeAmount');
            const quickAmountButtons = document.querySelectorAll('.quick-amount');
            const paymentForm = document.getElementById('paymentForm');

            // Constants
            const grandTotal = {{ $grandTotal }};

            /**
             * Calculate and display change amount
             */
            function calculateChange() {
                const amountPaid = parseFloat(amountPaidInput.value) || 0;
                const change = amountPaid - grandTotal;

                // Update amount paid display
                amountPaidDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(amountPaid)}`;

                // Update change amount display
                changeAmountDisplay.textContent =
                `Rp ${new Intl.NumberFormat('id-ID').format(Math.max(0, change))}`;

                // Add or remove success highlight based on sufficient payment
                if (amountPaid >= grandTotal) {
                    changeAmountDisplay.parentElement.classList.add('bg-success', 'text-white');
                    changeAmountDisplay.parentElement.classList.remove('bg-light');
                } else {
                    changeAmountDisplay.parentElement.classList.remove('bg-success', 'text-white');
                    changeAmountDisplay.parentElement.classList.add('bg-light');
                }
            }

            // Set amount from quick amount buttons
            quickAmountButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const amount = parseFloat(this.dataset.amount);
                    amountPaidInput.value = amount;
                    calculateChange();
                });
            });

            // Update change amount when amount paid changes
            amountPaidInput.addEventListener('input', calculateChange);

            // Validate payment form on submit
            paymentForm.addEventListener('submit', function(e) {
                const amountPaid = parseFloat(amountPaidInput.value) || 0;

                if (amountPaid < grandTotal) {
                    e.preventDefault();
                    alert('The payment amount must be at least equal to the total bill.');
                }
            });

            // Initialize change calculation
            calculateChange();
        });
    </script>
@endpush
