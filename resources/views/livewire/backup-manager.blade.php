<div class="space-y-6">

    <!-- Toast Notifications -->
    @if(!empty($successMessage))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(!empty($errorMessage))
        <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-300 px-4 py-3 rounded text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button type="button" wire:click="$set('errorMessage', '')" class="text-rose-600 dark:text-rose-400 hover:text-rose-800 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Data Preservation Banner -->
    <div class="p-4 rounded border border-blue-200 dark:border-[#1a2858] bg-blue-50/50 dark:bg-[#0c163b]/60 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="p-2.5 rounded bg-blue-100 dark:bg-[#142259] text-[#142259] dark:text-blue-300 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    Data Preservation & Disaster Recovery
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 max-w-2xl leading-relaxed">
                    Safeguards retail operations by archiving transaction histories, inventory logs, and sales records. Protects core business data against accidental deletion, hardware failure, or system crashes, ensuring operational continuity for the branch.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-end md:self-center shrink-0">
            <button type="button" wire:click="openUploadModal"
                    class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold px-3.5 py-2 rounded shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span>Upload & Restore Archive</span>
            </button>
        </div>
    </div>

    <!-- KPI Strip -->
    <div class="grid grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b]">
        <div class="px-4 py-3 min-w-0">
            <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Total Backups Stored</div>
            <div class="text-xl font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">{{ $totalBackupsCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Historical snapshots</div>
        </div>
        <div class="px-4 py-3 min-w-0">
            <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Last Snapshot Taken</div>
            <div class="text-sm font-bold text-slate-900 dark:text-white mt-1 truncate">{{ $lastBackupDate }}</div>
            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5">Automated timestamp</div>
        </div>
        <div class="px-4 py-3 min-w-0">
            <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Protected Records (Latest)</div>
            <div class="text-xl font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">{{ number_format($latestRecordCount) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Across 18 system tables</div>
        </div>
        <div class="px-4 py-3 min-w-0">
            <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Storage Footprint</div>
            <div class="text-xl font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">
                {{ $totalStorageBytes >= 1048576 ? number_format($totalStorageBytes / 1048576, 2) . ' MB' : number_format($totalStorageBytes / 1024, 1) . ' KB' }}
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Server storage used</div>
        </div>
    </div>

    <!-- Quick Backup Generator Panel -->
    <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] p-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-[#142259] dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Create New Database Snapshot
        </h3>

        <form wire:submit="createBackup" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="flex-1">
                <input type="text"
                       wire:model="notes"
                       placeholder="Optional backup note (e.g., Routine weekly archive, Pre-audit snapshot, Month-end backup)..."
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259]">
            </div>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded shadow-xs transition-colors cursor-pointer flex items-center justify-center gap-2 shrink-0 disabled:opacity-50">
                <svg wire:loading.remove wire:target="createBackup" class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                <svg wire:loading wire:target="createBackup" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span wire:loading.remove wire:target="createBackup">Generate Database Backup</span>
                <span wire:loading wire:target="createBackup">Archiving Tables...</span>
            </button>
        </form>
    </div>

    <!-- Backups Table -->
    <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">
        <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                Preserved System Archives ({{ count($backups) }})
            </h3>
            <span class="text-[11px] text-slate-500 dark:text-slate-400">Stored in <code class="font-mono bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">storage/app/backups/</code></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Archive File</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Created At</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Author & Notes</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Protected Records</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">File Size</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                        <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                            <td class="py-2.5 px-4 font-mono font-medium text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span class="truncate max-w-[220px]" title="{{ $backup['filename'] }}">{{ $backup['filename'] }}</span>
                            </td>
                            <td class="py-2.5 px-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $backup['created_at'] }}
                            </td>
                            <td class="py-2.5 px-4 text-slate-700 dark:text-slate-300">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $backup['created_by'] }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs">{{ $backup['notes'] }}</div>
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold font-mono bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900">
                                    {{ number_format($backup['total_records']) }} records
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $backup['size_human'] }}
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Download Button -->
                                    <a href="{{ route('backup.download', ['filename' => $backup['filename']]) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-[#1a2858] dark:hover:bg-[#223370] text-slate-700 dark:text-slate-200 text-[11px] font-semibold rounded transition-colors cursor-pointer"
                                       title="Download JSON backup archive to local storage">
                                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <span>Download</span>
                                    </a>

                                    <!-- Restore Button -->
                                    <button type="button"
                                            wire:click="confirmRestore('{{ $backup['filename'] }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-semibold rounded transition-colors cursor-pointer"
                                            title="Restore database to this snapshot">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span>Restore</span>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button"
                                            wire:click="deleteBackup('{{ $backup['filename'] }}')"
                                            wire:confirm="Are you sure you want to delete this backup file? This cannot be undone."
                                            class="inline-flex items-center p-1 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors cursor-pointer"
                                            title="Delete snapshot">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-xs font-medium">No database backups generated yet.</p>
                                <p class="text-[11px] mt-0.5">Click "Generate Database Backup" above to create your first preservation snapshot.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- RESTORE CONFIRMATION MODAL -->
    @if($showRestoreModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-[#0c163b] border border-slate-200 dark:border-[#1a2858] rounded-lg max-w-md w-full shadow-2xl p-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Confirm Database Restoration</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Disaster Recovery & Rollback Operation</p>
                    </div>
                </div>

                <div class="p-3 rounded bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 text-amber-800 dark:text-amber-300 text-xs space-y-1">
                    <p class="font-bold">⚠️ Warning: High impact operation</p>
                    <p>Restoring will overwrite current database records with the snapshot from <span class="font-mono font-semibold">{{ $selectedBackupFilename }}</span>. Ensure branch staff are not actively checking out sales.</p>
                </div>

                <form wire:submit="executeRestore" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Enter Admin Password to Authorize:
                        </label>
                        <input type="password"
                               wire:model="restorePassword"
                               placeholder="Admin account password"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-[#1a2858]">
                        <button type="button" wire:click="cancelRestore"
                                class="px-3.5 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded shadow-xs transition-colors cursor-pointer flex items-center gap-1.5 disabled:opacity-50">
                            <span wire:loading.remove wire:target="executeRestore">Authorize & Restore Database</span>
                            <span wire:loading wire:target="executeRestore">Restoring Database...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- UPLOAD & RESTORE MODAL -->
    @if($showUploadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-[#0c163b] border border-slate-200 dark:border-[#1a2858] rounded-lg max-w-md w-full shadow-2xl p-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded bg-blue-100 dark:bg-blue-950/50 text-[#142259] dark:text-blue-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Upload & Restore Archive</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Recover database from a previously downloaded .json backup</p>
                    </div>
                </div>

                <form wire:submit="executeUploadAndRestore" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Select Backup Archive (.json):
                        </label>
                        <input type="file"
                               wire:model="uploadedBackupFile"
                               accept=".json"
                               class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#142259] file:text-white hover:file:bg-[#0e1840] cursor-pointer">
                        @error('uploadedBackupFile') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Enter Admin Password to Authorize:
                        </label>
                        <input type="password"
                               wire:model="uploadPassword"
                               placeholder="Admin account password"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259]">
                        @error('uploadPassword') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-[#1a2858]">
                        <button type="button" wire:click="cancelUpload"
                                class="px-3.5 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded shadow-xs transition-colors cursor-pointer flex items-center gap-1.5 disabled:opacity-50">
                            <span wire:loading.remove wire:target="executeUploadAndRestore">Upload & Restore</span>
                            <span wire:loading wire:target="executeUploadAndRestore">Processing Recovery...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
