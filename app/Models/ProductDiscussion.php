<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDiscussion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_simple_id',
        'user_id',
        'author_name',
        'content',
        'parent_id',
        'like_count',
        'status',
    ];

    protected $casts = [
        'like_count' => 'integer',
    ];

    /**
     * Relationships
     */
    public function product()
    {
        return $this->belongsTo(ProductSimple::class, 'product_simple_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ProductDiscussion::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ProductDiscussion::class, 'parent_id')
            ->where('status', 'approved')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get display name (user name or author_name)
     */
    public function getDisplayNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->author_name ?? 'Người dùng ẩn danh';
    }

    /**
     * Get avatar initial
     */
    public function getAvatarInitialAttribute()
    {
        $name = $this->display_name;
        return strtoupper(substr($name, 0, 1));
    }
}
