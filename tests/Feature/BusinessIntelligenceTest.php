<?php

namespace Tests\Feature;

use App\Livewire\Admin\BusinessIntelligence;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\BusinessIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BusinessIntelligenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $manager;

    protected User $secretary;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'secretary']);

        $this->admin = User::factory()->create([
            'email' => 'admin@wellametalcorp.com',
        ]);
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create([
            'email' => 'manager@wellametalcorp.com',
        ]);
        $this->manager->assignRole('manager');

        $this->secretary = User::factory()->create([
            'email' => 'secretary@wellametalcorp.com',
        ]);
        $this->secretary->assignRole('secretary');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.bi'));
        $response->assertRedirect(route('login'));
    }

    public function test_secretary_cannot_access_business_intelligence(): void
    {
        $response = $this->actingAs($this->secretary)->get(route('admin.bi'));
        $response->assertForbidden();
    }

    public function test_manager_cannot_access_business_intelligence(): void
    {
        $response = $this->actingAs($this->manager)->get(route('admin.bi'));
        $response->assertForbidden();
    }

    public function test_admin_can_access_business_intelligence(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.bi'));
        $response->assertOk();
        $response->assertSee('Business Intelligence');
        $response->assertSee('Decision Analytics');
        $response->assertSee('Caravan Price Concessions');
    }

    public function test_business_intelligence_service_calculates_concessions_and_abc(): void
    {
        $product1 = Product::create([
            'name' => 'Wrought Iron Dining Set',
            'category' => 'Dining Set',
            'tagged_price' => 20000.00,
            'quantity_in_stock' => 10,
            'low_stock_threshold' => 2,
            'status' => 'active',
        ]);

        $product2 = Product::create([
            'name' => 'Old Vintage Bench',
            'category' => 'Garden Set',
            'tagged_price' => 5000.00,
            'quantity_in_stock' => 4,
            'low_stock_threshold' => 1,
            'status' => 'active',
        ]);

        // Sale with negotiated concession
        $sale = Sale::create([
            'sale_number' => 'WMC-BI-001',
            'customer_name' => 'Don Juan',
            'customer_phone' => '09170001111',
            'product_name' => $product1->name,
            'amount' => 18000.00,
            'amount_paid' => 18000.00,
            'original_amount' => 20000.00,
            'remaining_balance' => 0.00,
            'status' => 'completed',
            'payment_type' => 'full',
            'sale_date' => now()->toDateString(),
            'created_by' => $this->admin->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'original_price' => 20000.00,
            'unit_price' => 18000.00,
            'subtotal' => 18000.00,
        ]);

        $service = new BusinessIntelligenceService;
        $metrics = $service->getExecutiveMetrics('all_time');

        // Verify commercial KPIs
        $this->assertEquals(18000.00, $metrics['kpis']['total_invoiced_amount']);
        $this->assertEquals(18000.00, $metrics['kpis']['realized_cash_collected']);
        $this->assertEquals(20000.00, $metrics['negotiations']['gross_srp_total']);
        $this->assertEquals(2000.00, $metrics['negotiations']['total_concessions']);
        $this->assertEquals(10.0, $metrics['negotiations']['concession_rate']);

        // Verify dead stock capital (product2 had 0 sales, 4 units * 5000 = 20000)
        $this->assertEquals(20000.00, $metrics['inventory_abc']['dead_stock_capital']);
        $this->assertEquals(1, $metrics['inventory_abc']['dead_stock_count']);
    }

    public function test_livewire_business_intelligence_component_interactivity(): void
    {
        $product = Product::create([
            'name' => 'King Size Metal Bed',
            'category' => 'Bed Frame',
            'tagged_price' => 15000.00,
            'quantity_in_stock' => 5,
            'low_stock_threshold' => 1,
            'status' => 'active',
        ]);

        $sale = Sale::create([
            'sale_number' => 'WMC-BI-002',
            'customer_name' => 'Clara Montefalco',
            'customer_phone' => '09187778899',
            'product_name' => $product->name,
            'amount' => 13500.00,
            'amount_paid' => 13500.00,
            'original_amount' => 15000.00,
            'remaining_balance' => 0.00,
            'status' => 'completed',
            'payment_type' => 'full',
            'sale_date' => now()->toDateString(),
            'created_by' => $this->admin->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'original_price' => 15000.00,
            'unit_price' => 13500.00,
            'subtotal' => 13500.00,
        ]);

        Livewire::actingAs($this->admin)
            ->test(BusinessIntelligence::class)
            ->assertOk()
            ->assertSee('Caravan Price Concessions')
            ->assertSee('-₱1,500.00')
            ->call('setDatePreset', 'all_time')
            ->assertSet('datePreset', 'all_time')
            ->call('setActiveTab', 'negotiations')
            ->assertSet('activeTab', 'negotiations')
            ->assertSee('Category Negotiation Tolerance')
            ->call('setActiveTab', 'inventory_abc')
            ->assertSet('activeTab', 'inventory_abc')
            ->assertSee('Class A (Core Revenue Drivers)')
            ->call('setActiveTab', 'customers')
            ->assertSet('activeTab', 'customers')
            ->assertSee('Top 10 High-LTV VIP Clients')
            ->assertSee('Clara Montefalco');
    }
}
