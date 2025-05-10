<div class="container py-4">
    <div class="text-center mb-4">
        <h4 class="mb-1">Struk Pembayaran</h4>
        <small class="text-muted">Outlet ID: {{ $outletId }}</small><br>
        <small class="text-muted">Tanggal: {{ now()->format('d M Y H:i') }}</small>
    </div>

    <table class="table table-borderless table-sm">
        <thead class="border-bottom">
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cart as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-center">{{ $item['quantity'] }}</td>
                    <td class="text-end">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-top pt-3">
        <div class="d-flex justify-content-between">
            <strong>Total Produk</strong>
            <span>{{ array_sum(array_column($cart, 'quantity')) }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <strong>Total Pembayaran</strong>
            <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
        </div>
    </div>
</div>