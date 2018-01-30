<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'street_address', 'extra_address', 'postcode',
        'city', 'state', 'telephone',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
