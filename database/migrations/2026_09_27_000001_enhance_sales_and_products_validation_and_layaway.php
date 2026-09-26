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
            $table->string('customer_phone', 50)->nullable()->after('customer_name');
            $table->text('customer_address')->nullable()->after('customer_phone');
            $table->string('payment_type', 30)->default('full')->after('amount'); // 'full', 'layaway'
            $table->decimal('downpayment_amount', 12, 2)->default(0)->after('payment_type');
            $table->decimal('amount_paid', 12, 2)->default(0)->after('downpayment_amount');
            $table->decimal('remaining_balance', 12, 2)->default(0)->after('amount_paid');
            $table->date('layaway_expires_at')->nullable()->after('remaining_balance');
            $table->date('last_payment_date')->nullable()->after('layaway_expires_at');
            $table->foreignId('archived_by')->nullable()->after('is_archived')->constrained('users')->nullOnDelete();
            $table->foreignId('archive_approved_by')->nullable()->after('archived_by')->constrained('users')->nullOnDelete();
            $table->text('archive_reason')->nullable()->after('archive_approved_by');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('archived_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('archive_approved_by')->nullable()->after('archived_by')->constrained('users')->nullOnDelete();
            $table->text('archive_reason')->nullable()->after('archive_approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
            $table->dropForeign(['archive_approved_by']);
            $table->dropColumn([
                'customer_phone',
                'customer_address',
                'payment_type',
                'downpayment_amount',
                'amount_paid',
                'remaining_balance',
                'layaway_expires_at',
                'last_payment_date',
                'archived_by',
                'archive_approved_by',
                'archive_reason',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
            $table->dropForeign(['archive_approved_by']);
            $table->dropColumn([
                'archived_by',
                'archive_approved_by',
                'archive_reason',
            ]);
        });
    }
};
