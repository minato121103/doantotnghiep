<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SteamAccountGame extends Model
{
    use HasFactory;

    protected $table = 'steam_account_games';

    protected $fillable = [
        'steam_account_id',
        'product_simple_id',
        'is_highlighted',
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function steamAccount()
    {
        return $this->belongsTo(SteamAccount::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductSimple::class, 'product_simple_id');
    }
}
