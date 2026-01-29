<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'postcode',
        'address',
        'building',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 出品商品
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // いいね
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 購入履歴
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function isProfileIncomplete(): bool
    {
        return empty($this->name)
            || empty($this->postcode)
            || empty($this->address);
        
    }
}
