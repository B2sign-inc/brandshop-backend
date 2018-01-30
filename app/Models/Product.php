<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'images' => 'array',
    ];

    public function category()
    {
        return $this->hasOne(Category::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
