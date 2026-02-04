<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, TwoFactorAuthenticatable, MustVerifyEmailTrait;
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
