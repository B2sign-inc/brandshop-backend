<?php

use Illuminate\Database\Seeder;

use App\Models\Address;

class AddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $addresses = factory(Address::class)
                        ->times(20)
                        ->make();

        Address::insert($addresses->toArray());
    }
}
