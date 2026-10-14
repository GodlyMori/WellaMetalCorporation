<?php

namespace Tests\Feature;

use App\Livewire\InventoryManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditProductInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $category = Category::where('name', 'Sala Set')->first();

        $this->product = Product::create([
            'name' => 'Toril Metal Daybed',
            'category' => 'Sala Set',
            'category_id' => $category?->id,
            'description' => 'Black matte powder coated finish',
            'tagged_price' => 7500.00,
            'quantity_in_stock' => 14,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);
    }

    public function test_edit_product_modal_renders_read_only_stock_with_lock_icon_sku_badge_and_helper_note(): void
    {
        Livewire::test(InventoryManager::class)
            ->call('editProduct', $this->product->id)
            ->assertSet('showEditModal', true)
            ->assertSet('edit_sku', 'P'.sprintf('%03d', $this->product->id))
            ->assertSet('edit_stock', '14')
            ->assertSet('edit_category', 'Sala Set')
            ->assertSee('SKU: P'.sprintf('%03d', $this->product->id))
            ->assertSee('Quantity in stock 🔒 (Read-only)')
            ->assertSee('Stock cannot be edited here. It is automatically deducted upon purchase, and additions must go through Add Inventory or Stock Request.')
            ->assertSee('Category cannot be changed once created.')
            ->assertSeeHtml('readonly')
            ->assertSeeHtml('disabled')
            ->assertSee('Save changes')
            ->assertSee('Cancel');
    }

    public function test_edit_product_updates_static_catalog_attributes_successfully(): void
    {
        Livewire::test(InventoryManager::class)
            ->call('editProduct', $this->product->id)
            ->set('edit_name', 'Toril Metal Daybed Deluxe')
            ->set('edit_price', '8200.00')
            ->set('edit_low_stock_threshold', '6')
            ->set('edit_description', 'Upgraded thicker tubular frame')
            ->call('updateProduct')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $this->product->refresh();

        $this->assertSame('Toril Metal Daybed Deluxe', $this->product->name);
        $this->assertSame('8200.00', (string) $this->product->tagged_price);
        $this->assertSame(6, $this->product->low_stock_threshold);
        $this->assertSame('Upgraded thicker tubular frame', $this->product->description);
        $this->assertSame(14, $this->product->quantity_in_stock); // Stock remains unchanged
    }

    public function test_client_side_tampering_of_stock_is_rejected_and_stock_remains_unchanged(): void
    {
        // Malicious client manipulates the edit_stock wire:model directly via DevTools / Livewire payload
        Livewire::test(InventoryManager::class)
            ->call('editProduct', $this->product->id)
            ->set('edit_stock', '9999') // Attempted client-side tampering
            ->set('edit_name', 'Tampered Name')
            ->call('updateProduct')
            ->assertHasNoErrors();

        $this->product->refresh();

        // Verification: Name updated, but stock quantity in DB was NOT modified
        $this->assertSame('Tampered Name', $this->product->name);
        $this->assertSame(14, $this->product->quantity_in_stock);
    }

    public function test_updated_edit_stock_hook_resets_tampered_value(): void
    {
        $component = Livewire::test(InventoryManager::class)
            ->call('editProduct', $this->product->id);

        $component->set('edit_stock', '500');

        // Hook should re-sync back to actual stock quantity in database
        $component->assertSet('edit_stock', '14');
    }

    public function test_category_tampering_is_rejected_and_category_remains_unchanged(): void
    {
        $component = Livewire::test(InventoryManager::class)
            ->call('editProduct', $this->product->id);

        // Attempt client-side tampering via wire:model
        $component->set('edit_category', 'Dining Set');

        // updatedEditCategory hook immediately resets it
        $component->assertSet('edit_category', 'Sala Set');

        // Even if updateProduct is invoked, database remains Sala Set
        $component->call('updateProduct');

        $this->product->refresh();
        $this->assertSame('Sala Set', $this->product->category);
    }

    public function test_controller_endpoint_updates_static_fields_and_ignores_stock_and_category(): void
    {
        $response = $this->putJson(route('inventory.products.update', $this->product), [
            'name' => 'API Updated Daybed',
            'category' => 'Closet', // Attempted category mutation
            'category_id' => 99,
            'unit_price' => 9000.00,
            'low_stock_threshold' => 8,
            'description' => 'Updated via controller endpoint',
            'quantity_in_stock' => 999, // Injected stock attribute
            'stock' => 999,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->product->refresh();

        $this->assertSame('API Updated Daybed', $this->product->name);
        $this->assertSame('9000.00', (string) $this->product->tagged_price);
        $this->assertSame(8, $this->product->low_stock_threshold);
        $this->assertSame(14, $this->product->quantity_in_stock); // Completely preserved
    }
}
