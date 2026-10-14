<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionRedemption extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'promotion_id',
        'customer_id',
        'sale_id',
        'created_at',
    ];

    /**
     * The promotion that was redeemed.
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * The customer who redeemed the promotion.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * The sale on which the promotion was redeemed.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
