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
        'type',
        'tags',
        'view_count',
    ];
    #chuyển đổi kiểu dữ liệu
    protected $casts = [
        'tags' => 'array',
        'view_count' => 'integer',
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

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product', 'product_simple_id', 'promotion_id')
                    ->withTimestamps();
    }

    public function getActivePromotion()
    {
        return $this->promotions()
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now())
                    ->orderByDesc('discount_percent')
                    ->first();
    }

    /**
     * Parse current price from price string (last number with đ/₫).
     * E.g. "1.499.000 ₫ 120.000 ₫" -> 120000; "800.000đ" -> 800000
     */
    protected function parseCurrentPrice(?string $priceStr): float
    {
        if (!$priceStr || trim($priceStr) === '') {
            return 0;
        }
        if (preg_match_all('/[\d.,]+\s*[₫đ]/ui', $priceStr, $matches) && !empty($matches[0])) {
            $lastPrice = trim(end($matches[0]));
            $cleaned = preg_replace('/[₫đ\s]/ui', '', $lastPrice);
            $cleaned = str_replace(['.', ','], '', $cleaned);
            $amount = (float) $cleaned;
            if ($amount > 0) {
                return $amount;
            }
        }
        if (preg_match_all('/[\d.,]+/', $priceStr, $matches) && !empty($matches[0])) {
            $lastNumber = end($matches[0]);
            $cleaned = str_replace(['.', ','], '', $lastNumber);
            $amount = (float) $cleaned;
            if ($amount > 0) {
                return $amount;
            }
        }
        return 0;
    }

    public function getSalePrice()
    {
        try {
            $promo = $this->getActivePromotion();
            if (!$promo) {
                return null;
            }

            $numericPrice = $this->parseCurrentPrice($this->price ?? '');
            if ($numericPrice <= 0) {
                return null;
            }

            $salePrice = $numericPrice * (1 - $promo->discount_percent / 100);
            $result = [
                'sale_price' => round($salePrice),
                'discount_percent' => $promo->discount_percent,
                'promotion_name' => $promo->name,
                'promotion_ends_at' => $promo->ends_at ? $promo->ends_at->toIso8601String() : null,
            ];
            return $result;
        } catch (\Throwable $e) {
            \Log::warning('ProductSimple::getSalePrice failed', ['product_id' => $this->id, 'message' => $e->getMessage()]);
            return null;
        }
    }
}
