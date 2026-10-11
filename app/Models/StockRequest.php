<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'product_id',
        'requested_by',
        'quantity_requested',
        'reason',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'received_by',
        'received_at',
        'received_notes',
    ];

    protected $casts = [
        'quantity_requested' => 'integer',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => [
                'label' => 'Pending Approval',
                'bg' => 'bg-amber-50 dark:bg-amber-950/40',
                'text' => 'text-amber-700 dark:text-amber-400',
                'border' => 'border-amber-200 dark:border-amber-900',
            ],
            'awaiting_delivery' => [
                'label' => 'Awaiting Delivery',
                'bg' => 'bg-blue-50 dark:bg-blue-950/40',
                'text' => 'text-blue-700 dark:text-blue-400',
                'border' => 'border-blue-200 dark:border-blue-900',
            ],
            'received' => [
                'label' => 'Delivery Received',
                'bg' => 'bg-emerald-50 dark:bg-emerald-950/40',
                'text' => 'text-emerald-700 dark:text-emerald-400',
                'border' => 'border-emerald-200 dark:border-emerald-900',
            ],
            'rejected' => [
                'label' => 'Rejected',
                'bg' => 'bg-rose-50 dark:bg-rose-950/40',
                'text' => 'text-rose-700 dark:text-rose-400',
                'border' => 'border-rose-200 dark:border-rose-900',
            ],
            default => [
                'label' => ucfirst($this->status),
                'bg' => 'bg-slate-100 dark:bg-slate-800',
                'text' => 'text-slate-700 dark:text-slate-300',
                'border' => 'border-slate-200 dark:border-slate-700',
            ],
        };
    }
}
