<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'status',
        'balance',
        'total_orders',
        'total_spent',
        'address',
        'birthday',
        'gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date',
        'balance' => 'decimal:2',
        'total_orders' => 'integer',
        'total_spent' => 'integer',
    ];

    /**
     * Relationships
     */
    
    // Đơn hàng mà user này mua
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    // Lịch sử giao dịch
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Đánh giá của user
    public function reviews()
    {
        return $this->hasMany(Review::class, 'buyer_id');
    }

    // Bài viết mà user này viết (nếu model Article tồn tại)
    // public function articles()
    // {
    //     return $this->hasMany(Article::class, 'author_id');
    // }

    // Thông báo (nếu model Notification tồn tại)
    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class);
    // }
}
