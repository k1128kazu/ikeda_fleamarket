<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory; // ← 追加

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'brand',
        'condition',   // ★ 統一
        'description',
        'image_path',
        'is_sold',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
    /**
     * この商品をログインユーザーがいいねしているか判定
     */
    public function isLikedBy($user): bool
    {
        return $this->likes
            ->where('user_id', $user->id)
            ->isNotEmpty();
    }
}
