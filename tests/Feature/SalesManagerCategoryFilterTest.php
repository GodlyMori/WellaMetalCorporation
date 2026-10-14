<?php

namespace Tests\Feature;

use App\Livewire\SalesManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalesManagerCategoryFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Product $sofa;

    protected Product $bed;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $salaCategory = Category::where('name', 'Sala Set')->first();
        $bedCategory = Category::where('name', 'Bed Frame')->first();

        $this->sofa = Product::create([
            'name' => 'Royal Sala Set',
            'category' => 'Sala Set',
            'category_id' => $salaCategory?->id,
            'description' => 'Velvet sofa and center table',
            'tagged_price' => 12000.00,
            'quantity_in_stock' => 5,
            'low_stock_threshold' => 2,
            'status' => 'active',
        ]);

        $this->bed = Product::create([
            'name' => 'Queen Steel Bed Frame',
            'category' => 'Bed Frame',
            'category_id' => $bedCategory?->id,
            'description' => 'Tubular steel double-deck',
            'tagged_price' => 8500.00,
            'quantity_in_stock' => 3,
            'low_stock_threshold' => 2,
            'status' => 'active',
        ]);
    }

    public function test_sales_manager_renders_unified_filter_toolbar_with_category_dropdown(): void
    {
        Livewire::test(SalesManager::class)
            ->assertSee('Product catalog')
            ->assertSee('Search product SKU, name...')
            ->assertSeeHtml('wire:model.live="selectedCategory"')
            ->assertSee('All categories (2)')
            ->assertSee('Sala Set (1)')
            ->assertSee('Bed Frame (1)')
            // Ensure horizontal tabs are removed
            ->assertDontSee('All items');
    }

    public function test_selecting_category_dropdown_filters_product_catalog_reactively(): void
    {
        Livewire::test(SalesManager::class)
            ->assertSee('Royal Sala Set')
            ->assertSee('Queen Steel Bed Frame')
            // Filter by Sala Set
            ->set('selectedCategory', 'Sala Set')
            ->assertSee('Royal Sala Set')
            ->assertDontSee('Queen Steel Bed Frame')
            // Filter by Bed Frame
            ->set('selectedCategory', 'Bed Frame')
            ->assertSee('Queen Steel Bed Frame')
            ->assertDontSee('Royal Sala Set')
            // Reset to ALL
            ->set('selectedCategory', 'ALL')
            ->assertSee('Royal Sala Set')
            ->assertSee('Queen Steel Bed Frame');
    }
}
