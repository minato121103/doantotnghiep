<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'content',
        'images',
        'videos',
        'game_preference',
        'likes_count',
        'comments_count',
        'privacy',
        'is_active'
    ];
    
    protected $casts = [
        'images' => 'array',
        'videos' => 'array',
        'is_active' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
