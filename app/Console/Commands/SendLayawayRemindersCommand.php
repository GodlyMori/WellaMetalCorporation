<?php

namespace App\Console\Commands;

use App\Mail\LayawayDueReminderMail;
use App\Models\Sale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLayawayRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'layaway:send-reminders {--dry-run : Simulate execution without sending emails or updating records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch expiration reminders to customers with active lay-aways expiring within 14 days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info($isDryRun ? 'Simulating layaway reminder dispatch (--dry-run enabled)...' : 'Checking active lay-aways near due for reminders...');

        $nearDueLayaways = Sale::where('is_archived', false)
            ->where('payment_type', 'layaway')
            ->where('status', 'layaway')
            ->where('remaining_balance', '>', 0)
            ->whereNotNull('layaway_expires_at')
            ->whereDate('layaway_expires_at', '<=', now()->addDays(14))
            ->whereDate('layaway_expires_at', '>=', now())
            ->where(function ($q) {
                $q->whereNull('last_reminder_sent_at')
                    ->orWhere('last_reminder_sent_at', '<=', now()->subDays(3));
            })
            ->with(['customer', 'items.product'])
            ->get();

        if ($nearDueLayaways->isEmpty()) {
            $this->info('No pending lay-away reminders to dispatch today.');

            return self::SUCCESS;
        }

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($nearDueLayaways as $sale) {
            $daysRemaining = max(0, (int) now()->startOfDay()->diffInDays($sale->layaway_expires_at->startOfDay(), false));
            $recipientEmail = $sale->customer_email ?: $sale->customer?->email;

            if (empty($recipientEmail)) {
                $this->warn("Skipped {$sale->sale_number} (Customer: {$sale->customer_name}) - No email address on file.");
                $skippedCount++;

                continue;
            }

            if (! $isDryRun) {
                try {
                    Mail::to($recipientEmail)->send(new LayawayDueReminderMail($sale, $daysRemaining));

                    $sale->forceFill([
                        'last_reminder_sent_at' => now(),
                        'reminder_count' => ($sale->reminder_count ?? 0) + 1,
                    ])->save();

                    Log::info("Dispatched layaway due reminder for sale {$sale->sale_number} to {$recipientEmail}. Days left: {$daysRemaining}");
                    $this->info("✓ Sent reminder for {$sale->sale_number} to {$recipientEmail} ({$daysRemaining} days left).");
                    $sentCount++;
                } catch (\Throwable $e) {
                    Log::error("Failed sending layaway reminder for sale {$sale->sale_number}: {$e->getMessage()}");
                    $this->error("✗ Failed sending for {$sale->sale_number}: {$e->getMessage()}");
                }
            } else {
                $this->line("[DRY-RUN] Would send reminder for {$sale->sale_number} to {$recipientEmail} ({$daysRemaining} days remaining).");
                $sentCount++;
            }
        }

        $this->info("Lay-away reminder processing completed: {$sentCount} processed, {$skippedCount} skipped.");

        return self::SUCCESS;
    }
}
