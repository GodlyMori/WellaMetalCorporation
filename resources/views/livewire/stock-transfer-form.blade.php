<div>
    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block mb-1">Source Branch</label>
            <input type="text" wire:model="source_branch" class="border rounded px-3 py-2 w-full" placeholder="e.g. Pandi, Bulacan">
            @error('source_branch') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">Date Received</label>
            <input type="date" wire:model="date_received" class="border rounded px-3 py-2 w-full">
            @error('date_received') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">Notes (optional)</label>
            <textarea wire:model="notes" class="border rounded px-3 py-2 w-full"></textarea>
        </div>

        <hr class="my-4">

        <h3 class="font-semibold">Products in this delivery</h3>

        @foreach ($lines as $index => $line)
            <div class="flex gap-4 items-start border p-3 rounded">
                <div class="flex-1">
                    <label class="block mb-1 text-sm">Product</label>
                    <select wire:model="lines.{{ $index }}.product_id" class="border rounded px-3 py-2 w-full">
                        <option value="">-- Select --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    @error("lines.{$index}.product_id") <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="w-32">
                    <label class="block mb-1 text-sm">Quantity</label>
                    <input type="number" wire:model="lines.{{ $index }}.quantity_received" class="border rounded px-3 py-2 w-full">
                    @error("lines.{$index}.quantity_received") <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                @if (count($lines) > 1)
                    <button type="button" wire:click="removeLine({{ $index }})" class="mt-6 text-red-600">
                        Remove
                    </button>
                @endif
            </div>
        @endforeach

        <button type="button" wire:click="addLine" class="text-blue-600">
            + Add another product
        </button>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded block mt-4">
                Save Delivery
            </button>
        </div>
    </form>
</div>