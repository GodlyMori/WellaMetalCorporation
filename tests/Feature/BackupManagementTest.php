<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BackupManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $secretary;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'secretary']);

        $this->admin = User::factory()->create([
            'email' => 'admin@wellametalcorp.com',
            'password' => Hash::make('AdminSecret123!'),
        ]);
        $this->admin->assignRole('admin');

        $this->secretary = User::factory()->create([
            'email' => 'secretary@wellametalcorp.com',
            'password' => Hash::make('SecretarySecret123!'),
        ]);
        $this->secretary->assignRole('secretary');
    }

    protected function tearDown(): void
    {
        // Clean up test backups directory
        $service = new BackupService;
        $dir = $service->getBackupDirectory();
        if (File::exists($dir)) {
            $files = File::glob($dir.'/*');
            foreach ($files as $file) {
                @unlink($file);
            }
        }

        parent::tearDown();
    }

    public function test_only_admin_can_access_backup_page(): void
    {
        // Unauthenticated redirected to login
        $this->get(route('backup'))->assertRedirect(route('login'));

        // Secretary is forbidden by BackupManager mount authorization
        $this->actingAs($this->secretary)
            ->get(route('backup'))
            ->assertForbidden();

        // Admin can access successfully
        $this->actingAs($this->admin)
            ->get(route('backup'))
            ->assertOk()
            ->assertSee('Data Preservation', false)
            ->assertSee('Disaster Recovery', false);
    }

    public function test_backup_service_creates_valid_json_archive(): void
    {
        // Seed some sample data
        Category::firstOrCreate(['slug' => 'sala-set'], ['name' => 'Sala Set', 'is_active' => true]);
        Product::create([
            'name' => 'Heavy Duty Steel Gate',
            'category' => 'Sala Set',
            'tagged_price' => 15000.00,
            'quantity_in_stock' => 10,
            'low_stock_threshold' => 3,
            'status' => 'active',
        ]);
        Customer::create([
            'name' => 'Juan Dela Cruz',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'phone' => '09171234567',
        ]);

        $service = new BackupService;
        $result = $service->createBackup('Automated test backup', $this->admin);

        $this->assertTrue($result['success']);
        $this->assertFileExists($result['filepath']);

        $content = json_decode(File::get($result['filepath']), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('manifest', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals('Objective 5: Data Preservation and Disaster Recovery', $content['manifest']['objective']);
        $this->assertGreaterThan(0, $content['manifest']['total_records']);
    }

    public function test_backup_service_lists_created_backups(): void
    {
        $service = new BackupService;
        $service->createBackup('Snapshot 1', $this->admin);

        $backups = $service->getBackups();
        $this->assertCount(1, $backups);
        $this->assertStringContainsString('backup_wella_metal_', $backups[0]['filename']);
        $this->assertEquals('Snapshot 1', $backups[0]['notes']);
    }

    public function test_backup_restoration_restores_database_records(): void
    {
        $service = new BackupService;

        // 1. Create initial state
        Product::create([
            'name' => 'Preserved Stainless Table',
            'category' => 'Dining Set',
            'tagged_price' => 8500.00,
            'quantity_in_stock' => 5,
            'low_stock_threshold' => 2,
            'status' => 'active',
        ]);

        $backupResult = $service->createBackup('Before crash', $this->admin);

        // 2. Simulate accidental deletion / corruption
        Product::query()->delete();
        $this->assertDatabaseCount('products', 0);

        // 3. Restore from backup
        $restoreResult = $service->restoreBackup($backupResult['filepath'], $this->admin, 'AdminSecret123!');

        $this->assertTrue($restoreResult['success']);
        $this->assertDatabaseHas('products', [
            'name' => 'Preserved Stainless Table',
        ]);
    }

    public function test_backup_restoration_rejects_incorrect_password(): void
    {
        $service = new BackupService;
        $backupResult = $service->createBackup('Test auth', $this->admin);

        $restoreResult = $service->restoreBackup($backupResult['filepath'], $this->admin, 'WrongPassword!');
        $this->assertFalse($restoreResult['success']);
        $this->assertStringContainsString('Admin authorization failed', $restoreResult['message']);
    }

    public function test_admin_can_download_backup_archive(): void
    {
        $service = new BackupService;
        $backupResult = $service->createBackup('Test download', $this->admin);

        $this->actingAs($this->admin)
            ->get(route('backup.download', ['filename' => $backupResult['filename']]))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename='.$backupResult['filename']);

        // Non-admin cannot download
        $this->actingAs($this->secretary)
            ->get(route('backup.download', ['filename' => $backupResult['filename']]))
            ->assertForbidden();
    }
}
