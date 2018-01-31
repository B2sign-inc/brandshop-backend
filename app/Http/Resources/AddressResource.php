<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AddressResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'street_address' => $this->street_address,
            'extra_address' => $this->extra_address,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'state' => $this->state,
            'telephone' => $this->telephone,
        ];
    }
}