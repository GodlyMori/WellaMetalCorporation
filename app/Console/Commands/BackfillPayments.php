<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely and idempotently backfill missing payment ledger records for legacy sales.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting historical payment ledger backfill...');

        $sales = Sale::withTrashed()->get();

        $inspectedCount = 0;
        $createdCount = 0;
        $skippedExistingCount = 0;
        $skippedZeroAmountCount = 0;

        foreach ($sales as $sale) {
            $inspectedCount++;

            // Rule 1: Skip if amount_paid is zero or negative
            if ((float)$sale->amount_paid <= 0) {
                $skippedZeroAmountCount++;
                continue;
            }

            // Rule 2 & 3: Skip if payment records already exist for this sale
            if ($sale->payments()->exists()) {
                $skippedExistingCount++;
                continue;
            }

            // Rule 4: Create exactly ONE historical Payment record
            $paymentDate = $sale->last_payment_date 
                ? Carbon::parse($sale->last_payment_date)->startOfDay() 
                : ($sale->sale_date ? Carbon::parse($sale->sale_date)->startOfDay() : now());

            Payment::create([
                'sale_id' => $sale->id,
                'amount' => $sale->amount_paid,
                'payment_date' => $paymentDate,
                'payment_method' => 'cash',
                'reference_number' => null,
                'notes' => 'Historical payment balance imported during payment ledger backfill',
                'recorded_by' => $sale->created_by,
            ]);

            $createdCount++;
        }

        $this->newLine();
        $this->info('--- Payment Ledger Backfill Summary ---');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Sales Inspected', $inspectedCount],
                ['Payment Records Created', $createdCount],
                ['Skipped (Payments Already Existed)', $skippedExistingCount],
                ['Skipped (Amount Paid Was Zero)', $skippedZeroAmountCount],
            ]
        );

        $this->info("Backfill complete. {$createdCount} payment record(s) created.");

        return Command::SUCCESS;
    }
}
