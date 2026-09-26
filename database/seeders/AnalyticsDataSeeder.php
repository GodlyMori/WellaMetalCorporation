<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AnalyticsDataSeeder extends Seeder
{
    public function run(): void
    {
        // Check if we already have sufficient historical sales
        if (Sale::count() >= 25) {
            return;
        }

        $admin = User::where('email', 'admin@wellametal.test')->first() ?? User::first();
        $manager = User::where('email', 'manager@wellametal.test')->first() ?? $admin;
        $secretary = User::where('email', 'secretary@wellametal.test')->first() ?? $admin;

        $staffUsers = [$admin->id, $manager->id, $secretary->id];

        $products = Product::where('status', 'active')->get();
        if ($products->isEmpty()) {
            return;
        }

        $activePromo = Promotion::where('status', 'active')->first();

        // Sample realistic customer names and locations in Davao / Mindanao
        $customers = [
            ['name' => 'Maria Theresa Santos', 'phone' => '0917-823-4411', 'address' => 'Bajada, Davao City'],
            ['name' => 'Roberto Carlos Alcantara', 'phone' => '0920-551-8920', 'address' => 'Lanang, Davao City'],
            ['name' => 'Grace Joy Dimaculangan', 'phone' => '0918-332-9012', 'address' => 'Matina Aplaya, Davao City'],
            ['name' => 'Engr. Ferdinand Marcos Jr.', 'phone' => '0922-441-2300', 'address' => 'Buhangin, Davao City'],
            ['name' => 'Atty. Patricia Mae Lim', 'phone' => '0919-772-1144', 'address' => 'Toril, Davao City'],
            ['name' => 'Michael Angelo Reyes', 'phone' => '0927-661-8899', 'address' => 'Tagum City, Davao del Norte'],
            ['name' => 'Lourdes Cristina Tan', 'phone' => '0915-442-9988', 'address' => 'Panabo City, Davao del Norte'],
            ['name' => 'Gabriel Alfonso Cruz', 'phone' => '0939-112-4455', 'address' => 'Ecoland, Davao City'],
            ['name' => 'Stephanie Ann Villanueva', 'phone' => '0917-991-3322', 'address' => 'Cabantian, Davao City'],
            ['name' => 'Dra. Beatrice Angela Co', 'phone' => '0928-883-2211', 'address' => 'Obrero, Davao City'],
            ['name' => 'Danilo Jose Gomez', 'phone' => '0916-554-7711', 'address' => 'Calinan, Davao City'],
            ['name' => 'Karen Sophia Del Rosario', 'phone' => '0921-338-9900', 'address' => 'Tibungco, Davao City'],
            ['name' => 'Arch. Jonathan Kyle Uy', 'phone' => '0918-776-5544', 'address' => 'Indangan, Davao City'],
            ['name' => 'Rowena Isabel Bautista', 'phone' => '0926-443-1188', 'address' => 'Bangkal, Davao City'],
            ['name' => 'Capt. Manuel Salvador Jr.', 'phone' => '0917-229-8833', 'address' => 'Sasa, Davao City'],
            ['name' => 'Carmela Jane Soriano', 'phone' => '0929-665-4411', 'address' => 'Catalunan Grande, Davao City'],
            ['name' => 'Vincent Patrick Lee', 'phone' => '0915-331-7766', 'address' => 'Agdao, Davao City'],
            ['name' => 'Monique Denise Ramos', 'phone' => '0920-884-3322', 'address' => 'Mintal, Davao City'],
            ['name' => 'Benjamin Arthur Te', 'phone' => '0919-224-6677', 'address' => 'Toril, Davao City'],
            ['name' => 'Joanna Marie Yap', 'phone' => '0927-448-1199', 'address' => 'Buhangin, Davao City'],
        ];

        $colors = ['#2563eb', '#ea580c', '#7c3aed', '#16a34a', '#e11d48', '#0284c7', '#059669', '#d97706'];

        // Dates spanning July 2026, August 2026, and September 2026
        $dates = [
            '2026-07-05', '2026-07-12', '2026-07-18', '2026-07-24', '2026-07-29',
            '2026-08-03', '2026-08-08', '2026-08-14', '2026-08-19', '2026-08-23', '2026-08-28',
            '2026-09-02', '2026-09-06', '2026-09-10', '2026-09-14', '2026-09-17', '2026-09-21', '2026-09-24', '2026-09-25'
        ];

        $orderIndex = Sale::count() + 1;

        foreach ($dates as $idx => $dateStr) {
            $customer = $customers[$idx % count($customers)];
            $product = $products->random();
            $qty = rand(1, 2);
            $itemTotal = (float)$product->tagged_price * $qty;

            // Decide payment type: ~35% layaway, ~65% full
            $isLayaway = ($idx % 3 === 0);
            $paymentType = $isLayaway ? 'layaway' : 'full';

            // Discount / Promo
            $usePromo = ($activePromo && $idx % 4 === 0);
            $discountAmount = 0.0;
            $promoId = null;
            $promoName = null;

            if ($usePromo) {
                $discountAmount = round($itemTotal * 0.10, 2); // 10% promo
                $promoId = $activePromo->id;
                $promoName = $activePromo->name;
            }

            $netAmount = max(0.0, $itemTotal - $discountAmount);

            if ($isLayaway) {
                // Downpayment 30% to 50%
                $downpaymentRatio = rand(30, 50) / 100.0;
                $downpayment = round($netAmount * $downpaymentRatio, 2);
                $remainingBalance = round($netAmount - $downpayment, 2);
                $status = 'layaway';
                $expiryDate = Carbon::parse($dateStr)->addMonths(3)->toDateString();
            } else {
                $downpayment = $netAmount;
                $remainingBalance = 0.0;
                $status = 'completed';
                $expiryDate = null;
            }

            $saleNumber = 'ORD-2026-' . str_pad($orderIndex++, 3, '0', STR_PAD_LEFT);
            $creatorId = $staffUsers[$idx % count($staffUsers)];
            $color = $colors[$idx % count($colors)];

            $sale = Sale::create([
                'sale_number' => $saleNumber,
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'customer_address' => $customer['address'],
                'avatar_color' => $color,
                'product_id' => $product->id,
                'product_name' => $product->name . ($qty > 1 ? " (x{$qty})" : ''),
                'promotion_id' => $promoId,
                'promo_name' => $promoName,
                'amount' => $netAmount,
                'original_amount' => $itemTotal,
                'discount_amount' => $discountAmount,
                'payment_type' => $paymentType,
                'downpayment_amount' => $isLayaway ? $downpayment : 0,
                'amount_paid' => $downpayment,
                'remaining_balance' => $remainingBalance,
                'layaway_expires_at' => $expiryDate,
                'last_payment_date' => $dateStr,
                'sale_date' => $dateStr,
                'status' => $status,
                'is_archived' => false,
                'notes' => $isLayaway ? 'Lay-Away transaction. Initial downpayment settled.' : 'Standard retail cash sale.',
                'created_by' => $creatorId,
                'created_at' => Carbon::parse($dateStr . ' 10:30:00'),
                'updated_at' => Carbon::parse($dateStr . ' 10:30:00'),
            ]);

            // Create SaleItem
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $product->tagged_price,
                'subtotal' => $netAmount,
                'created_at' => Carbon::parse($dateStr . ' 10:30:00'),
                'updated_at' => Carbon::parse($dateStr . ' 10:30:00'),
            ]);
        }

        // Also ensure all existing sales have SaleItems
        foreach (Sale::all() as $s) {
            if ($s->product_id && $s->items()->count() === 0) {
                SaleItem::create([
                    'sale_id' => $s->id,
                    'product_id' => $s->product_id,
                    'quantity' => 1,
                    'unit_price' => $s->amount,
                    'subtotal' => $s->amount,
                    'created_at' => $s->created_at ?? now(),
                    'updated_at' => $s->updated_at ?? now(),
                ]);
            }
        }
    }
}
