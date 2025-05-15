@extends('layouts.app')

@section('title', 'POS Payment')

@section('content')
    <div class="page-inner">
        <div class="card shadow-lg" style="width: 500px; background-color: white; border-radius: 10px;">
            <div class="card-body">
                <div class="receipt-container">
                    <div class="text-center mb-4">
                        <h4 class="mb-1">Payment Receipt</h4>
                        <small class="text-muted">Outlet: {{ Auth::user()->employee->outlets[0]->name }}</small><br>
                        <small class="text-muted">Cashier: {{ Auth::user()->employee->name }}</small><br>
                        <small class="text-muted">Date: {{ now()->format('d M Y H:i') }}</small>
                    </div>

                    <table class="table table-borderless table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-center">{{ $item['quantity'] }}</td>
                                    <td class="text-end">Rp
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Total Products</strong>
                            <span>{{ array_sum(array_column($cart, 'quantity')) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Total Payment</strong>
                            <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <strong>Invoice Number:</strong> <span class="fw-bold">{{ $invoiceNumber }}</span>
                    </div>

                    <div class="text-center mt-4 no-print">
                        <button class="btn btn-primary btn-sm" onclick="window.print()">Print Receipt</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        @media print {
            .no-print {
                display: none;
                /* Hide elements with the 'no-print' class during printing */
            }
        }
    </style>
@endsection
