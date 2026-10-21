<?php

namespace Tests\Feature;

use App\Livewire\ReportsManager;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\PeriodicReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PeriodicReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'secretary']);

        $this->user = User::factory()->create([
            'email' => 'admin@wellametalcorp.com',
        ]);
        $this->user->assignRole('admin');
    }

    public function test_periodic_report_service_groups_daily_sales(): void
    {
        $product = Product::create([
            'name' => 'Steel Canopy',
            'category' => 'Garden Set',
            'tagged_price' => 12000.00,
            'quantity_in_stock' => 15,
            'low_stock_threshold' => 3,
            'status' => 'active',
        ]);

        $sale1 = Sale::create([
            'sale_number' => 'WMC-20261010-001',
            'customer_name' => 'Roberto Carlos',
            'customer_phone' => '09181112233',
            'product_name' => $product->name,
            'amount' => 12000.00,
            'amount_paid' => 12000.00,
            'remaining_balance' => 0.00,
            'status' => 'completed',
            'sale_date' => '2026-10-10',
            'created_by' => $this->user->id,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 12000.00,
            'subtotal' => 12000.00,
        ]);

        $sale2 = Sale::create([
            'sale_number' => 'WMC-20261012-002',
            'customer_name' => 'Maria Santos',
            'customer_phone' => '09192223344',
            'product_name' => $product->name,
            'amount' => 24000.00,
            'amount_paid' => 5000.00,
            'remaining_balance' => 19000.00,
            'status' => 'layaway',
            'sale_date' => '2026-10-12',
            'created_by' => $this->user->id,
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 12000.00,
            'subtotal' => 24000.00,
        ]);

        $service = new PeriodicReportService;
        $summary = $service->getPeriodicSummary('daily', '2026-10-01', '2026-10-31');

        $this->assertEquals(2, $summary['totals']['total_transactions']);
        $this->assertEquals(1, $summary['totals']['completed_count']);
        $this->assertEquals(1, $summary['totals']['layaway_count']);
        // Realized cash = 12000 (completed) + 5000 (layaway deposit) = 17000
        $this->assertEquals(17000.00, $summary['totals']['realized_cash']);
        $this->assertEquals(19000.00, $summary['totals']['outstanding_receivables']);
        $this->assertCount(2, $summary['buckets']);
    }

    public function test_inventory_turnover_velocity_calculation(): void
    {
        $product = Product::create([
            'name' => 'Fast Moving Bench',
            'category' => 'Garden Set',
            'tagged_price' => 3500.00,
            'quantity_in_stock' => 20,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);

        $sale = Sale::create([
            'sale_number' => 'WMC-20261014-005',
            'customer_name' => 'Elena Gomez',
            'product_name' => $product->name,
            'amount' => 35000.00,
            'amount_paid' => 35000.00,
            'remaining_balance' => 0.00,
            'status' => 'completed',
            'sale_date' => '2026-10-14',
            'created_by' => $this->user->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 3500.00,
            'subtotal' => 35000.00,
        ]);

        $service = new PeriodicReportService;
        $movements = $service->getInventoryMovements('2026-10-01', '2026-10-31');

        $this->assertEquals(10, $movements['total_units_sold']);
        $this->assertGreaterThan(0, $movements['overall_turnover_rate']);

        $item = collect($movements['items'])->firstWhere('product_id', $product->id);
        $this->assertNotNull($item);
        $this->assertEquals(10, $item['units_sold']);
        $this->assertEquals('High Velocity', $item['velocity']);
    }

    public function test_reports_page_loads_with_periodic_tab(): void
    {
        $this->actingAs($this->user)
            ->get(route('reports'))
            ->assertOk()
            ->assertSee('Periodic summaries', false)
            ->assertSee('Periodic Sales Ledger', false)
            ->assertDontSee('Automated Periodic Business Report Generation', false);
    }

    public function test_reports_manager_switches_periodic_subviews(): void
    {
        Livewire::actingAs($this->user)
            ->test(ReportsManager::class)
            ->assertSet('periodicSubView', 'ledger')
            ->assertSee('Cadence:')
            ->call('setPeriodicSubView', 'turnover')
            ->assertSet('periodicSubView', 'turnover')
            ->assertSee('Overall Turnover Velocity')
            ->call('setPeriodicSubView', 'traceability')
            ->assertSet('periodicSubView', 'traceability')
            ->assertSee('Underlying Transaction Traceability Ledger');
    }

    public function test_export_periodic_pdf_downloads_clean_pdf(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('reports.export.periodic-pdf', [
                'periodicity' => 'daily',
                'start_date' => '2026-10-01',
                'end_date' => '2026-10-31',
            ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('wella-metal-periodic-daily-report-', $response->headers->get('Content-Disposition') ?? '');
    }

    public function test_export_sales_pdf_accepts_search_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('reports.export.sales-pdf', [
                'search' => 'Carlos',
                'status' => 'ALL',
            ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_reports_manager_sales_status_dropdown_and_order_modal(): void
    {
        $product = Product::create([
            'name' => 'Wrought Iron Gate',
            'category' => 'Gate',
            'tagged_price' => 17000.00,
            'quantity_in_stock' => 5,
            'low_stock_threshold' => 1,
            'status' => 'active',
        ]);

        $sale = Sale::create([
            'sale_number' => 'WMC-20261020-001',
            'customer_name' => 'Don Juan',
            'customer_phone' => '09171234567',
            'product_name' => $product->name,
            'amount' => 16000.00,
            'amount_paid' => 16000.00,
            'original_amount' => 17000.00,
            'remaining_balance' => 0.00,
            'status' => 'completed',
            'payment_type' => 'full',
            'sale_date' => '2026-10-20',
            'created_by' => $this->user->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'original_price' => 17000.00,
            'unit_price' => 16000.00,
            'subtotal' => 16000.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(ReportsManager::class)
            ->set('activeTab', 'sales')
            ->set('salesStatus', 'completed')
            ->assertSet('salesStatus', 'completed')
            ->assertSee('WMC-20261020-001')
            ->call('openOrderModal', $sale->id)
            ->assertSet('showOrderModal', true)
            ->assertSee('Negotiated Pricing Details')
            ->assertSee('Don Juan')
            ->assertSee('SRP ₱17,000.00')
            ->assertSee('Agreed ₱16,000.00')
            ->assertSee('-₱1,000.00')
            ->call('closeOrderModal')
            ->assertSet('showOrderModal', false);
    }

    public function test_reports_manager_layaway_filtering_and_pdf_exports(): void
    {
        $product = Product::create([
            'name' => 'Steel Dining Table',
            'category' => 'Dining Set',
            'tagged_price' => 20000.00,
            'quantity_in_stock' => 2,
            'low_stock_threshold' => 3,
            'status' => 'active',
        ]);

        $layawayActive = Sale::create([
            'sale_number' => 'WMC-LAY-001',
            'customer_name' => 'Maria Clara',
            'customer_phone' => '09191234567',
            'product_name' => $product->name,
            'amount' => 20000.00,
            'downpayment_amount' => 5000.00,
            'amount_paid' => 5000.00,
            'remaining_balance' => 15000.00,
            'payment_type' => 'layaway',
            'status' => 'layaway',
            'layaway_expires_at' => now()->addDays(30),
            'sale_date' => now()->toDateString(),
            'created_by' => $this->user->id,
        ]);

        $layawaySettled = Sale::create([
            'sale_number' => 'WMC-LAY-002',
            'customer_name' => 'Crisostomo Ibarra',
            'customer_phone' => '09197654321',
            'product_name' => $product->name,
            'amount' => 20000.00,
            'downpayment_amount' => 5000.00,
            'amount_paid' => 20000.00,
            'remaining_balance' => 0.00,
            'payment_type' => 'layaway',
            'status' => 'completed',
            'sale_date' => now()->subDays(10)->toDateString(),
            'created_by' => $this->user->id,
        ]);

        // Test ReportsManager layaway tabs
        Livewire::actingAs($this->user)
            ->test(ReportsManager::class)
            ->set('activeTab', 'layaways')
            ->set('layawayStatus', 'active')
            ->assertSee('WMC-LAY-001')
            ->assertDontSee('WMC-LAY-002')
            ->set('layawayStatus', 'settled')
            ->assertSee('WMC-LAY-002')
            ->assertDontSee('WMC-LAY-001');

        // Test PDF Exports
        $responseLayawayPdf = $this->actingAs($this->user)->get(route('reports.export.layaways-pdf', ['status' => 'active']));
        $responseLayawayPdf->assertOk();
        $this->assertStringContainsString('application/pdf', $responseLayawayPdf->headers->get('Content-Type'));

        $responseInvPdf = $this->actingAs($this->user)->get(route('reports.export.inventory-pdf'));
        $responseInvPdf->assertOk();
        $this->assertStringContainsString('application/pdf', $responseInvPdf->headers->get('Content-Type'));

        $responsePeriodicPdf = $this->actingAs($this->user)->get(route('reports.export.periodic-pdf'));
        $responsePeriodicPdf->assertOk();
        $this->assertStringContainsString('application/pdf', $responsePeriodicPdf->headers->get('Content-Type'));

        $responseSalesPdf = $this->actingAs($this->user)->get(route('reports.export.sales-pdf'));
        $responseSalesPdf->assertOk();
        $this->assertStringContainsString('application/pdf', $responseSalesPdf->headers->get('Content-Type'));

        $responseAllPdf = $this->actingAs($this->user)->get(route('reports.export.all-pdf'));
        $responseAllPdf->assertOk();
        $this->assertStringContainsString('application/pdf', $responseAllPdf->headers->get('Content-Type'));
    }
}
