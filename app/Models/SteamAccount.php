<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class SteamAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'username',
        'password',
        'email',
        'email_password',
        'status',
        'sold_at',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'email_password',
    ];

    /**
     * Relationships
     */
    public function games()
    {
        return $this->belongsToMany(ProductSimple::class, 'steam_account_games', 'steam_account_id', 'product_simple_id')
                    ->withPivot('is_highlighted')
                    ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope để tìm tài khoản có game cụ thể
     */
    public function scopeHasGame($query, $productSimpleId)
    {
        return $query->whereHas('games', function($q) use ($productSimpleId) {
            $q->where('product_simple.id', $productSimpleId);
        })->where('status', 'available');
    }

    /**
     * Accessors - Mã hóa/giải mã password
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getPasswordAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setEmailPasswordAttribute($value)
    {
        $this->attributes['email_password'] = Crypt::encryptString($value);
    }

    public function getEmailPasswordAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }
}

