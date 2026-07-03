@php
    $selectedCustomerId = old('customer_id', isset($sale) ? $sale->customer_id : '');

    $formItems = old('items');
    if (!$formItems && isset($sale)) {
        $formItems = $sale->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
            ];
        })->toArray();
    }
    $formItems = $formItems ?? [];
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-3">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-select" required>
            <option value="">-- Pilih Customer --</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ $selectedCustomerId == $customer->id ? 'selected' : '' }}>
                    {{ $customer->name }}
                </option>
            @endforeach
        </select>
        @error('customer_id')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal</label>
        <input type="date" name="sale_date" class="form-control"
               value="{{ old('sale_date', isset($sale) ? $sale->sale_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('sale_date')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <hr>
    <h5>Item Penjualan</h5>

    <table class="table table-bordered align-middle" id="items-table">
        <thead>
            <tr>
                <th style="width:35%">Produk</th>
                <th style="width:15%">Qty</th>
                <th style="width:20%">Harga</th>
                <th style="width:20%">Subtotal</th>
                <th style="width:10%"></th>
            </tr>
        </thead>
        <tbody id="items-body">
            @foreach($formItems as $i => $item)
                <tr class="item-row">
                    <td>
                        <select name="items[{{ $i }}][product_id]" class="form-select product-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->harga }}"
                                    {{ ($item['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                    {{ $product->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[{{ $i }}][quantity]" class="form-control qty-input"
                               value="{{ $item['quantity'] ?? 1 }}" min="1" required>
                    </td>
                    <td class="price-cell">0</td>
                    <td class="subtotal-cell">0</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button type="button" id="add-row" class="btn btn-secondary btn-sm mb-3">+ Tambah Item</button>

    @error('items')
        <div class="text-danger mb-2">{{ $message }}</div>
    @enderror

    <div class="mb-3 text-end">
        <strong>Total: Rp <span id="grand-total">0</span></strong>
    </div>

    <button type="submit" class="btn btn-primary">{{ $buttonLabel }}</button>
    <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">Batal</a>
</form>

<template id="row-template">
    <tr class="item-row">
        <td>
            <select name="items[__INDEX__][product_id]" class="form-select product-select" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->harga }}">
                        {{ $product->nama_barang }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[__INDEX__][quantity]" class="form-control qty-input" value="1" min="1" required>
        </td>
        <td class="price-cell">0</td>
        <td class="subtotal-cell">0</td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
    let rowIndex = {{ count($formItems) }};

    function formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function recalcRow(row) {
        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.qty-input');
        const priceCell = row.querySelector('.price-cell');
        const subtotalCell = row.querySelector('.subtotal-cell');

        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption ? parseFloat(selectedOption.dataset.price || 0) : 0;
        const qty = parseInt(qtyInput.value || 0);
        const subtotal = price * qty;

        priceCell.textContent = formatRupiah(price);
        subtotalCell.textContent = formatRupiah(subtotal);

        return subtotal;
    }

    function recalcAll() {
        let total = 0;
        document.querySelectorAll('#items-body .item-row').forEach(row => {
            total += recalcRow(row);
        });
        document.getElementById('grand-total').textContent = formatRupiah(total);
    }

    document.getElementById('items-body').addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select') || e.target.classList.contains('qty-input')) {
            recalcAll();
        }
    });

    document.getElementById('items-body').addEventListener('input', function (e) {
        if (e.target.classList.contains('qty-input')) {
            recalcAll();
        }
    });

    document.getElementById('items-body').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            recalcAll();
        }
    });

    document.getElementById('add-row').addEventListener('click', function () {
        const template = document.getElementById('row-template').innerHTML.replaceAll('__INDEX__', rowIndex);
        document.getElementById('items-body').insertAdjacentHTML('beforeend', template);
        rowIndex++;
    });

    document.addEventListener('DOMContentLoaded', recalcAll);
</script>
@endpush