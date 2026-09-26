<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';

    public function render()
    {
        $products = Product::query()
            ->where('status', 'active')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.product-list', [
            'products' => $products,
        ]);
    }
}