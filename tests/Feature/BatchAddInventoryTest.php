<?php

namespace Tests\Feature;

use App\Livewire\AddInventory;
use App\Models\Product;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class BatchAddInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_batch_endpoint_rejects_duplicate_items_with_422_json(): void
    {
        $payload = [
            'items' => [
                [
                    'name' => 'Modern Metal Bed',
                    'category' => 'Bed Frame',
                    'description' => 'King size steel frame',
                    'tagged_price' => 12500.00,
                    'quantity_in_stock' => 2,
                    'low_stock_threshold' => 3,
                ],
                [
                    'name' => 'modern metal bed ', // Duplicate with different casing/whitespace
                    'category' => 'Bed Frame',
                    'description' => 'King size steel frame',
                    'tagged_price' => 12500.00,
                    'quantity_in_stock' => 5,
                    'low_stock_threshold' => 3,
                ],
            ],
        ];

        $response = $this->postJson(route('inventory.batch'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'items.1' => 'Duplicate product row in payload.',
            ]);

        $this->assertDatabaseMissing('products', [
            'name' => 'Modern Metal Bed',
        ]);
    }

    public function test_batch_endpoint_persists_valid_items_atomically(): void
    {
        $payload = [
            'items' => [
                [
                    'name' => 'Deluxe Steel Cabinet',
                    'category' => 'Closet',
                    'description' => '4 doors, powder coated',
                    'tagged_price' => 18000.00,
                    'quantity_in_stock' => 4,
                    'low_stock_threshold' => 2,
                ],
                [
                    'name' => 'Ergo Office Sala Chair',
                    'category' => 'Sala Set',
                    'description' => 'Leatherette cushioning',
                    'tagged_price' => 6500.00,
                    'quantity_in_stock' => 10,
                    'low_stock_threshold' => 5,
                ],
            ],
        ];

        $response = $this->postJson(route('inventory.batch'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'newCount' => 2,
                    'restockedCount' => 0,
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Deluxe Steel Cabinet',
            'quantity_in_stock' => 4,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Ergo Office Sala Chair',
            'quantity_in_stock' => 10,
        ]);

        $this->assertDatabaseCount('inventory_adjustments', 2);
    }

    public function test_batch_endpoint_restocks_existing_product_atomically(): void
    {
        $existing = Product::create([
            'name' => 'Verona Coffee Table',
            'category' => 'Sala Set',
            'tagged_price' => 4500.00,
            'quantity_in_stock' => 5,
            'status' => 'active',
        ]);

        $payload = [
            'items' => [
                [
                    'product_id' => $existing->id,
                    'name' => $existing->name,
                    'category' => 'Sala Set',
                    'description' => '',
                    'tagged_price' => 4500.00,
                    'quantity_in_stock' => 3,
                ],
            ],
        ];

        $response = $this->postJson(route('inventory.batch'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'newCount' => 0,
                    'restockedCount' => 1,
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $existing->id,
            'quantity_in_stock' => 8,
        ]);

        $this->assertDatabaseHas('inventory_adjustments', [
            'product_id' => $existing->id,
            'old_quantity' => 5,
            'new_quantity' => 8,
            'quantity_change' => 3,
        ]);
    }

    public function test_livewire_component_detects_real_time_duplicate_rows_and_generates_exact_error_message(): void
    {
        Livewire::test(AddInventory::class)
            ->set('entryItems', [
                [
                    'product_id' => null,
                    'is_existing' => false,
                    'name' => 'Stainless Wardrobe',
                    'category' => 'Closet',
                    'description' => 'Mirror finish',
                    'tagged_price' => '15000',
                    'quantity_in_stock' => '2',
                    'low_stock_threshold' => '2',
                ],
                [
                    'product_id' => null,
                    'is_existing' => false,
                    'name' => ' stainless wardrobe ',
                    'category' => 'Closet',
                    'description' => 'Mirror finish',
                    'tagged_price' => '15000',
                    'quantity_in_stock' => '3',
                    'low_stock_threshold' => '2',
                ],
            ])
            ->assertSet('hasDuplicateOrIncomplete', true)
            ->assertSee('Duplicate entry: This product is already entered in Item #1. Adjust quantity there instead.');
    }

    public function test_livewire_component_clears_error_and_reindexes_when_duplicate_row_is_removed(): void
    {
        $component = Livewire::test(AddInventory::class)
            ->set('entryItems', [
                [
                    'product_id' => null,
                    'is_existing' => false,
                    'name' => 'Stainless Wardrobe',
                    'category' => 'Closet',
                    'description' => 'Mirror finish',
                    'tagged_price' => '15000',
                    'quantity_in_stock' => '2',
                    'low_stock_threshold' => '2',
                ],
                [
                    'product_id' => null,
                    'is_existing' => false,
                    'name' => 'Stainless Wardrobe',
                    'category' => 'Closet',
                    'description' => 'Mirror finish',
                    'tagged_price' => '15000',
                    'quantity_in_stock' => '3',
                    'low_stock_threshold' => '2',
                ],
            ])
            ->assertSet('hasDuplicateOrIncomplete', true);

        // Remove row 1 (the duplicate)
        $component->call('removeEntryRow', 1)
            ->assertSet('hasDuplicateOrIncomplete', false)
            ->assertDontSee('Duplicate entry: This product is already entered in Item #1. Adjust quantity there instead.');
    }

    public function test_livewire_batch_save_executes_and_displays_success_message(): void
    {
        Livewire::test(AddInventory::class)
            ->set('entryItems', [
                [
                    'product_id' => null,
                    'is_existing' => false,
                    'name' => 'Titanium Office Desk',
                    'category' => 'Sala Set',
                    'description' => 'Heavy duty',
                    'tagged_price' => '8500',
                    'quantity_in_stock' => '4',
                    'low_stock_threshold' => '2',
                ],
            ])
            ->call('saveInventoryEntries')
            ->assertHasNoErrors()
            ->assertSet('successMessage', '1 new product(s) added successfully!');

        $this->assertDatabaseHas('products', [
            'name' => 'Titanium Office Desk',
            'quantity_in_stock' => 4,
        ]);
    }

    public function test_transaction_rolls_back_if_exception_occurs_during_save(): void
    {
        $service = app(InventoryService::class);

        // Intentionally create a payload where the second item causes an unexpected error or validation failure
        $items = [
            [
                'name' => 'Item Alpha',
                'category' => 'Sala Set',
                'description' => '',
                'tagged_price' => 1000,
                'quantity_in_stock' => 1,
            ],
            [
                'name' => 'Item Alpha', // Duplicate
                'category' => 'Sala Set',
                'description' => '',
                'tagged_price' => 1000,
                'quantity_in_stock' => 1,
            ],
        ];

        try {
            $service->saveBatch($items);
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('items.1', $e->errors());
        }

        // Verify neither product was persisted
        $this->assertDatabaseMissing('products', [
            'name' => 'Item Alpha',
        ]);
    }

    public function test_inventory_add_page_is_accessible(): void
    {
        $response = $this->get(route('inventory.add'));

        $response->assertStatus(200)
            ->assertSeeLivewire(AddInventory::class);
    }
}
