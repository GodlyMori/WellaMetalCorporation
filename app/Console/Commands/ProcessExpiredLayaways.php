<?php

namespace App\Console\Commands;

use App\Services\LayawayService;
use Illuminate\Console\Command;

class ProcessExpiredLayaways extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'layaway:process-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find past-expiration layaway orders, restore reserved stock, and mark orders cancelled.';

    /**
     * Execute the console command.
     */
    public function handle(LayawayService $service): int
    {
        $this->info('Scanning for expired lay-away orders...');

        $processed = $service->processExpiredLayaways();

        $this->info("Completed: {$processed} expired lay-away order(s) processed and stock restored.");

        return Command::SUCCESS;
    }
}
