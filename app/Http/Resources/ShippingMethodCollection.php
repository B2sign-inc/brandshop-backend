<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShippingMethodCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function ($shippingMethod) {
                return [
                    'id' => $shippingMethod->id,
                    'code' => $shippingMethod->code,
                    'name' => $shippingMethod->name,
                    'amount' => $shippingMethod->calculate(),
                ];
            }),
        ];
    }
}
