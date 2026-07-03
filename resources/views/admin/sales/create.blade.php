<x-appadmin-layout>
    <div class="container mt-5">
        <h1>Create Sale</h1>
        @include('admin.sales._form', [
            'action' => route('admin.sales.store'),
            'method' => 'POST',
            'buttonLabel' => 'Create Sale',
            'customers' => $customers,
            'products' => $products,
        ])
    </div>
</x-appadmin-layout>