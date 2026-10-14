<?php

namespace Tests\Feature;

use App\Livewire\SalesManager;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionRedemption;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PromotionOneTimeUseTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Promotion $promo;

    protected Customer $customer;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->product = Product::create([
            'name' => 'Verona Leather Sofa',
            'category' => 'Sofa',
            'tagged_price' => 10000.00,
            'quantity_in_stock' => 10,
            'status' => 'active',
        ]);

        $this->promo = Promotion::create([
            'name' => 'Kadayawan Festival Sale',
            'code' => 'KADAYAWAN2026',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'applicable_category' => 'ALL',
            'min_order_amount' => 0.00,
            'starts_at' => now()->subDay()->toDateString(),
            'ends_at' => now()->addDays(7)->toDateString(),
            'status' => 'active',
            'created_by' => $this->user->id,
            'approved_by' => $this->user->id,
            'approved_at' => now(),
        ]);

        $this->customer = Customer::create([
            'customer_number' => 'CUST-2026-0001',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'phone' => '0917-123-4567',
            'address' => 'Bajada, Davao City',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_sale_creation_records_promotion_redemption_atomically(): void
    {
        Livewire::test(SalesManager::class)
            ->call('selectCustomer', $this->customer->id)
            ->call('addProductToCart', $this->product->id)
            ->set('promo_code_input', 'KADAYAWAN2026')
            ->call('applyPromoCode')
            ->assertSet('selected_promotion_id', $this->promo->id)
            ->assertSet('discountAmount', 1000.0)
            ->set('payment_type', 'full')
            ->set('cash_tendered', '9000.00')
            ->call('recordSale')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('promotion_redemptions', [
            'promotion_id' => $this->promo->id,
            'customer_id' => $this->customer->id,
        ]);

        $redemption = PromotionRedemption::where('promotion_id', $this->promo->id)
            ->where('customer_id', $this->customer->id)
            ->first();

        $this->assertNotNull($redemption);
        $this->assertNotNull($redemption->sale_id);
        $this->assertEquals($this->promo->id, $redemption->promotion->id);
        $this->assertEquals($this->customer->id, $redemption->customer->id);
    }

    public function test_customer_cannot_apply_previously_redeemed_promo_code(): void
    {
        // Record prior redemption
        PromotionRedemption::create([
            'promotion_id' => $this->promo->id,
            'customer_id' => $this->customer->id,
            'created_at' => now(),
        ]);

        Livewire::test(SalesManager::class)
            ->call('selectCustomer', $this->customer->id)
            ->call('addProductToCart', $this->product->id)
            ->set('promo_code_input', 'KADAYAWAN2026')
            ->call('applyPromoCode')
            ->assertSet('selected_promotion_id', null)
            ->assertSet('promoErrorMessage', 'This promotion has already been redeemed for this customer account.')
            ->assertSet('discountAmount', 0.0);
    }

    public function test_selecting_customer_revokes_already_selected_redeemed_promo(): void
    {
        // Record prior redemption
        PromotionRedemption::create([
            'promotion_id' => $this->promo->id,
            'customer_id' => $this->customer->id,
            'created_at' => now(),
        ]);

        // Secretaty enters promo first, then selects the customer who already redeemed it
        Livewire::test(SalesManager::class)
            ->call('addProductToCart', $this->product->id)
            ->set('promo_code_input', 'KADAYAWAN2026')
            ->call('applyPromoCode')
            ->assertSet('selected_promotion_id', $this->promo->id)
            ->call('selectCustomer', $this->customer->id)
            ->assertSet('selected_promotion_id', null)
            ->assertSet('promoErrorMessage', 'This promotion has already been redeemed for this customer account.');
    }

    public function test_database_blocks_duplicate_redemptions_via_unique_index(): void
    {
        PromotionRedemption::create([
            'promotion_id' => $this->promo->id,
            'customer_id' => $this->customer->id,
            'created_at' => now(),
        ]);

        $this->expectException(UniqueConstraintViolationException::class);

        PromotionRedemption::create([
            'promotion_id' => $this->promo->id,
            'customer_id' => $this->customer->id,
            'created_at' => now(),
        ]);
    }

    public function test_different_customer_can_redeem_promo(): void
    {
        // First customer redeems
        PromotionRedemption::create([
            'promotion_id' => $this->promo->id,
            'customer_id' => $this->customer->id,
            'created_at' => now(),
        ]);

        // Second customer
        $otherCustomer = Customer::create([
            'customer_number' => 'CUST-2026-0002',
            'first_name' => 'Juan',
            'last_name' => 'Luna',
            'name' => 'Juan Luna',
            'phone' => '0918-987-6543',
            'address' => 'Lanang, Davao City',
            'created_by' => $this->user->id,
        ]);

        Livewire::test(SalesManager::class)
            ->call('selectCustomer', $otherCustomer->id)
            ->call('addProductToCart', $this->product->id)
            ->set('promo_code_input', 'KADAYAWAN2026')
            ->call('applyPromoCode')
            ->assertSet('selected_promotion_id', $this->promo->id)
            ->assertSet('promoErrorMessage', '')
            ->assertSet('discountAmount', 1000.0);
    }
}
