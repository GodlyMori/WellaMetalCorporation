<?php

namespace Tests\Feature;

use App\Livewire\AdminAnalyticsBi;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_unauthenticated_user_cannot_access_analytics()
    {
        $response = $this->get('/analytics');
        $response->assertRedirect('/login');
    }

    public function test_secretary_cannot_access_admin_analytics()
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $response = $this->actingAs($secretary)->get('/analytics');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_manager_cannot_access_admin_analytics()
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->get('/analytics');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_access_analytics_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/analytics');
        $response->assertStatus(200);
        $response->assertSee('ADMIN EXECUTIVE SUITE');
        $response->assertSee('LIVE COMPUTED BI');
    }

    public function test_admin_analytics_livewire_component_functions_properly()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(AdminAnalyticsBi::class)
            ->assertStatus(200)
            ->assertSee('GROSS SALES')
            ->assertSee('REALIZED CASH')
            ->assertSee('LAYAWAY RECEIVABLES')
            ->set('timeframe', '7d')
            ->assertSet('timeframe', '7d')
            ->set('activeTab', 'layaways')
            ->assertSet('activeTab', 'layaways')
            ->set('activeTab', 'products')
            ->assertSet('activeTab', 'products')
            ->set('activeTab', 'staff')
            ->assertSet('activeTab', 'staff');
    }
}
