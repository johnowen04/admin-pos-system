@extends('layouts.app')

@section('title', 'POS Receipt')

@section('content')
    <div class="page-inner">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Payment Receipt</h4>
                            @if (route($previousRoute) !== route('pos.index'))
                                <span
                                    class="badge @if ($isVoided) bg-danger @else bg-success @endif">{{ $isVoided ? 'Void' : 'Completed' }}</span>
                            @else
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5 class="mb-1 fw-bold">{{ $outletName }}</h5>
                            <small class="text-muted d-block">Cashier:
                                {{ $receiptCreator }}</small>
                            <small class="text-muted d-block">{{ $date }}</small>
                            <div class="mt-2">
                                <span class="fw-bold">Invoice:</span> {{ $invoiceNumber }}
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cart as $item)
                                        <tr>
                                            <td>{{ $item['name'] }}</td>
                                            <td class="text-center">{{ $item['quantity'] }}</td>
                                            <td class="text-end">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">Rp
                                                {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr class="my-3">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <span class="fw-bold">Payment Method:</span>
                                    <span>{{ $paymentMethod ?? 'Cash' }}</span>
                                </div>
                                <div class="mb-1">
                                    <span class="fw-bold">Transaction Date:</span>
                                    <span>{{ $date }}</span>
                                </div>
                                <div>
                                    <span class="fw-bold">Items Count:</span>
                                    <span>{{ array_sum(array_column($cart, 'quantity')) }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold">Total:</span>
                                    <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold">Amount Paid:</span>
                                    <span>Rp {{ number_format($amountPaid ?? $grandTotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Change:</span>
                                    <span>Rp
                                        {{ number_format(($amountPaid ?? $grandTotal) - $grandTotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="text-center mb-3">
                            <p class="mb-1">Thank you for your purchase!</p>
                            <small class="text-muted">Please keep this receipt for your records.</small>
                        </div>
                    </div>

                    <div class="card-footer bg-white no-print mb-2">
                        <div class="d-flex justify-content-between">
                            @if ($previousRoute === 'pos.payment')
                                <a href="{{ route('pos.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-shopping-cart me-1"></i> New Transaction
                                </a>
                            @else
                                <a href="{{ route($previousRoute) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            @endif
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="fas fa-print me-1"></i> Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background-color: white;
                font-size: 12pt;
            }
            .no-print {
                display: none;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .page-inner {
                padding: 0 !important;
            }

            .card-header {
                background-color: white !important;
            }

            hr {
                border-color: #000;
            }
        }
    </style>
@endsection
