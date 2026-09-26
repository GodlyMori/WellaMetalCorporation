<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_type', // 'percentage', 'fixed'
        'discount_value',
        'applicable_category', // 'ALL', 'Sofa', 'Dining Table', 'Closet'
        'min_order_amount',
        'starts_at',
        'ends_at',
        'status', // 'active', 'pending_approval', 'inactive', 'rejected'
        'created_by',
        'approved_by',
        'approved_at',
        'admin_notes',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'approved_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Scope: Promos that are active, approved by admin, and currently within date range
     */
    public function scopeAvailableForSales($query)
    {
        $today = now()->toDateString();
        return $query->where('status', 'active')
            ->whereDate('starts_at', '<=', $today)
            ->whereDate('ends_at', '>=', $today);
    }

    /**
     * Check if promo is currently valid by date
     */
    public function getIsDateValidAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->starts_at->toDateString() <= $today && $this->ends_at->toDateString() >= $today;
    }

    /**
     * Compute discount given an array of cart items:
     * [ ['product_id' => ..., 'quantity' => ..., 'unit_price' => ..., 'subtotal' => ...] ]
     */
    public function calculateDiscount(array $items): float
    {
        $productIds = collect($items)->pluck('product_id')->filter()->unique()->all();
        if (empty($productIds)) {
            return 0.0;
        }

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $eligibleSubtotal = 0.0;
        foreach ($items as $item) {
            $pId = $item['product_id'] ?? null;
            $product = $products->get($pId);
            if (!$product) {
                continue;
            }

            // Check category eligibility
            if ($this->applicable_category === 'ALL' || strcasecmp($this->applicable_category, $product->category) === 0) {
                $eligibleSubtotal += (float)($item['subtotal'] ?? 0);
            }
        }

        // Check minimum order requirement
        if ($eligibleSubtotal <= 0 || ($this->min_order_amount > 0 && $eligibleSubtotal < (float)$this->min_order_amount)) {
            return 0.0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = $eligibleSubtotal * ((float)$this->discount_value / 100.0);
        } else {
            // Fixed cash discount
            $discount = min($eligibleSubtotal, (float)$this->discount_value);
        }

        return round(max(0.0, $discount), 2);
    }
}
