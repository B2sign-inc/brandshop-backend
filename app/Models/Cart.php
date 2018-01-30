<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['quantity', 'product_id'];
    protected $casts = [
        'user_id' => 'int',
        'product_id' => 'int',
    ];

    public function product()
    {
        return $this->hasOne(Product::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
