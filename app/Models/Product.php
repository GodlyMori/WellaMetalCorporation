<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'tagged_price',
        'quantity_in_stock',
        'status', // 'active', 'archived'
        'archived_by',
        'archive_approved_by',
        'archive_reason',
    ];

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
        if ($this->quantity_in_stock <= 4) {
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
}