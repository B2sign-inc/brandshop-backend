<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\ShippingMethodCollection;
use App\Models\ShippingMethod;

class ShippingMethodController extends Controller
{
    public function all()
    {
        return new ShippingMethodCollection(ShippingMethod::all());
    }
}