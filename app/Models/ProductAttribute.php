<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
