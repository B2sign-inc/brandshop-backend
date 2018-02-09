<?php


namespace App\Brandshop\Shipping\Validator;


use App\Brandshop\Shipping\Exceptions\InvalidAddressException;
use App\Models\Address;

class AddressValidator
{

    /**
     * @return boolean
     * @throws InvalidAddressException
     */
    public function validate(Address $address)
    {
        // TODO validate address
        return true;
    }
}