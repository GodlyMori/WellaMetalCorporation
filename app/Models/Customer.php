<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_number',
        'name',
        'phone',
        'address',
        'email',
        'notes',
        'total_orders_count',
        'total_spent',
        'created_by',
    ];

    protected $casts = [
        'total_orders_count' => 'integer',
        'total_spent' => 'decimal:2',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        $initials = '';
        foreach ($parts as $p) {
            $initials .= strtoupper(substr($p, 0, 1));
        }
        return substr($initials, 0, 2) ?: 'CU';
    }
}
