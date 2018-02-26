<?php


namespace App\Brandshop\Shipping\Validator;


use App\Brandshop\Shipping\Exceptions\InvalidAddressException;
use App\Models\Address;
use Hbliang\ShippingManager\Shipping;
use Illuminate\Support\Facades\Cache;

class AddressValidator
{

    /**
     * @return boolean
     */
    public function validate(Address $address)
    {
        if (trim($address->street_address) === 'test') {
            return false;
        }

        $cacheKey = $address->toJson();

        if (Cache::tags(['address', 'validate'])->has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $addressEntity = \Hbliang\ShippingManager\Entities\Address::factory([
            'street' => $address->street_address,
            'city' => $address->city,
            'state' => $address->state,
            'postalCode' => $address->postcode,
            'country' => 'US',
        ]);

        Shipping::validateAddresses([$addressEntity]);

        Cache::tags(['address', 'validate'])->put($cacheKey, $addressEntity->isValid());

        return $addressEntity->isValid();
    }
}