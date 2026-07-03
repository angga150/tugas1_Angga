<x-appadmin-layout>
    <div class="container mt-5">
        <h1>Sales</h1>
        <p>Daftar transaksi penjualan</p>
        <a href="{{ route('admin.sales.create') }}" class="btn btn-primary mb-3">Add Sale</a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Customer</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Item</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->kode }}</td>
                        <td>{{ $sale->customer->name }}</td>
                        <td>{{ $sale->sale_date->format('d M Y') }}</td>
                        <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                        <td>{{ $sale->items->count() }}</td>
                        <td>
                            <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('admin.sales.edit', $sale->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.sales.destroy', $sale->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-appadmin-layout>