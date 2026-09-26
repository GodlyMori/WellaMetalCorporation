<?php

namespace Tests\Feature;

use App\Livewire\PromotionsManager;
use App\Livewire\SalesManager;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class EventPromotionsTest extends TestCase
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
            'name' => 'Sample Metal Item ' . uniqid(),
            'category' => 'Sofa',
            'description' => 'Standard description',
            'tagged_price' => 10000.00,
            'quantity_in_stock' => 50,
            'status' => 'active',
            'reorder_threshold' => 5,
        ], $attributes));
    }

    /**
     * Test 1: Admin creates promo -> activates immediately
     */
    public function test_admin_can_create_and_activate_promo_immediately(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PromotionsManager::class)
            ->set('name', 'Valentine Special Sale')
            ->set('code', 'VALENTINE2026')
            ->set('discount_type', 'percentage')
            ->set('discount_value', '15')
            ->set('applicable_category', 'ALL')
            ->set('starts_at', now()->toDateString())
            ->set('ends_at', now()->addWeeks(2)->toDateString())
            ->call('savePromotion')
            ->assertHasNoErrors();

        $promo = Promotion::where('code', 'VALENTINE2026')->first();
        $this->assertNotNull($promo);
        $this->assertEquals('active', $promo->status);
        $this->assertEquals($this->admin->id, $promo->approved_by);
        $this->assertNotNull($promo->approved_at);
    }

    /**
     * Test 2: Manager creates promo -> defaults to pending_approval (Admin approval needed)
     */
    public function test_manager_created_promo_requires_admin_approval(): void
    {
        Livewire::actingAs($this->manager)
            ->test(PromotionsManager::class)
            ->set('name', 'Kadayawan Festival Discount')
            ->set('code', 'KADAYAWAN26')
            ->set('discount_type', 'percentage')
            ->set('discount_value', '10')
            ->set('applicable_category', 'Sofa')
            ->set('starts_at', now()->toDateString())
            ->set('ends_at', now()->addWeeks(2)->toDateString())
            ->call('savePromotion')
            ->assertHasNoErrors();

        $promo = Promotion::where('code', 'KADAYAWAN26')->first();
        $this->assertNotNull($promo);
        $this->assertEquals('pending_approval', $promo->status);
        $this->assertNull($promo->approved_by);

        // Secretary cannot see or use pending_approval promo
        $salesComponent = Livewire::actingAs($this->secretary)->test(SalesManager::class);
        $availablePromos = $salesComponent->get('availablePromotions');
        $this->assertFalse($availablePromos->contains('id', $promo->id));

        // Admin approves and activates
        Livewire::actingAs($this->admin)
            ->test(PromotionsManager::class)
            ->call('approvePromotion', $promo->id)
            ->assertHasNoErrors();

        $promo->refresh();
        $this->assertEquals('active', $promo->status);
        $this->assertEquals($this->admin->id, $promo->approved_by);

        // Now secretary CAN see and apply the approved promo
        $salesComponent = Livewire::actingAs($this->secretary)->test(SalesManager::class);
        $availablePromos = $salesComponent->get('availablePromotions');
        $this->assertTrue($availablePromos->contains('id', $promo->id));
    }

    /**
     * Test 3: Category Scoping restricts discounts to matching product categories only
     */
    public function test_promo_category_scoping_only_discounts_matching_category(): void
    {
        // Kadayawan promo is 10% off Sofa only
        $promo = Promotion::create([
            'name' => 'Kadayawan Sofa Special',
            'code' => 'KADAYAWAN-SOFA',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'applicable_category' => 'Sofa',
            'min_order_amount' => 0,
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addWeeks(2)->toDateString(),
            'status' => 'active',
            'created_by' => $this->admin->id,
            'approved_by' => $this->admin->id,
            'approved_at' => now(),
        ]);

        $sofa = $this->createProduct(['name' => 'Italian Leather Sofa', 'category' => 'Sofa', 'tagged_price' => 10000]);
        $table = $this->createProduct(['name' => 'Metal Dining Table', 'category' => 'Dining Table', 'tagged_price' => 5000]);

        $component = Livewire::actingAs($this->secretary)
            ->test(SalesManager::class)
            ->set('items', [
                ['product_id' => $sofa->id, 'quantity' => 1, 'unit_price' => 10000, 'subtotal' => 10000],
                ['product_id' => $table->id, 'quantity' => 1, 'unit_price' => 5000, 'subtotal' => 5000],
            ])
            ->set('selected_promotion_id', $promo->id);

        // Total before discount is 15,000
        $this->assertEquals(15000.0, $component->get('totalAmount'));

        // Discount is 10% of 10,000 (Sofa) = 1,000 (Dining Table is excluded!)
        $this->assertEquals(1000.0, $component->get('discountAmount'));

        // Net Amount = 14,000
        $this->assertEquals(14000.0, $component->get('netAmount'));
    }

    /**
     * Test 4: Checkout records promo snapshot, discount, original amount, and layaway calculations
     */
    public function test_sale_recording_with_promo_and_layaway(): void
    {
        $promo = Promotion::create([
            'name' => 'Valentine Love Sale',
            'code' => 'LOVE2026',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'applicable_category' => 'ALL',
            'min_order_amount' => 0,
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addWeeks(2)->toDateString(),
            'status' => 'active',
            'created_by' => $this->admin->id,
            'approved_by' => $this->admin->id,
            'approved_at' => now(),
        ]);

        $product = $this->createProduct(['name' => 'King Bed Frame', 'category' => 'Sofa', 'tagged_price' => 20000]);

        Livewire::actingAs($this->secretary)
            ->test(SalesManager::class)
            ->set('customer_name', 'Romeo Santos')
            ->set('customer_phone', '0917-555-4321')
            ->set('customer_address', '14 Valentine Ave, Davao City')
            ->set('items', [
                ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 20000, 'subtotal' => 20000],
            ])
            ->set('selected_promotion_id', $promo->id)
            ->set('payment_type', 'layaway')
            ->set('downpayment_amount', 4000)
            ->call('recordSale')
            ->assertHasNoErrors();

        $sale = Sale::first();
        $this->assertNotNull($sale);
        // Original was 20,000
        $this->assertEquals(20000.0, (float)$sale->original_amount);
        // Discount 20% = 4,000
        $this->assertEquals(4000.0, (float)$sale->discount_amount);
        // Net payable = 16,000
        $this->assertEquals(16000.0, (float)$sale->amount);
        // Promo snapshot
        $this->assertEquals($promo->id, $sale->promotion_id);
        $this->assertEquals('Valentine Love Sale', $sale->promo_name);
        // Layaway: Downpayment = 4,000, Remaining = 12,000
        $this->assertEquals(4000.0, (float)$sale->amount_paid);
        $this->assertEquals(12000.0, (float)$sale->remaining_balance);
        $this->assertEquals('layaway', $sale->status);
    }

    /**
     * Test 5: Admin can toggle activation (deactivate / reactivate)
     */
    public function test_admin_can_deactivate_and_reactivate_promo(): void
    {
        $promo = Promotion::create([
            'name' => 'Flash Sale',
            'discount_type' => 'fixed',
            'discount_value' => 500,
            'applicable_category' => 'ALL',
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addWeeks(1)->toDateString(),
            'status' => 'active',
            'created_by' => $this->admin->id,
            'approved_by' => $this->admin->id,
        ]);

        $component = Livewire::actingAs($this->admin)->test(PromotionsManager::class);

        // Deactivate
        $component->call('toggleStatus', $promo->id);
        $this->assertEquals('inactive', $promo->fresh()->status);

        // Secretary cannot see inactive promo
        $sales = Livewire::actingAs($this->secretary)->test(SalesManager::class);
        $this->assertFalse($sales->get('availablePromotions')->contains('id', $promo->id));

        // Reactivate
        Livewire::actingAs($this->admin)->test(PromotionsManager::class)->call('toggleStatus', $promo->id);
        $this->assertEquals('active', $promo->fresh()->status);
    }
}
