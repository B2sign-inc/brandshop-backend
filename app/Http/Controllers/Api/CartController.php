<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
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

    public function store(StoreCartRequest $request)
    {
        $attributes = [];
        foreach ($request->get('attributes', []) as $attributeId => $value) {
            $attributes[] = [
                'attribute_id' => $attributeId,
                'value' => $value,
            ];
        }

        Cart::create([
            'product_id' => $request->get('product_id'),
            'quantity' => $request->get('quantity'),
            'user_id' => Auth::user()->id,
            'attributes' => $attributes,
        ]);

        return $this->carts();
    }

    /**
     * TODO update attributes
     * @param Request $request
     * @param Cart $cart
     * @return CartCollection|\Illuminate\Http\JsonResponse
     */
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

        return $this->carts();
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
