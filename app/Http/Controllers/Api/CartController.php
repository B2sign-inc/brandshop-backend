<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartCollection;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use ApiResponse;

    protected function carts()
    {
        $carts = Auth::user()->carts()->with('product')->get();

        return new CartCollection($carts);
    }

    public function index()
    {
        return $this->carts();
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $data['user_id'] = Auth::user()->id;

        $cart = Cart::create($data);

        return $this->carts();
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== Auth::user()->id) {
            return $this->respondForbidden();
        }

        $quantity = intval($request->get('quantity'));
        if ($quantity <= 0) {
            return $this->respondForbidden('Cant set quantity less than 0.');
        } elseif ($cart->quantity !== $quantity) {
            $cart->quantity = $quantity;
            $cart->save();
        }

        return $this->carts();
    }

    public function empty()
    {
        Auth::user()->carts()->delete();

        return $this->respondSuccess('Empty cart successfully.');
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== Auth::user()->id) {
            return $this->respondForbidden();
        }

        $cart->delete();

        return $this->carts();
    }
}
