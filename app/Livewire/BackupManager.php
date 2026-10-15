<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\BackupService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class BackupManager extends Component
{
    use WithFileUploads;

    public string $notes = '';

    public string $successMessage = '';

    public string $errorMessage = '';

    // Restore confirmation modal
    public bool $showRestoreModal = false;

    public string $selectedBackupFilename = '';

    public string $restorePassword = '';

    // Upload & Restore modal
    public bool $showUploadModal = false;

    /** @var mixed */
    public $uploadedBackupFile = null;

    public string $uploadPassword = '';

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('admin')) {
            abort(403, 'Unauthorized: Access to database backups is strictly restricted to System Administrators.');
        }
    }

    public function createBackup(BackupService $backupService): void
    {
        $this->resetFeedback();

        try {
            $user = Auth::user();
            $result = $backupService->createBackup($this->notes, $user);

            $this->notes = '';
            $this->successMessage = $result['message'];
        } catch (\Throwable $e) {
            $this->errorMessage = 'Failed to generate database backup: '.$e->getMessage();
        }
    }

    public function confirmRestore(string $filename): void
    {
        $this->resetFeedback();
        $this->selectedBackupFilename = $filename;
        $this->restorePassword = '';
        $this->showRestoreModal = true;
    }

    public function cancelRestore(): void
    {
        $this->showRestoreModal = false;
        $this->selectedBackupFilename = '';
        $this->restorePassword = '';
    }

    public function executeRestore(BackupService $backupService): void
    {
        $this->resetFeedback();

        if (empty($this->restorePassword)) {
            $this->errorMessage = 'Please enter your Admin password to authorize the database restoration.';

            return;
        }

        $filepath = $backupService->getBackupDirectory().DIRECTORY_SEPARATOR.basename($this->selectedBackupFilename);

        /** @var User $user */
        $user = Auth::user();
        $result = $backupService->restoreBackup($filepath, $user, $this->restorePassword);

        if ($result['success']) {
            $this->showRestoreModal = false;
            $this->restorePassword = '';
            $this->selectedBackupFilename = '';
            $this->successMessage = $result['message'];
        } else {
            $this->errorMessage = $result['message'];
        }
    }

    public function openUploadModal(): void
    {
        $this->resetFeedback();
        $this->uploadedBackupFile = null;
        $this->uploadPassword = '';
        $this->showUploadModal = true;
    }

    public function cancelUpload(): void
    {
        $this->showUploadModal = false;
        $this->uploadedBackupFile = null;
        $this->uploadPassword = '';
    }

    public function executeUploadAndRestore(BackupService $backupService): void
    {
        $this->resetFeedback();

        $this->validate([
            'uploadedBackupFile' => 'required|file|max:51200', // 50MB max
            'uploadPassword' => 'required|string',
        ]);

        $tempPath = $this->uploadedBackupFile->getRealPath();

        /** @var User $user */
        $user = Auth::user();
        $result = $backupService->restoreBackup($tempPath, $user, $this->uploadPassword);

        if ($result['success']) {
            // Also save a copy of this uploaded backup into the backups directory
            $newFilename = 'backup_wella_metal_uploaded_'.now()->format('Y_m_d_His').'.json';
            $destPath = $backupService->getBackupDirectory().DIRECTORY_SEPARATOR.$newFilename;
            @copy($tempPath, $destPath);

            $this->showUploadModal = false;
            $this->uploadedBackupFile = null;
            $this->uploadPassword = '';
            $this->successMessage = 'Uploaded backup successfully restored into the system!';
        } else {
            $this->errorMessage = $result['message'];
        }
    }

    public function deleteBackup(string $filename, BackupService $backupService): void
    {
        $this->resetFeedback();

        $deleted = $backupService->deleteBackup($filename);
        if ($deleted) {
            $this->successMessage = "Backup archive '{$filename}' was deleted successfully.";
        } else {
            $this->errorMessage = "Unable to delete backup file '{$filename}'.";
        }
    }

    protected function resetFeedback(): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    public function render(BackupService $backupService)
    {
        $backups = $backupService->getBackups();

        $totalBackupsCount = count($backups);
        $lastBackupDate = $backups[0]['created_at'] ?? 'Never';
        $latestRecordCount = $backups[0]['total_records'] ?? 0;
        $totalStorageBytes = array_sum(array_column($backups, 'size_bytes'));

        return view('livewire.backup-manager', [
            'backups' => $backups,
            'totalBackupsCount' => $totalBackupsCount,
            'lastBackupDate' => $lastBackupDate,
            'latestRecordCount' => $latestRecordCount,
            'totalStorageBytes' => $totalStorageBytes,
        ]);
    }
}
