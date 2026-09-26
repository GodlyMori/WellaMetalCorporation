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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('category'); // chair, table, bed, cabinet, outdoor, other
        $table->text('description')->nullable();
        $table->decimal('tagged_price', 10, 2);
        $table->integer('quantity_in_stock')->default(0);
        $table->string('status')->default('active'); // active or archived
        $table->timestamps();
        $table->softDeletes(); // safety net
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
