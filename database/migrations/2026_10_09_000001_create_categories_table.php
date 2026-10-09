<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed initial standard categories
        $defaultCategories = [
            'Sala Set' => 'Living room sofa sets, center tables, and metal loungers',
            'Wardrobe' => 'Heavy-duty steel clothes lockers and modular wardrobes',
            'Dining Set' => 'Wrought iron dining tables and matching chairs',
            'Bed Frame' => 'Tubular steel single, queen, and double-deck bed frames',
            'Mattresses' => 'High-density foam and orthopedic spring mattresses',
            'Closet' => 'Multi-tier storage closets and metal utility organizers',
            'Garden Set' => 'Weather-resistant outdoor patio sets and garden benches',
            'Office Furniture' => 'Clerical steel desks, filing cabinets, and ergonomic swivel chairs',
        ];

        foreach ($defaultCategories as $name => $desc) {
            DB::table('categories')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $desc,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add category_id foreign key to products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->nullOnDelete();
        });

        // Link existing products to the corresponding category_id
        $categories = DB::table('categories')->get();
        foreach ($categories as $cat) {
            DB::table('products')->where('category', $cat->name)->update(['category_id' => $cat->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('categories');
    }
};
