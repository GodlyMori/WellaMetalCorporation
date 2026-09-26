<div>
    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block mb-1">Product Name</label>
            <input type="text" wire:model="name" class="border rounded px-3 py-2 w-full">
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">Category</label>
            <select wire:model="category" class="border rounded px-3 py-2 w-full">
                <option value="">-- Select --</option>
                <option value="chair">Chair</option>
                <option value="table">Table</option>
                <option value="bed">Bed</option>
                <option value="cabinet">Cabinet</option>
                <option value="outdoor">Outdoor</option>
                <option value="other">Other</option>
            </select>
            @error('category') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">Description (optional)</label>
            <textarea wire:model="description" class="border rounded px-3 py-2 w-full"></textarea>
        </div>

        <div>
            <label class="block mb-1">Tagged Price (₱)</label>
            <input type="number" step="0.01" wire:model="tagged_price" class="border rounded px-3 py-2 w-full">
            @error('tagged_price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">Starting Quantity</label>
            <input type="number" wire:model="quantity_in_stock" class="border rounded px-3 py-2 w-full">
            @error('quantity_in_stock') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Save Product
        </button>
    </form>
</div>