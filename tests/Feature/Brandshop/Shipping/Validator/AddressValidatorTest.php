<?php


namespace Tests\Feature\Brandshop\Shipping\Validator;


use App\Brandshop\Shipping\Validator\AddressValidator;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressValidatorTest extends TestCase
{
    use RefreshDatabase;

    public function testAddress()
    {
        $validator = new AddressValidator();

        $address  = new Address([
            'first_name' => 'first',
            'last_name' => 'last',
            'street_address' => '1600 Amphitheatre Parkway',
            'extra_address' => '',
            'postcode' => '94043 ',
            'city' => 'Mountain View',
            'state' => 'CA',
            'telephone' => '6268888888',
        ]);

        $this->assertTrue($validator->validate($address));


        $address->street_address = '123';
        $this->assertFalse($validator->validate($address));
    }
}