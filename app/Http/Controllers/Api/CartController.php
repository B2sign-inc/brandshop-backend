<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $data['user_id'] = Auth::user()->id;

        $product = Product::findOrFail($data['product_id']);

        $cart = new Cart();
        $cart->quantity = $data['quantity'];
        $cart->user_id = Auth::user()->id;

        $product->carts()->save($cart);

        return new CartResource($cart);
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

        return new CartResource($cart);
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== Auth::user()->id) {
            return $this->respondForbidden();
        }

        $cart->delete();

        return $this->respondSuccess('Delete successfully.');
    }
}
