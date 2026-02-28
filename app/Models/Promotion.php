<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discount_percent',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'discount_percent' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(ProductSimple::class, 'promotion_product', 'promotion_id', 'product_simple_id')
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('starts_at', '<=', now())
                     ->where('ends_at', '>=', now());
    }
}
