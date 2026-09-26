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
        // 1. Promotions Table
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Kadayawan Festival Sale", "Valentine's Day Promo"
            $table->string('code')->nullable()->unique(); // e.g. "KADAYAWAN2026", "VALENTINE"
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // e.g. 10.00 (%) or 1000.00 (PHP)
            $table->string('applicable_category')->default('ALL'); // 'ALL', 'Sofa', 'Dining Table', 'Closet'
            $table->decimal('min_order_amount', 10, 2)->default(0.00);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->enum('status', ['active', 'pending_approval', 'inactive', 'rejected'])->default('pending_approval');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Add promo columns to sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('promotion_id')->nullable()->after('product_name')->constrained('promotions')->nullOnDelete();
            $table->string('promo_name')->nullable()->after('promotion_id');
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('amount');
            $table->decimal('original_amount', 10, 2)->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['promotion_id']);
            $table->dropColumn(['promotion_id', 'promo_name', 'discount_amount', 'original_amount']);
        });

        Schema::dropIfExists('promotions');
    }
};
