<?php

namespace Tests\Feature;

use App\Livewire\CustomerManager;
use App\Livewire\SalesManager;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\PeriodicReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NegotiatedPriceSaleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->product = Product::create([
            'name' => 'Wella Heavy Duty Work Table',
            'category' => 'Tables',
            'tagged_price' => 17000.00,
            'quantity_in_stock' => 10,
            'low_stock_threshold' => 2,
            'status' => 'active',
        ]);
    }

    public function test_cart_item_initializes_with_product_tagged_price_as_srp(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->assertSet('items.0.srp', 17000.00)
            ->assertSet('items.0.unit_price', 17000.00)
            ->assertSet('items.0.is_negotiated', false)
            ->assertSet('grossSrpTotal', 17000.00)
            ->assertSet('negotiatedConcessionTotal', 0.00)
            ->assertSet('hasNegotiatedItems', false)
            ->assertSet('totalAmount', 17000.00)
            ->assertSet('netAmount', 17000.00);
    }

    public function test_cashier_can_negotiate_item_price_downwards(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->call('updateItemPrice', 0, 16000.00)
            ->assertSet('items.0.srp', 17000.00)
            ->assertSet('items.0.unit_price', 16000.00)
            ->assertSet('items.0.subtotal', 16000.00)
            ->assertSet('items.0.is_negotiated', true)
            ->assertSet('grossSrpTotal', 17000.00)
            ->assertSet('negotiatedConcessionTotal', 1000.00)
            ->assertSet('hasNegotiatedItems', true)
            ->assertSet('totalAmount', 16000.00)
            ->assertSet('netAmount', 16000.00)
            ->assertSee('SRP ₱17,000.00')
            ->assertSee('Negotiated (-₱1,000.00)')
            ->assertSee('Price adjustment (negotiated)');
    }

    public function test_cashier_can_reset_negotiated_price_back_to_srp(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->call('updateItemPrice', 0, 16000.00)
            ->assertSet('items.0.is_negotiated', true)
            ->call('resetItemPrice', 0)
            ->assertSet('items.0.unit_price', 17000.00)
            ->assertSet('items.0.is_negotiated', false)
            ->assertSet('negotiatedConcessionTotal', 0.00)
            ->assertSet('hasNegotiatedItems', false)
            ->assertSet('netAmount', 17000.00);
    }

    public function test_recording_negotiated_sale_persists_srp_and_concession_audit_trail(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->call('updateItemPrice', 0, 16000.00)
            ->set('customer_first_name', 'Elena')
            ->set('customer_last_name', 'Reyes')
            ->set('customer_phone', '09171234567')
            ->set('customer_address', 'Caravan Booth 12, Malolos')
            ->set('payment_type', 'full')
            ->set('cash_tendered', 16000.00)
            ->call('recordSale')
            ->assertHasNoErrors()
            ->assertSet('showReceiptModal', true);

        // Verify Sale in database
        $sale = Sale::first();
        $this->assertNotNull($sale);
        $this->assertEquals(17000.00, (float) $sale->original_amount, 'Original amount must record the Gross SRP value.');
        $this->assertEquals(1000.00, (float) $sale->discount_amount, 'Discount amount must track the negotiated concession.');
        $this->assertEquals(16000.00, (float) $sale->amount, 'Net amount must record the final agreed price.');

        // Verify SaleItem in database
        $saleItem = SaleItem::where('sale_id', $sale->id)->first();
        $this->assertNotNull($saleItem);
        $this->assertEquals(17000.00, (float) $saleItem->original_price, 'SaleItem original_price must store SRP.');
        $this->assertEquals(16000.00, (float) $saleItem->unit_price, 'SaleItem unit_price must store Agreed Price.');
        $this->assertTrue($saleItem->is_negotiated);
        $this->assertEquals(1000.00, $saleItem->concession_amount);
    }

    public function test_recording_regular_sale_maintains_consistent_srp_without_negotiated_flag(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->set('customer_first_name', 'Carlos')
            ->set('customer_last_name', 'Mendoza')
            ->set('customer_phone', '09187654321')
            ->set('customer_address', 'Main Office, Manila')
            ->set('payment_type', 'full')
            ->set('cash_tendered', 17000.00)
            ->call('recordSale')
            ->assertHasNoErrors();

        $sale = Sale::first();
        $this->assertEquals(17000.00, (float) $sale->original_amount);
        $this->assertEquals(0.00, (float) $sale->discount_amount);
        $this->assertEquals(17000.00, (float) $sale->amount);

        $saleItem = SaleItem::where('sale_id', $sale->id)->first();
        $this->assertEquals(17000.00, (float) $saleItem->original_price);
        $this->assertEquals(17000.00, (float) $saleItem->unit_price);
        $this->assertFalse($saleItem->is_negotiated);
        $this->assertEquals(0.00, $saleItem->concession_amount);
    }

    public function test_layaway_min_downpayment_adjusts_dynamically_to_negotiated_price(): void
    {
        $component = Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->set('payment_type', 'layaway')
            // Standard SRP 17,000 -> 20% downpayment = 3,400
            ->assertSet('minDownpayment', 3400.00)
            // GM agrees to 15,000 -> 20% downpayment = 3,000
            ->call('updateItemPrice', 0, 15000.00)
            ->assertSet('minDownpayment', 3000.00)
            ->set('customer_first_name', 'Liza')
            ->set('customer_last_name', 'So')
            ->set('customer_phone', '09191234567')
            ->set('customer_address', 'Bulacan')
            ->set('downpayment_amount', 3000.00)
            ->set('cash_tendered', 3000.00)
            ->call('recordSale')
            ->assertHasNoErrors();

        $sale = Sale::first();
        $this->assertEquals('layaway', $sale->status);
        $this->assertEquals(17000.00, (float) $sale->original_amount);
        $this->assertEquals(2000.00, (float) $sale->discount_amount);
        $this->assertEquals(15000.00, (float) $sale->amount);
        $this->assertEquals(3000.00, (float) $sale->amount_paid);
        $this->assertEquals(12000.00, (float) $sale->remaining_balance);
    }

    public function test_customer_manager_history_receipt_displays_negotiated_breakdown(): void
    {
        $customer = Customer::create([
            'customer_number' => 'CUST-2026-0001',
            'name' => 'Ferdinand Marcos',
            'first_name' => 'Ferdinand',
            'last_name' => 'Marcos',
            'phone' => '09170000001',
            'created_by' => $this->user->id,
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'sale_number' => 'ORD-2026-001',
            'customer_name' => 'Ferdinand Marcos',
            'customer_phone' => '09170000001',
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'amount' => 16000.00,
            'original_amount' => 17000.00,
            'discount_amount' => 1000.00,
            'payment_type' => 'full',
            'amount_paid' => 16000.00,
            'sale_date' => now()->toDateString(),
            'status' => 'completed',
            'created_by' => $this->user->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'original_price' => 17000.00,
            'unit_price' => 16000.00,
            'subtotal' => 16000.00,
        ]);

        Livewire::test(CustomerManager::class)
            ->call('openReceipt', $sale->id)
            ->assertSet('showReceiptModal', true)
            ->assertSet('receiptData.gross_srp_total', 17000.00)
            ->assertSet('receiptData.negotiated_concession_total', 1000.00)
            ->assertSet('receiptData.has_negotiated_items', true)
            ->assertSee('Gross SRP total')
            ->assertSee('Price adjustment (negotiated)')
            ->assertSee('Agreed subtotal');
    }

    public function test_periodic_report_aggregates_gross_srp_and_concessions_consistently(): void
    {
        Sale::create([
            'sale_number' => 'ORD-2026-001',
            'customer_name' => 'Test Customer',
            'product_name' => 'Wella Heavy Duty Work Table',
            'amount' => 16000.00,
            'original_amount' => 17000.00,
            'discount_amount' => 1000.00,
            'payment_type' => 'full',
            'amount_paid' => 16000.00,
            'remaining_balance' => 0.00,
            'sale_date' => now()->toDateString(),
            'status' => 'completed',
            'is_archived' => false,
            'created_by' => $this->user->id,
        ]);

        $service = app(PeriodicReportService::class);
        $summary = $service->getPeriodicSummary('daily', now()->toDateString(), now()->toDateString());

        $this->assertEquals(17000.00, $summary['totals']['gross_srp_volume']);
        $this->assertEquals(1000.00, $summary['totals']['total_discounts']);
        $this->assertEquals(16000.00, $summary['totals']['gross_sales_volume']);
        $this->assertEquals(16000.00, $summary['totals']['realized_cash']);
    }

    public function test_negotiated_price_cannot_exceed_15_percent_discount_floor(): void
    {
        // Product SRP is 17,000. Max 15% discount is 2,550. Floor is 14,450.
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->call('updateItemPrice', 0, 10000.00) // 41% discount (exceeds 15%)
            ->assertHasErrors(['items.0.price'])
            ->assertSet('items.0.unit_price', 14450.00) // Clamped to floor
            ->assertSee('maximum 15% discount allowed')
            ->assertDontSee('error prevention');
    }

    public function test_customer_phone_cannot_exceed_11_digits(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->set('customer_first_name', 'Roberto')
            ->set('customer_last_name', 'Carlos')
            ->set('customer_address', '123 Main St, Davao City')
            ->set('cash_tendered', 17000.00)
            ->set('customer_phone', '091712345678') // 12 digits
            ->call('recordSale')
            ->assertHasErrors(['customer_phone'])
            ->set('customer_phone', '09171234567') // Exactly 11 digits
            ->call('recordSale')
            ->assertHasNoErrors(['customer_phone']);
    }
}
