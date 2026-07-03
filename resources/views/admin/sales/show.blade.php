<x-appadmin-layout>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1>Sale Detail</h1>
                <p class="mb-0">Kode: {{ $sale->kode }}</p>
            </div>
            <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Customer:</strong>
                        <div>{{ $sale->customer->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <strong>Tanggal:</strong>
                        <div>{{ $sale->sale_date->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <strong>Total:</strong>
                        <div>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->nama_barang }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-appadmin-layout>