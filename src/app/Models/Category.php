<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ← 追加

class Category extends Model
{
    use HasFactory; // ← 追加

    protected $fillable = ['name'];

    public function items()
    {
        return $this->belongsToMany(Item::class);
    }
}
