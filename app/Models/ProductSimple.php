<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSimple extends Model
{
    use HasFactory;
    #setup API model
    protected $table = 'product_simple'; 

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'image',
        'title',
        'price',
        'short_description',
        'detail_description',
        'category',
        'tags',
        'view_count',
        'rating_count',
        'average_rating',
    ];
    #chuyển đổi kiểu dữ liệu
    protected $casts = [
        'tags' => 'array',
        'view_count' => 'integer',
        'rating_count' => 'integer',
        'average_rating' => 'decimal:2',
    ];

    public function getRouteKeyName()
    {
        return 'id';
    }
    #quan hệ 
    public function steamAccounts()
    {
        return $this->belongsToMany(SteamAccount::class, 'steam_account_games', 'product_simple_id', 'steam_account_id')
                    ->withPivot('is_highlighted')
                    ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'product_simple_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_simple_id');
    }
}
