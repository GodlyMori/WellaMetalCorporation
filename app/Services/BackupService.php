<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BackupService
{
    /**
     * Core business tables to archive in logical dependency order.
     *
     * @var array<int, string>
     */
    public const BACKUP_TABLES = [
        'users',
        'roles',
        'permissions',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        'categories',
        'customers',
        'products',
        'promotions',
        'sales',
        'sale_items',
        'payments',
        'inventory_adjustments',
        'stock_transfers',
        'stock_transfer_items',
        'stock_requests',
        'promotion_redemptions',
    ];

    /**
     * Ensure storage directory exists and return its path.
     */
    public function getBackupDirectory(): string
    {
        $dir = storage_path('app/backups');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        return $dir;
    }

    /**
     * Generate an on-demand database backup archive.
     *
     * @return array{success: bool, filename: string, total_records: int, filepath: string, message: string}
     */
    public function createBackup(?string $notes = null, ?User $creator = null): array
    {
        $this->getBackupDirectory();

        $tableCounts = [];
        $totalRecords = 0;
        $tablesData = [];

        foreach (self::BACKUP_TABLES as $table) {
            if (Schema::hasTable($table)) {
                $rows = DB::table($table)->get()->map(fn ($r) => (array) $r)->toArray();
                $count = count($rows);
                $tablesData[$table] = $rows;
                $tableCounts[$table] = $count;
                $totalRecords += $count;
            }
        }

        $timestamp = now();
        $filename = 'backup_wella_metal_'.$timestamp->format('Y_m_d_His').'.json';
        $filepath = $this->getBackupDirectory().DIRECTORY_SEPARATOR.$filename;

        $payload = [
            'manifest' => [
                'version' => '1.0',
                'app_name' => 'Wella Metal Corporation Retail Operations',
                'objective' => 'Objective 5: Data Preservation and Disaster Recovery',
                'created_at' => $timestamp->toIso8601String(),
                'created_at_human' => $timestamp->format('M d, Y h:i A'),
                'created_by_id' => $creator?->id,
                'created_by_name' => $creator?->name ?? 'System Administrator',
                'notes' => $notes ?: 'Manual operational snapshot',
                'environment' => config('app.env', 'production'),
                'total_records' => $totalRecords,
                'table_counts' => $tableCounts,
            ],
            'data' => $tablesData,
        ];

        File::put($filepath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        Log::info("Database backup created: {$filename} ({$totalRecords} records).");

        return [
            'success' => true,
            'filename' => $filename,
            'total_records' => $totalRecords,
            'filepath' => $filepath,
            'message' => "Successfully archived {$totalRecords} records to {$filename}.",
        ];
    }

    /**
     * Retrieve all available backups sorted by newest first.
     *
     * @return array<int, array{filename: string, filepath: string, size_human: string, size_bytes: int, created_at: string, created_by: string, notes: string, total_records: int, table_counts: array<string, int>}>
     */
    public function getBackups(): array
    {
        $dir = $this->getBackupDirectory();
        $files = File::glob($dir.DIRECTORY_SEPARATOR.'*.json');

        $backups = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $size = File::size($file);

            // Read the manifest from the file header
            $manifest = $this->extractManifest($file);

            $backups[] = [
                'filename' => $filename,
                'filepath' => $file,
                'size_human' => $this->formatBytes($size),
                'size_bytes' => $size,
                'created_at' => $manifest['created_at_human'] ?? Carbon::createFromTimestamp(File::lastModified($file))->format('M d, Y h:i A'),
                'created_by' => $manifest['created_by_name'] ?? 'System Administrator',
                'notes' => $manifest['notes'] ?? 'Manual backup',
                'total_records' => (int) ($manifest['total_records'] ?? 0),
                'table_counts' => (array) ($manifest['table_counts'] ?? []),
            ];
        }

        // Sort newest first
        usort($backups, function ($a, $b) {
            return strcmp($b['filename'], $a['filename']);
        });

        return $backups;
    }

    /**
     * Restore database from a given JSON backup file.
     *
     * @return array{success: bool, message: string}
     */
    public function restoreBackup(string $filepath, User $adminUser, string $password): array
    {
        if (! Hash::check($password, $adminUser->password)) {
            return [
                'success' => false,
                'message' => 'Admin authorization failed: Incorrect password provided.',
            ];
        }

        if (! File::exists($filepath)) {
            return [
                'success' => false,
                'message' => 'Backup archive file was not found on the server.',
            ];
        }

        $rawContent = File::get($filepath);
        $payload = json_decode($rawContent, true);

        if (! is_array($payload) || ! isset($payload['manifest']) || ! isset($payload['data'])) {
            return [
                'success' => false,
                'message' => 'Invalid or corrupted backup archive file format.',
            ];
        }

        $driver = DB::getDriverName();

        DB::beginTransaction();
        try {
            // 1. Temporarily disable foreign key constraints
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            // 2. Clear target tables in reverse order
            $tablesInBackup = array_keys($payload['data']);
            $tablesToClear = array_reverse(self::BACKUP_TABLES);

            foreach ($tablesToClear as $table) {
                if (in_array($table, $tablesInBackup) && Schema::hasTable($table)) {
                    DB::table($table)->delete();
                }
            }

            // 3. Restore records table by table
            $restoredCount = 0;
            foreach (self::BACKUP_TABLES as $table) {
                if (isset($payload['data'][$table]) && Schema::hasTable($table)) {
                    $rows = $payload['data'][$table];
                    if (! empty($rows)) {
                        foreach (array_chunk($rows, 100) as $chunk) {
                            DB::table($table)->insert($chunk);
                            $restoredCount += count($chunk);
                        }
                    }
                }
            }

            // 4. Re-enable foreign key constraints
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            DB::commit();

            Log::info("Database successfully restored by Admin {$adminUser->name} from ".basename($filepath)." ({$restoredCount} records).");

            return [
                'success' => true,
                'message' => "Database recovery successful! Restored {$restoredCount} records across all business modules.",
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            Log::error("Database restore failed: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);

            return [
                'success' => false,
                'message' => 'Database restoration failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Delete an existing backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        $filepath = $this->getBackupDirectory().DIRECTORY_SEPARATOR.$filename;

        if (File::exists($filepath)) {
            return File::delete($filepath);
        }

        return false;
    }

    /**
     * Extract manifest info without parsing entire large data if possible.
     */
    protected function extractManifest(string $filepath): array
    {
        try {
            $handle = fopen($filepath, 'r');
            if (! $handle) {
                return [];
            }

            // Read the first 4KB which comfortably contains the manifest
            $chunk = fread($handle, 4096);
            fclose($handle);

            if (preg_match('/"manifest"\s*:\s*(\{.*?\})\s*,\s*"data"/s', $chunk, $matches)) {
                $manifest = json_decode($matches[1], true);
                if (is_array($manifest)) {
                    return $manifest;
                }
            }

            // Fallback to decoding the entire file if chunk regex didn't catch it
            $content = File::get($filepath);
            $full = json_decode($content, true);

            return $full['manifest'] ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Format bytes to human readable format.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}
