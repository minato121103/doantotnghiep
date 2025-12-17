<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_code',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'payment_method',
        'description',
        'order_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Generate unique transaction code
     */
    public static function generateTransactionCode($type = 'TXN')
    {
        do {
            $code = $type . date('Ymd') . strtoupper(substr(uniqid(), -8));
        } while (self::where('transaction_code', $code)->exists());

        return $code;
    }
}

