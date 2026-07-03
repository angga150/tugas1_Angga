<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('customer', 'items')->orderBy('id')->get();
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Products::all();
        return view('admin.sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'sale_date'             => 'required|date',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $lastSale = Sale::where('kode', 'like', 'TRX-SAMPLE-%')->orderByDesc('id')->first();
            $nextNumber = $lastSale ? ((int) substr($lastSale->kode, -3)) + 1 : 1;
            $kode = 'TRX-SAMPLE-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'kode'         => $kode,
                'customer_id'  => $request->customer_id,
                'sale_date'    => $request->sale_date,
                'total_amount' => 0,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Products::findOrFail($item['product_id']);
                $qty     = (int) $item['quantity'];
                $price   = $product->harga;
                $subtotal = $qty * $price;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'price'      => $price,
                    'subtotal'   => $subtotal,
                ]);

                $total += $subtotal;
            }

            $sale->update(['total_amount' => $total]);
        });

        return redirect()->route('admin.sales.index')->with('success', 'Sale created successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load('customer', 'items.product');
        return view('admin.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $sale->load('items');
        $customers = Customer::all();
        $products  = Products::all();
        return view('admin.sales.edit', compact('sale', 'customers', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'sale_date'             => 'required|date',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $sale) {
            $sale->update([
                'customer_id' => $request->customer_id,
                'sale_date'   => $request->sale_date,
            ]);

            $sale->items()->delete();

            $total = 0;
            foreach ($request->items as $item) {
                $product = Products::findOrFail($item['product_id']);
                $qty     = (int) $item['quantity'];
                $price   = $product->harga;
                $subtotal = $qty * $price;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'price'      => $price,
                    'subtotal'   => $subtotal,
                ]);

                $total += $subtotal;
            }

            $sale->update(['total_amount' => $total]);
        });

        return redirect()->route('admin.sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('admin.sales.index')->with('success', 'Sale deleted successfully.');
    }
}