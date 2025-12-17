<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'steam_account_id',
        'product_simple_id',
        'steam_credentials',
        'price',
    ];

    protected $casts = [
        'steam_credentials' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function steamAccount()
    {
        return $this->belongsTo(SteamAccount::class);
    }

    public function game()
    {
        return $this->belongsTo(ProductSimple::class, 'product_simple_id');
    }
}

