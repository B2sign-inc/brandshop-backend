<?php

namespace App\Models;

use App\Brandshop\Shipping\Exceptions\InvalidAddressException;
use App\Brandshop\Shipping\Validator\AddressValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'street_address', 'extra_address', 'postcode',
        'city', 'state', 'telephone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return bool
     * @throws InvalidAddressException
     */
    public function validate($soft = false)
    {
        return App::make(AddressValidator::class)->validate($this, $soft);
    }
}
