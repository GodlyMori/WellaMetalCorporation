<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'last_reminder_sent_at')) {
                $table->timestamp('last_reminder_sent_at')->nullable()->after('layaway_expires_at');
            }
            if (! Schema::hasColumn('sales', 'reminder_count')) {
                $table->integer('reminder_count')->default(0)->after('last_reminder_sent_at');
            }
            if (! Schema::hasColumn('sales', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('sales', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
            if (! Schema::hasColumn('sales', 'cancellation_reason')) {
                $table->string('cancellation_reason', 255)->nullable()->after('cancelled_at');
            }
            if (! Schema::hasColumn('sales', 'deposit_disposition')) {
                $table->string('deposit_disposition', 50)->nullable()->after('cancellation_reason'); // forfeited, refunded, none
            }
            if (! Schema::hasColumn('sales', 'forfeited_deposit_amount')) {
                $table->decimal('forfeited_deposit_amount', 12, 2)->default(0.00)->after('deposit_disposition');
            }
            if (! Schema::hasColumn('sales', 'refunded_deposit_amount')) {
                $table->decimal('refunded_deposit_amount', 12, 2)->default(0.00)->after('forfeited_deposit_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach ([
                'reminder_count',
                'last_reminder_sent_at',
                'refunded_deposit_amount',
                'forfeited_deposit_amount',
                'deposit_disposition',
                'cancellation_reason',
                'cancelled_at',
                'cancelled_by',
            ] as $col) {
                if (Schema::hasColumn('sales', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
