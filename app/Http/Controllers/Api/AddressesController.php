<?php

namespace App\Http\Controllers\Api;

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
        $page = $request->get('page');
        $page = (intval($page) > 0) ? intval($page) : 1;

        $addresses = Auth::user()->addresses()->paginate(15, ['*'], 'page', $page);
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
        if ($address->user_id !== Auth::user()->id) {
            return $this->respondForbidden();
        }

        $address->delete();
        return $this->respondSuccess('Deleted successfully.');
    }
}
