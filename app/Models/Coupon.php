<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function scopeValid($query)
    {
        return $query->where('starts_at', '<=', now())
                     ->where('ends_at', '>=', now())
                     ->where(function ($q) {
                         $q->whereNull('max_uses')
                           ->orWhereColumn('used_count', '<', 'max_uses');
                     });
    }

    public function isValid(): bool
    {
        $now = now();
        if ($this->starts_at > $now || $this->ends_at < $now) {
            return false;
        }
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }
        return true;
    }

    public function applyToTotal(float $total): float
    {
        if ($this->min_order_amount && $total < (float) $this->min_order_amount) {
            return 0;
        }

        if ($this->type === 'percent') {
            return round($total * (float) $this->value / 100, 2);
        }

        return min((float) $this->value, $total);
    }
}
