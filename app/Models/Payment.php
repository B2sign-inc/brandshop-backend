<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['amount', 'transaction'];

    protected $casts = [
        'transaction' => 'object',
    ];
}
