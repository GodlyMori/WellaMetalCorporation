<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class FigmaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@wellametal.test')->first();

        // 1. Seed Products (Total 12 products across 3 categories = 68 total stock units)
        $products = [
            // SOFAS (Category total stock: 28)
            [
                'id' => 1,
                'name' => 'Milano Sectional Sofa',
                'category' => 'Sofa',
                'description' => '5-seat L-shaped, velvet charcoal',
                'tagged_price' => 1899.00,
                'quantity_in_stock' => 12,
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Avalon 3-Seater Sofa',
                'category' => 'Sofa',
                'description' => 'Classic linen, solid oak legs',
                'tagged_price' => 1299.00,
                'quantity_in_stock' => 8,
                'status' => 'active',
            ],
            [
                'id' => 3,
                'name' => 'Nova Loveseat',
                'category' => 'Sofa',
                'description' => 'Compact 2-seat, microfiber beige',
                'tagged_price' => 849.00,
                'quantity_in_stock' => 3,
                'status' => 'active',
            ],
            [
                'id' => 4,
                'name' => 'Luxe Recliner Sofa',
                'category' => 'Sofa',
                'description' => 'Power recliner, top-grain leather',
                'tagged_price' => 2199.00,
                'quantity_in_stock' => 5,
                'status' => 'active',
            ],

            // DINING TABLES (Category total stock: 22)
            [
                'id' => 5,
                'name' => 'Parma Dining Table',
                'category' => 'Dining Table',
                'description' => '6-seater, solid walnut, 180cm',
                'tagged_price' => 1050.00,
                'quantity_in_stock' => 7,
                'status' => 'active',
            ],
            [
                'id' => 6,
                'name' => 'Siena Extendable Table',
                'category' => 'Dining Table',
                'description' => 'Extends 160–220cm, white oak',
                'tagged_price' => 1350.00,
                'quantity_in_stock' => 4,
                'status' => 'active',
            ],
            [
                'id' => 7,
                'name' => 'Urban Round Table',
                'category' => 'Dining Table',
                'description' => '4-seater, marble top, matte black base',
                'tagged_price' => 720.00,
                'quantity_in_stock' => 2,
                'status' => 'active',
            ],
            [
                'id' => 8,
                'name' => 'Verona Glass Dining Table',
                'category' => 'Dining Table',
                'description' => 'Tempered glass, brushed steel legs',
                'tagged_price' => 1450.00,
                'quantity_in_stock' => 9,
                'status' => 'active',
            ],

            // CLOSET (Category total stock: 18)
            [
                'id' => 9,
                'name' => 'Hampton Walk-In Closet',
                'category' => 'Closet',
                'description' => 'Modular organizer with drawers',
                'tagged_price' => 3200.00,
                'quantity_in_stock' => 6,
                'status' => 'active',
            ],
            [
                'id' => 10,
                'name' => 'Como 2-Door Armoire',
                'category' => 'Closet',
                'description' => 'Solid pine with hanging rail',
                'tagged_price' => 2850.00,
                'quantity_in_stock' => 5,
                'status' => 'active',
            ],
            [
                'id' => 11,
                'name' => 'Venezia Wardrobe',
                'category' => 'Closet',
                'description' => 'Mirrored 3-door wardrobe',
                'tagged_price' => 1750.00,
                'quantity_in_stock' => 7,
                'status' => 'active',
            ],
            [
                'id' => 12,
                'name' => 'Nordic Sliding Closet',
                'category' => 'Closet',
                'description' => 'Minimalist sliding door unit, beech wood',
                'tagged_price' => 2100.00,
                'quantity_in_stock' => 0,
                'status' => 'archived', // Demonstrates archived capability
            ],
        ];

        foreach ($products as $pData) {
            Product::updateOrCreate(['id' => $pData['id']], $pData);
        }

        // 2. Seed Recent Sales (matching Figma screenshot exactly)
        $sales = [
            [
                'sale_number' => 'ORD-2026-001',
                'customer_name' => 'Elena Marchetti',
                'customer_initials' => 'EM',
                'avatar_color' => '#2563eb', // Royal Blue
                'product_id' => 1,
                'product_name' => 'Milano Sectional Sofa',
                'amount' => 1899.00,
                'sale_date' => '2026-09-24',
                'status' => 'completed',
                'created_by' => $admin?->id,
            ],
            [
                'sale_number' => 'ORD-2026-002',
                'customer_name' => 'James Okonkwo',
                'customer_initials' => 'JO',
                'avatar_color' => '#ea580c', // Orange
                'product_id' => 5,
                'product_name' => 'Parma Dining Table',
                'amount' => 2100.00,
                'sale_date' => '2026-09-23',
                'status' => 'completed',
                'created_by' => $admin?->id,
            ],
            [
                'sale_number' => 'ORD-2026-003',
                'customer_name' => 'Sofia Reyes',
                'customer_initials' => 'SR',
                'avatar_color' => '#7c3aed', // Purple
                'product_id' => 11,
                'product_name' => 'Venezia Wardrobe',
                'amount' => 1750.00,
                'sale_date' => '2026-09-22',
                'status' => 'pending',
                'created_by' => $admin?->id,
            ],
            [
                'sale_number' => 'ORD-2026-004',
                'customer_name' => 'David Chen',
                'customer_initials' => 'DC',
                'avatar_color' => '#16a34a', // Emerald Green
                'product_id' => 4,
                'product_name' => 'Luxe Recliner Sofa',
                'amount' => 2199.00,
                'sale_date' => '2026-09-21',
                'status' => 'completed',
                'created_by' => $admin?->id,
            ],
            [
                'sale_number' => 'ORD-2026-005',
                'customer_name' => 'Amara Diallo',
                'customer_initials' => 'AD',
                'avatar_color' => '#e11d48', // Rose Red
                'product_id' => 6,
                'product_name' => 'Siena Extendable Table',
                'amount' => 1350.00,
                'sale_date' => '2026-09-20',
                'status' => 'completed',
                'created_by' => $admin?->id,
            ],
            [
                'sale_number' => 'ORD-2026-006',
                'customer_name' => 'Lucas Petrov',
                'customer_initials' => 'LP',
                'avatar_color' => '#15803d', // Forest Green
                'product_id' => 2,
                'product_name' => 'Avalon 3-Seater Sofa',
                'amount' => 2598.00,
                'sale_date' => '2026-09-19',
                'status' => 'cancelled',
                'created_by' => $admin?->id,
            ],
        ];

        foreach ($sales as $sData) {
            Sale::updateOrCreate(['sale_number' => $sData['sale_number']], $sData);
        }
    }
}
