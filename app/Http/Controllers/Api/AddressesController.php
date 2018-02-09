<?php

namespace App\Http\Controllers\Api;

use App\Brandshop\Shipping\Validator\AddressValidator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;

use Auth;
use App\Models\Address;

class AddressesController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $page = intval($request->get('page'));
        $page = $page > 0 ? $page : 1;

        $limit = intval($request->get('limit'));
        $limit = $limit > 0 ? $limit : 15;

        $addresses = Auth::user()->addresses()->paginate($limit, ['*'], 'page', $page);
        return AddressResource::collection($addresses);
    }

    public function show(Address $address)
    {
        if ($address->user_id !== Auth::user()->id) {
            return $this->respondForbidden();
        }
        return new AddressResource($address);
    }

    public function store(AddressRequest $request, Address $address)
    {
        $address->fill($request->all());
        $address->user_id = Auth::user()->id;
        $address->save();

        return new AddressResource($address);
    }

    public function update(AddressRequest $request, Address $address)
    {
        if ($address->user_id !== Auth::user()->id) {
            return $this->respondForbidden();
        }

        $address->fill($request->all());
        $address->save();
        return new AddressResource($address);
    }

    public function destroy(Address $address)
    {
        $user = Auth::user();
        if ($address->user_id !== $user->id) {
            return $this->respondForbidden();
        }

        if ($user->default_billing_id === $address->id ||
            $user->default_shipping_id === $address->id
        ) {
            $user->default_billing_id = ($user->default_billing_id === $address->id) ? 0 : $user->default_billing_id;
            $user->default_shipping_id = ($user->default_shipping_id === $address->id) ? 0 : $user->default_shipping_id;
            $user->save();
        }

        $address->delete();
        return $this->respondSuccess('Deleted successfully.');
    }

    public function validateAddress(AddressValidator $addressValidator, Request $request)
    {
        $addressValidator->validate(new Address($request->all()));
    }
}
