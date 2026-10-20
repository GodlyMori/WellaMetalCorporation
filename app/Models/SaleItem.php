<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'original_price',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'original_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function getIsNegotiatedAttribute(): bool
    {
        return $this->original_price !== null && (float) $this->unit_price < (float) $this->original_price;
    }

    public function getSrpPriceAttribute(): float
    {
        return (float) ($this->original_price ?? $this->unit_price);
    }

    public function getConcessionAmountAttribute(): float
    {
        if ($this->original_price === null) {
            return 0.0;
        }

        return max(0.0, ((float) $this->original_price - (float) $this->unit_price) * (int) $this->quantity);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
