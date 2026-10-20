<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'sale_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_email',
        'customer_initials',
        'avatar_color',
        'product_id',
        'product_name',
        'promotion_id',
        'promo_name',
        'amount',
        'discount_amount',
        'original_amount',
        'payment_type', // 'full', 'layaway'
        'downpayment_amount',
        'amount_paid',
        'remaining_balance',
        'layaway_expires_at',
        'last_payment_date',
        'sale_date',
        'status', // 'completed', 'pending', 'cancelled', 'layaway'
        'is_archived',
        'notes',
        'created_by',
        'archived_by',
        'archive_approved_by',
        'archive_reason',
        'customer_email',
        'last_reminder_sent_at',
        'reminder_count',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'deposit_disposition',
        'forfeited_deposit_amount',
        'refunded_deposit_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'downpayment_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'forfeited_deposit_amount' => 'decimal:2',
        'refunded_deposit_amount' => 'decimal:2',
        'layaway_expires_at' => 'date',
        'last_payment_date' => 'date',
        'sale_date' => 'date',
        'last_reminder_sent_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_count' => 'integer',
        'is_archived' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function promotionRedemption()
    {
        return $this->hasOne(PromotionRedemption::class);
    }

    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function archiveApprover()
    {
        return $this->belongsTo(User::class, 'archive_approved_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getComputedInitialsAttribute(): string
    {
        if (! empty($this->customer_initials)) {
            return $this->customer_initials;
        }
        $parts = explode(' ', trim($this->customer_name));
        $initials = '';
        foreach ($parts as $p) {
            $initials .= strtoupper(substr($p, 0, 1));
        }

        return substr($initials, 0, 2);
    }
}
