<div>
    <div class="flex gap-4 mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search products..."
            class="border rounded px-3 py-2 w-full"
        >

        <select wire:model.live="categoryFilter" class="border rounded px-3 py-2">
            <option value="">All Categories</option>
            <option value="chair">Chair</option>
            <option value="table">Table</option>
            <option value="bed">Bed</option>
            <option value="cabinet">Cabinet</option>
            <option value="outdoor">Outdoor</option>
            <option value="other">Other</option>
        </select>
    </div>

    <table class="w-full border-collapse">
        <thead>
            <tr class="text-left border-b">
                <th class="py-2">Name</th>
                <th class="py-2">Category</th>
                <th class="py-2">Tagged Price</th>
                <th class="py-2">Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr class="border-b">
                    <td class="py-2">{{ $product->name }}</td>
                    <td class="py-2">{{ ucfirst($product->category) }}</td>
                    <td class="py-2">₱{{ number_format($product->tagged_price, 2) }}</td>
                    <td class="py-2">
                        {{ $product->quantity_in_stock }}
                        @if ($product->quantity_in_stock <= 5)
                            <span class="text-red-600 text-sm ml-1">(Low stock)</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>