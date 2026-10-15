<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    /**
     * Download a stored database backup JSON file.
     */
    public function download(string $filename, BackupService $backupService): BinaryFileResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('admin')) {
            abort(403, 'Unauthorized: Database backup access is strictly restricted to System Administrators.');
        }

        $safeFilename = basename($filename);
        $filepath = $backupService->getBackupDirectory().DIRECTORY_SEPARATOR.$safeFilename;

        if (! File::exists($filepath)) {
            abort(404, 'The requested database backup file does not exist.');
        }

        return response()->download($filepath, $safeFilename, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache, private',
        ]);
    }
}
