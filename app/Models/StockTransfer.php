<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_branch',
        'received_by',
        'date_received',
        'notes',
    ];

    protected $casts = [
        'date_received' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}