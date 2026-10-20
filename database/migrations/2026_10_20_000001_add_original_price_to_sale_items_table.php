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
        if (Schema::hasTable('sale_items') && ! Schema::hasColumn('sale_items', 'original_price')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->decimal('original_price', 12, 2)->nullable()->after('quantity');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sale_items') && Schema::hasColumn('sale_items', 'original_price')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropColumn('original_price');
            });
        }
    }
};
