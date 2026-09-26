<?php

namespace Tests\Feature;

use App\Livewire\InventoryManager;
use App\Livewire\SalesManager;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected User $secretary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@wellametal.com',
            'password' => Hash::make('admin12345'),
        ]);
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create([
            'name' => 'Branch Manager',
            'email' => 'manager@wellametal.com',
            'password' => Hash::make('manager12345'),
        ]);
        $this->manager->assignRole('manager');

        $this->secretary = User::factory()->create([
            'name' => 'Sales Secretary',
            'email' => 'secretary@wellametal.com',
            'password' => Hash::make('secretary12345'),
        ]);
        $this->secretary->assignRole('secretary');
    }

    private function createProduct(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Sample Product ' . uniqid(),
            'category' => 'Sofa',
            'description' => 'Standard description',
            'tagged_price' => 1000.00,
            'quantity_in_stock' => 50,
            'status' => 'active',
            'reorder_threshold' => 5,
        ], $attributes));
    }

    /**
     * Test 1: Customer name cannot contain numbers
     */
    public function test_customer_name_cannot_contain_numbers(): void
    {
        $product = $this->createProduct(['quantity_in_stock' => 50, 'tagged_price' => 100]);

        Livewire::actingAs($this->secretary)
            ->test(SalesManager::class)
            ->set('customer_name', 'Customer 123')
            ->set('customer_phone', '09171234567')
            ->set('customer_address', '123 Main St')
            ->set('payment_type', 'full')
            ->set('items.0.product_id', $product->id)
            ->set('items.0.quantity', 2)
            ->set('items.0.unit_price', 100)
            ->set('items.0.subtotal', 200)
            ->call('recordSale')
            ->assertHasErrors(['customer_name']);
    }

    /**
     * Test 2: Customer phone and address are required
     */
    public function test_customer_phone_and_address_are_required(): void
    {
        $product = $this->createProduct(['quantity_in_stock' => 50, 'tagged_price' => 100]);

        Livewire::actingAs($this->secretary)
            ->test(SalesManager::class)
            ->set('customer_name', 'Valid Customer')
            ->set('customer_phone', '')
            ->set('customer_address', '')
            ->set('payment_type', 'full')
            ->set('items.0.product_id', $product->id)
            ->set('items.0.quantity', 2)
            ->set('items.0.unit_price', 100)
            ->set('items.0.subtotal', 200)
            ->call('recordSale')
            ->assertHasErrors(['customer_phone', 'customer_address']);
    }

    /**
     * Test 3: Product item name CAN contain numbers
     */
    public function test_product_item_name_can_contain_numbers(): void
    {
        Livewire::actingAs($this->admin)
            ->test(InventoryManager::class)
            ->set('new_name', 'Steel Angle Bar 2x2 #40')
            ->set('new_category', 'Metal Bars')
            ->set('new_description', 'Heavy duty industrial bar')
            ->set('new_price', '450.00')
            ->set('new_stock', '100')
            ->call('saveSingleProduct')
            ->assertHasNoErrors(['new_name']);

        $this->assertDatabaseHas('products', [
            'name' => 'Steel Angle Bar 2x2 #40',
        ]);
    }

    /**
     * Test 4: Multi-item sales creates items and decrements stock for each product
     */
    public function test_multi_item_sales_creates_records_and_deducts_inventory(): void
    {
        $p1 = $this->createProduct(['name' => 'Item Alpha', 'quantity_in_stock' => 10, 'tagged_price' => 500]);
        $p2 = $this->createProduct(['name' => 'Item Beta', 'quantity_in_stock' => 20, 'tagged_price' => 300]);

        Livewire::actingAs($this->secretary)
            ->test(SalesManager::class)
            ->set('customer_name', 'John Doe Construction')
            ->set('customer_phone', '0917-888-9999')
            ->set('customer_address', '45 Quezon Ave, QC')
            ->set('payment_type', 'full')
            ->set('items', [
                ['product_id' => $p1->id, 'quantity' => 2, 'unit_price' => 500, 'subtotal' => 1000],
                ['product_id' => $p2->id, 'quantity' => 3, 'unit_price' => 300, 'subtotal' => 900],
            ])
            ->call('recordSale')
            ->assertHasNoErrors();

        $this->assertEquals(8, $p1->fresh()->quantity_in_stock);
        $this->assertEquals(17, $p2->fresh()->quantity_in_stock);

        $sale = Sale::first();
        $this->assertNotNull($sale);
        $this->assertEquals(1900, $sale->amount);
        $this->assertEquals('completed', $sale->status);
        $this->assertCount(2, $sale->items);
    }

    /**
     * Test 5: Layaway order reserves stock and sets 3-month expiry
     */
    public function test_layaway_reserves_stock_and_sets_three_months_expiry(): void
    {
        $product = $this->createProduct(['quantity_in_stock' => 15, 'tagged_price' => 1000]);

        Livewire::actingAs($this->secretary)
            ->test(SalesManager::class)
            ->set('customer_name', 'Maria Santos')
            ->set('customer_phone', '0920-111-2222')
            ->set('customer_address', 'Brgy Central, Pasig City')
            ->set('payment_type', 'layaway')
            ->set('downpayment_amount', 400)
            ->set('items', [
                ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 1000, 'subtotal' => 1000],
            ])
            ->call('recordSale')
            ->assertHasNoErrors();

        // Stock reserved/deducted
        $this->assertEquals(14, $product->fresh()->quantity_in_stock);

        $sale = Sale::first();
        $this->assertEquals('layaway', $sale->status);
        $this->assertEquals(400, $sale->amount_paid);
        $this->assertEquals(600, $sale->remaining_balance);
        $this->assertNotNull($sale->layaway_expires_at);

        // Expiry is ~3 months in future
        $this->assertEquals(
            now()->addMonths(3)->toDateString(),
            $sale->layaway_expires_at->toDateString()
        );
    }

    /**
     * Test 6: Secretary archiving triggers authorization modal, requires admin/manager password
     */
    public function test_secretary_archiving_requires_admin_or_manager_permission(): void
    {
        $product = $this->createProduct(['status' => 'active']);

        // 1. Secretary attempts archive -> triggers modal without archiving immediately
        $component = Livewire::actingAs($this->secretary)
            ->test(InventoryManager::class)
            ->call('archiveProduct', $product->id);

        $component->assertSet('showAuthModal', true);
        $this->assertEquals('active', $product->fresh()->status);

        // 2. Entering wrong credentials fails
        $component->set('authApproverId', $this->admin->id)
            ->set('authPassword', 'wrong-pass')
            ->set('authReason', 'Damaged goods in transit')
            ->call('confirmAuthorizedArchive');

        $this->assertNotEmpty($component->get('authError'));
        $this->assertEquals('active', $product->fresh()->status);

        // 3. Entering valid manager/admin credentials completes the archive
        $component->set('authApproverId', $this->manager->id)
            ->set('authPassword', 'manager12345')
            ->set('authReason', 'Authorized by manager')
            ->call('confirmAuthorizedArchive')
            ->assertSet('showAuthModal', false);

        $this->assertEquals('archived', $product->fresh()->status);
        $this->assertEquals($this->secretary->id, $product->fresh()->archived_by);
        $this->assertEquals($this->manager->id, $product->fresh()->archive_approved_by);
    }
}
