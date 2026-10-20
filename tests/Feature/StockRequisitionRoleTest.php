<?php

namespace Tests\Feature;

use App\Livewire\InventoryManager;
use App\Models\Product;
use App\Models\StockRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockRequisitionRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $manager;

    protected User $secretary;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'secretary']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        $this->secretary = User::factory()->create();
        $this->secretary->assignRole('secretary');

        $this->product = Product::create([
            'name' => 'Steel Gate Standard',
            'category' => 'Gate',
            'tagged_price' => 12000.00,
            'quantity_in_stock' => 2,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);
    }

    public function test_secretary_sees_request_button_and_can_submit_requisition(): void
    {
        Livewire::actingAs($this->secretary)
            ->test(InventoryManager::class)
            ->assertSee('Request')
            ->call('openRequestModal', $this->product->id)
            ->assertSet('showRequestModal', true)
            ->set('requestQuantity', 10)
            ->set('requestReason', 'Low showroom stock')
            ->call('submitStockRequest')
            ->assertSet('showRequestModal', false)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stock_requests', [
            'product_id' => $this->product->id,
            'requested_by' => $this->secretary->id,
            'quantity_requested' => 10,
            'status' => 'pending',
        ]);
    }

    public function test_admin_does_not_see_request_button_and_cannot_request_stock(): void
    {
        Livewire::actingAs($this->admin)
            ->test(InventoryManager::class)
            ->assertDontSeeHtml('wire:click="openRequestModal('.$this->product->id.')"')
            ->call('openRequestModal', $this->product->id)
            ->assertSet('showRequestModal', false)
            ->assertSee('Stock requisitions can only be submitted by Secretaries');

        Livewire::actingAs($this->admin)
            ->test(InventoryManager::class)
            ->set('requestProductId', $this->product->id)
            ->set('requestQuantity', 5)
            ->set('requestReason', 'Test')
            ->call('submitStockRequest')
            ->assertSee('Stock requisitions can only be submitted by Secretaries');

        $this->assertDatabaseMissing('stock_requests', [
            'product_id' => $this->product->id,
            'requested_by' => $this->admin->id,
        ]);
    }

    public function test_manager_does_not_see_request_button_and_cannot_request_stock(): void
    {
        Livewire::actingAs($this->manager)
            ->test(InventoryManager::class)
            ->assertDontSeeHtml('wire:click="openRequestModal('.$this->product->id.')"')
            ->call('openRequestModal', $this->product->id)
            ->assertSet('showRequestModal', false)
            ->assertSee('Stock requisitions can only be submitted by Secretaries');

        Livewire::actingAs($this->manager)
            ->test(InventoryManager::class)
            ->set('requestProductId', $this->product->id)
            ->set('requestQuantity', 5)
            ->set('requestReason', 'Test')
            ->call('submitStockRequest')
            ->assertSee('Stock requisitions can only be submitted by Secretaries');

        $this->assertDatabaseMissing('stock_requests', [
            'product_id' => $this->product->id,
            'requested_by' => $this->manager->id,
        ]);
    }

    public function test_admin_and_manager_can_approve_stock_requests(): void
    {
        $stockRequest = StockRequest::create([
            'request_number' => 'REQ-2026-0001',
            'product_id' => $this->product->id,
            'requested_by' => $this->secretary->id,
            'quantity_requested' => 8,
            'reason' => 'Restock needed',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->manager)
            ->test(InventoryManager::class)
            ->call('approveStockRequest', $stockRequest->id)
            ->assertHasNoErrors();

        $stockRequest->refresh();
        $this->assertEquals('awaiting_delivery', $stockRequest->status);
        $this->assertEquals($this->manager->id, $stockRequest->approved_by);
    }
}
