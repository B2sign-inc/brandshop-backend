<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponse;

    public function update(Request $request)
    {
        $data = $this->validate($request, [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->save();
        return $this->respondSuccess('Updated Successfully.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        $data = $this->validate($request, [
            'type' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        if ($address->user_id !== $user->id) {
            return $this->respondForbidden();
        }

        if ($data['type'] === 'billing') {
            $user->default_billing_id = $address->id;
            $user->save();
        } elseif ($data['type'] === 'shipping') {
            $user->default_shipping_id = $address->id;
            $user->save();
        }
        return $this->respondSuccess('Updated Successfully.');
    }
}
