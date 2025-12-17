<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'buyer_id',
        'steam_account_id',
        'product_simple_id',
        'amount',
        'fee',
        'payment_method',
        'status',
        'notes',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function steamAccount()
    {
        return $this->belongsTo(SteamAccount::class);
    }

    public function game()
    {
        return $this->belongsTo(ProductSimple::class, 'product_simple_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Generate unique order code
     */
    public static function generateOrderCode()
    {
        do {
            $code = 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        } while (self::where('order_code', $code)->exists());

        return $code;
    }
}

