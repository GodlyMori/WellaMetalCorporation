<?php

namespace Tests\Feature;

use App\Console\Commands\SendLayawayRemindersCommand;
use App\Livewire\Dashboard;
use App\Livewire\LayawayManager;
use App\Mail\LayawayDueReminderMail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\LayawayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LayawayEnhancementsAndDashboardOptimizationTest extends TestCase
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
        $this->actingAs($this->user);

        Artisan::registerCommand(new SendLayawayRemindersCommand);
    }

    public function test_dashboard_aggregates_top_products_via_database(): void
    {
        $prodA = Product::create([
            'name' => 'Heavy Gate Model A',
            'category' => 'Gate',
            'tagged_price' => 20000.00,
            'quantity_in_stock' => 10,
            'status' => 'active',
        ]);

        $prodB = Product::create([
            'name' => 'Steel Bench Model B',
            'category' => 'Garden Set',
            'tagged_price' => 5000.00,
            'quantity_in_stock' => 10,
            'status' => 'active',
        ]);

        $sale1 = Sale::create([
            'sale_number' => 'WMC-DASH-001',
            'customer_name' => 'Juan Dela Cruz',
            'product_name' => $prodA->name,
            'amount' => 40000.00,
            'amount_paid' => 40000.00,
            'status' => 'completed',
            'payment_type' => 'full',
            'sale_date' => now()->toDateString(),
            'created_by' => $this->user->id,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $prodA->id,
            'quantity' => 2,
            'unit_price' => 20000.00,
            'subtotal' => 40000.00,
        ]);

        $sale2 = Sale::create([
            'sale_number' => 'WMC-DASH-002',
            'customer_name' => 'Pedro Penduko',
            'product_name' => $prodB->name,
            'amount' => 10000.00,
            'amount_paid' => 10000.00,
            'status' => 'completed',
            'payment_type' => 'full',
            'sale_date' => now()->toDateString(),
            'created_by' => $this->user->id,
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $prodB->id,
            'quantity' => 2,
            'unit_price' => 5000.00,
            'subtotal' => 10000.00,
        ]);

        Livewire::test(Dashboard::class)
            ->assertSee('Heavy Gate Model A')
            ->assertSee('Steel Bench Model B')
            ->assertSee('₱40,000.00')
            ->assertSee('₱10,000.00');
    }

    public function test_send_layaway_reminders_command_dispatches_email(): void
    {
        Mail::fake();

        $product = Product::create([
            'name' => 'Garden Swing',
            'category' => 'Garden Set',
            'tagged_price' => 15000.00,
            'quantity_in_stock' => 5,
            'status' => 'active',
        ]);

        $customer = Customer::create([
            'name' => 'Elena Reyes',
            'email' => 'elena@example.com',
            'phone' => '09171112233',
        ]);

        $sale = Sale::create([
            'sale_number' => 'WMC-REMIND-001',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'product_name' => $product->name,
            'amount' => 15000.00,
            'downpayment_amount' => 5000.00,
            'amount_paid' => 5000.00,
            'remaining_balance' => 10000.00,
            'payment_type' => 'layaway',
            'status' => 'layaway',
            'layaway_expires_at' => now()->addDays(7)->toDateString(),
            'sale_date' => now()->subMonths(2)->toDateString(),
            'created_by' => $this->user->id,
        ]);
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 15000.00,
            'subtotal' => 15000.00,
        ]);

        $this->artisan(SendLayawayRemindersCommand::class)
            ->expectsOutputToContain('Sent reminder for WMC-REMIND-001 to elena@example.com')
            ->assertSuccessful();

        Mail::assertSent(LayawayDueReminderMail::class, function ($mail) {
            return $mail->hasTo('elena@example.com') && $mail->daysRemaining <= 7;
        });

        $this->assertNotNull($sale->fresh()->last_reminder_sent_at);
        $this->assertEquals(1, $sale->fresh()->reminder_count);
    }

    public function test_layaway_manager_sends_manual_reminder_and_records_audit(): void
    {
        Mail::fake();

        $product = Product::create([
            'name' => 'Steel Patio Set',
            'category' => 'Garden Set',
            'tagged_price' => 18000.00,
            'quantity_in_stock' => 4,
            'status' => 'active',
        ]);

        $sale = Sale::create([
            'sale_number' => 'WMC-MANUAL-001',
            'customer_name' => 'Manuel Quezon',
            'customer_email' => 'manuel@example.com',
            'customer_phone' => '09187654321',
            'product_name' => $product->name,
            'amount' => 18000.00,
            'downpayment_amount' => 6000.00,
            'amount_paid' => 6000.00,
            'remaining_balance' => 12000.00,
            'payment_type' => 'layaway',
            'status' => 'layaway',
            'layaway_expires_at' => now()->addDays(10)->toDateString(),
            'sale_date' => now()->toDateString(),
            'created_by' => $this->user->id,
        ]);

        Livewire::test(LayawayManager::class)
            ->call('sendReminder', $sale->id)
            ->assertSee('Reminder #1 dispatched successfully to manuel@example.com');

        Mail::assertSent(LayawayDueReminderMail::class, fn ($mail) => $mail->hasTo('manuel@example.com'));
        $this->assertEquals(1, $sale->fresh()->reminder_count);
    }

    public function test_layaway_cancellation_records_deposit_disposition_and_audit_trail(): void
    {
        $product = Product::create([
            'name' => 'Steel Canopy Table',
            'category' => 'Dining Set',
            'tagged_price' => 25000.00,
            'quantity_in_stock' => 3,
            'status' => 'active',
        ]);

        $sale = Sale::create([
            'sale_number' => 'WMC-FORFEIT-001',
            'customer_name' => 'Apolinario Mabini',
            'product_name' => $product->name,
            'amount' => 25000.00,
            'downpayment_amount' => 8000.00,
            'amount_paid' => 8000.00,
            'remaining_balance' => 17000.00,
            'payment_type' => 'layaway',
            'status' => 'layaway',
            'layaway_expires_at' => now()->subDay()->toDateString(),
            'sale_date' => now()->subMonths(3)->toDateString(),
            'created_by' => $this->user->id,
        ]);
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 25000.00,
            'subtotal' => 25000.00,
        ]);

        Livewire::test(LayawayManager::class)
            ->call('openCancelModal', $sale->id)
            ->assertSet('showCancelModal', true)
            ->set('depositDisposition', 'forfeited')
            ->set('cancelReason', 'Customer defaulted past deadline')
            ->call('submitCancellation')
            ->assertSet('showCancelModal', false)
            ->assertSee('cancelled and reserved stock restored');

        $fresh = $sale->fresh();
        $this->assertEquals('cancelled', $fresh->status);
        $this->assertEquals('forfeited', $fresh->deposit_disposition);
        $this->assertEquals(8000.00, (float) $fresh->forfeited_deposit_amount);
        $this->assertEquals(0.00, (float) $fresh->refunded_deposit_amount);
        $this->assertEquals($this->user->id, $fresh->cancelled_by);
        $this->assertNotNull($fresh->cancelled_at);
        $this->assertEquals(4, $product->fresh()->quantity_in_stock); // Restocked 3 + 1 = 4
    }

    public function test_layaway_service_auto_cancellation_records_audit_trail(): void
    {
        $product = Product::create([
            'name' => 'Iron Grille Chair',
            'category' => 'Sala Set',
            'tagged_price' => 7000.00,
            'quantity_in_stock' => 2,
            'status' => 'active',
        ]);

        $sale = Sale::create([
            'sale_number' => 'WMC-AUTO-EXPIRE-001',
            'customer_name' => 'Gregorio Del Pilar',
            'product_name' => $product->name,
            'amount' => 7000.00,
            'downpayment_amount' => 2000.00,
            'amount_paid' => 2000.00,
            'remaining_balance' => 5000.00,
            'payment_type' => 'layaway',
            'status' => 'layaway',
            'layaway_expires_at' => now()->subDays(5)->toDateString(),
            'sale_date' => now()->subMonths(4)->toDateString(),
            'created_by' => $this->user->id,
        ]);
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 7000.00,
            'subtotal' => 7000.00,
        ]);

        $service = new LayawayService;
        $processed = $service->processExpiredLayaways($this->user->id);

        $this->assertEquals(1, $processed);
        $fresh = $sale->fresh();
        $this->assertEquals('cancelled', $fresh->status);
        $this->assertEquals('forfeited', $fresh->deposit_disposition);
        $this->assertEquals(2000.00, (float) $fresh->forfeited_deposit_amount);
        $this->assertEquals($this->user->id, $fresh->cancelled_by);
        $this->assertNotNull($fresh->cancelled_at);
        $this->assertEquals(3, $product->fresh()->quantity_in_stock); // Restocked 2 + 1 = 3
    }
}
