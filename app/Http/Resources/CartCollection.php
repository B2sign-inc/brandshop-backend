<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
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
            'data' => [
                'total' => $this->collection->sum(function($cart) {
                    return $cart->product->price;
                }),
                'items' => $this->collection->transform(function ($cart) {
                    return [
                        'id' => $cart->id,
                        'product' => $cart->product,
                        'quantity' => $cart->quantity,
                    ];
                }),
            ]
        ];
    }
}
