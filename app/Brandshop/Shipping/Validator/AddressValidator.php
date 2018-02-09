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
    public function validate(Address $address, $soft = false)
    {
        // TODO validate address
        if (trim($address->street_address) === 'test') {
            if ($soft) {
                return false;
            } else {
                throw new InvalidAddressException('Your address is invalid.');
            }
        }
        return true;
    }
}