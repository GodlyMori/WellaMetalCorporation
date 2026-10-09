<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    public const CATEGORIES = [
        'Sala Set',
        'Wardrobe',
        'Dining Set',
        'Bed Frame',
        'Mattresses',
        'Closet',
        'Garden Set',
        'Office Furniture',
    ];

    protected $fillable = [
        'name',
        'category',
        'category_id',
        'description',
        'tagged_price',
        'quantity_in_stock',
        'low_stock_threshold',
        'status', // 'active', 'archived'
        'archived_by',
        'archive_approved_by',
        'archive_reason',
    ];

    public function categoryRef()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function archiveApprover()
    {
        return $this->belongsTo(User::class, 'archive_approved_by');
    }

    protected $casts = [
        'tagged_price' => 'decimal:2',
        'quantity_in_stock' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    public function getFormattedIdAttribute(): string
    {
        return sprintf('P%03d', $this->id);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->status === 'archived') {
            return 'Archived';
        }
        if ($this->quantity_in_stock <= 0) {
            return 'Out of Stock';
        }
        $threshold = $this->low_stock_threshold ?? 5;
        if ($this->quantity_in_stock <= $threshold) {
            return 'Low Stock';
        }
        return 'In Stock';
    }

    public function stockTransferItems()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryAdjustments()
    {
        return $this->hasMany(InventoryAdjustment::class);
    }
}