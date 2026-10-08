<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductForm extends Component
{
    public string $name = '';
    public string $category = '';
    public string $description = '';
    public string $tagged_price = '';
    public string $quantity_in_stock = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'tagged_price' => 'required|numeric|min:0',
            'quantity_in_stock' => 'required|integer|min:0',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        Product::create($validated);

        session()->flash('message', 'Product added successfully.');

        $this->reset(['name', 'category', 'description', 'tagged_price', 'quantity_in_stock']);
    }

    public function render()
    {
        return view('livewire.product-form');
    }
}