<?php

namespace Tests\Feature;

use App\Livewire\CustomerManager;
use App\Livewire\SalesManager;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SalesRecordTest extends TestCase
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
            'name' => 'Diamond Steel Chair',
            'category' => 'Sala Set',
            'tagged_price' => 2500.00,
            'quantity_in_stock' => 10,
            'low_stock_threshold' => 2,
            'status' => 'active',
        ]);
    }

    public function test_record_cash_sale_displays_digital_receipt_modal(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->set('customer_first_name', 'Maria')
            ->set('customer_last_name', 'Santos')
            ->set('customer_phone', '09123456789')
            ->set('customer_address', '123 Main St, Bulacan')
            ->set('customer_email', 'maria@example.com')
            ->set('payment_type', 'full')
            ->set('cash_tendered', 2500.00)
            ->call('recordSale')
            ->assertHasNoErrors()
            ->assertSet('showReceiptModal', true)
            ->assertSee('Official sales receipt')
            ->assertSee('Maria Santos')
            ->assertSee('Diamond Steel Chair');
    }

    public function test_record_layaway_sale_displays_digital_receipt_modal(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->set('customer_first_name', 'Juan')
            ->set('customer_last_name', 'Dela Cruz')
            ->set('customer_phone', '09987654321')
            ->set('customer_address', '456 Metal Rd, Bulacan')
            ->set('customer_email', 'juan@example.com')
            ->set('payment_type', 'layaway')
            ->set('downpayment_amount', 500.00)
            ->set('cash_tendered', 500.00)
            ->call('recordSale')
            ->assertHasNoErrors()
            ->assertSet('showReceiptModal', true)
            ->assertSee('Official sales receipt')
            ->assertSee('Juan Dela Cruz');
    }

    public function test_record_cash_sale_without_email_displays_digital_receipt_modal(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->set('customer_first_name', 'Roberto')
            ->set('customer_last_name', 'Gomez')
            ->set('customer_phone', '09170001122')
            ->set('customer_address', '789 Industrial Ave, Davao')
            ->set('customer_email', '')
            ->set('payment_type', 'full')
            ->set('cash_tendered', 3000.00)
            ->call('recordSale')
            ->assertHasNoErrors()
            ->assertSet('showReceiptModal', true)
            ->assertSee('Official sales receipt')
            ->assertSee('Roberto Gomez')
            ->assertSee('Change given:');
    }

    public function test_record_sale_with_existing_customer_selection(): void
    {
        $existingCustomer = Customer::create([
            'customer_number' => 'CUST-2026-9999',
            'first_name' => 'Elena',
            'last_name' => 'Reyes',
            'name' => 'Elena Reyes',
            'phone' => '09185554321',
            'email' => 'elena@example.com',
            'address' => 'Corner St, Davao City',
            'created_by' => $this->user->id,
        ]);

        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->call('selectCustomer', $existingCustomer->id)
            ->set('payment_type', 'full')
            ->set('cash_tendered', 2500.00)
            ->call('recordSale')
            ->assertHasNoErrors()
            ->assertSet('showReceiptModal', true)
            ->assertSee('Official sales receipt')
            ->assertSee('Elena Reyes');
    }

    public function test_reopen_receipt_modal_from_sales_manager(): void
    {
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->set('customer_first_name', 'Clara')
            ->set('customer_last_name', 'Bautista')
            ->set('customer_phone', '09171112233')
            ->set('customer_address', 'Poblacion, Davao')
            ->set('customer_email', 'clara@example.com')
            ->set('payment_type', 'full')
            ->set('cash_tendered', 2500.00)
            ->call('recordSale')
            ->call('closeReceiptModal')
            ->assertSet('showReceiptModal', false)
            ->call('reopenReceiptModal')
            ->assertSet('showReceiptModal', true)
            ->assertSee('Clara Bautista');
    }

    public function test_view_receipt_modal_from_customer_manager_history(): void
    {
        $customer = Customer::create([
            'customer_number' => 'CUST-2026-8888',
            'first_name' => 'Danilo',
            'last_name' => 'Cruz',
            'name' => 'Danilo Cruz',
            'phone' => '09223334455',
            'email' => 'danilo@example.com',
            'address' => 'Toril, Davao City',
            'created_by' => $this->user->id,
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'sale_number' => 'ORD-2026-777',
            'product_name' => 'Diamond Steel Chair',
            'customer_name' => 'Danilo Cruz',
            'customer_phone' => '09223334455',
            'customer_address' => 'Toril, Davao City',
            'customer_email' => 'danilo@example.com',
            'amount' => 2500.00,
            'original_amount' => 2500.00,
            'discount_amount' => 0,
            'payment_type' => 'full',
            'amount_paid' => 2500.00,
            'remaining_balance' => 0,
            'sale_date' => now()->toDateString(),
            'status' => 'completed',
            'created_by' => $this->user->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 2500.00,
            'subtotal' => 2500.00,
        ]);

        Livewire::test(CustomerManager::class)
            ->call('viewHistory', $customer->id)
            ->assertSet('showHistoryModal', true)
            ->call('openReceipt', $sale->id)
            ->assertSet('showReceiptModal', true)
            ->assertSee('Official sales receipt')
            ->assertSee('ORD-2026-777')
            ->assertSee('Danilo Cruz');
    }

    public function test_standalone_sales_receipt_route_renders_successfully(): void
    {
        $sale = Sale::create([
            'customer_id' => null,
            'sale_number' => 'ORD-2026-888',
            'product_name' => 'Diamond Steel Chair',
            'customer_name' => 'Grace Lim',
            'customer_phone' => '09334445566',
            'customer_address' => 'Bajada, Davao City',
            'amount' => 2500.00,
            'original_amount' => 2500.00,
            'discount_amount' => 0,
            'payment_type' => 'full',
            'amount_paid' => 2500.00,
            'remaining_balance' => 0,
            'sale_date' => now()->toDateString(),
            'status' => 'completed',
            'created_by' => $this->user->id,
        ]);

        $response = $this->get(route('sales.receipt', $sale->sale_number));
        $response->assertOk()
            ->assertSee('Official Digital Receipt')
            ->assertSee('ORD-2026-888')
            ->assertSee('Grace Lim');
    }

    public function test_record_sale_succeeds_and_opens_receipt_even_if_mail_transport_fails(): void
    {
        Mail::shouldReceive('to')
            ->andReturnSelf();
        Mail::shouldReceive('send')
            ->andThrow(new \RuntimeException('Connection timed out to mail server'));

        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->set('customer_first_name', 'Rolando')
            ->set('customer_last_name', 'Mendoza')
            ->set('customer_phone', '09123456780')
            ->set('customer_address', 'Ecoland, Davao City')
            ->set('customer_email', 'rolando@example.com')
            ->set('payment_type', 'full')
            ->set('cash_tendered', 2500.00)
            ->call('recordSale')
            ->assertHasNoErrors()
            ->assertSet('showReceiptModal', true)
            ->assertSee('Official sales receipt')
            ->assertSee('Rolando Mendoza');
    }

    public function test_standalone_sales_receipt_route_without_parameter_loads_latest_sale(): void
    {
        $sale = Sale::create([
            'customer_id' => null,
            'sale_number' => 'ORD-2026-999',
            'product_name' => 'Diamond Steel Chair',
            'customer_name' => 'Henry Sy',
            'customer_phone' => '09998887766',
            'customer_address' => 'Lanang, Davao City',
            'amount' => 2500.00,
            'original_amount' => 2500.00,
            'discount_amount' => 0,
            'payment_type' => 'full',
            'amount_paid' => 2500.00,
            'remaining_balance' => 0,
            'sale_date' => now()->toDateString(),
            'status' => 'completed',
            'created_by' => $this->user->id,
        ]);

        $response = $this->get(route('sales.receipt'));
        $response->assertOk()
            ->assertSee('Official Digital Receipt')
            ->assertSee('ORD-2026-999');
    }
}
