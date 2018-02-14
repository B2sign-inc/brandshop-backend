<?php

use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\ShippingMethod::create([
            'code' => 'FEDEX_GROUND',
            'name' => 'Ground',
        ]);

        \App\Models\ShippingMethod::create([
            'code' => 'FEDEX_2DAY_AIR',
            'name' => '2 Day Air',
        ]);
    }
}
