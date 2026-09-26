<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockTransferForm extends Component
{
    public string $source_branch = '';
    public string $date_received = '';
    public string $notes = '';

    // Each line is an array like ['product_id' => '', 'quantity_received' => '']
    public array $lines = [
        ['product_id' => '', 'quantity_received' => ''],
    ];

    public function addLine()
    {
        $this->lines[] = ['product_id' => '', 'quantity_received' => ''];
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines); // re-index the array
    }

    protected function rules(): array
    {
        return [
            'source_branch' => 'required|string|max:255',
            'date_received' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity_received' => 'required|integer|min:1',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $transfer = StockTransfer::create([
                'source_branch' => $validated['source_branch'],
                'received_by' => Auth::id(),
                'date_received' => $validated['date_received'],
                'notes' => $validated['notes'],
            ]);

            foreach ($validated['lines'] as $line) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $line['product_id'],
                    'quantity_received' => $line['quantity_received'],
                ]);

                // Increase the product's stock by the received quantity
                Product::where('id', $line['product_id'])
                    ->increment('quantity_in_stock', $line['quantity_received']);
            }
        });

        session()->flash('message', 'Delivery logged and stock updated successfully.');

        $this->reset(['source_branch', 'date_received', 'notes']);
        $this->lines = [['product_id' => '', 'quantity_received' => '']];
    }

    public function render()
    {
        return view('livewire.stock-transfer-form', [
            'products' => Product::where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}