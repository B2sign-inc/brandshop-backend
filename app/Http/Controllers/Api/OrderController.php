<?php

namespace App\Http\Controllers\Api;

use App\Brandshop\Shipping\Exceptions\InvalidAddressException;
use App\Events\OrderPlaced;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function place(PlaceOrderRequest $request)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $shippingAddress = Address::create($request->get('shipping_address'));
            $billingAddress = Address::create($request->get('use_different_billing_address')
                ? $request->get('billing_address') : $request->get('shipping_address'));

            $data['shipping_address_id'] = $shippingAddress->id;
            $data['billing_address_id'] = $billingAddress->id;
            $data['user_id'] = $user->id;
            $data['shipping_option'] = $request->get('shipping_method_id');

            $order = new Order($data);
            $order->stateMachine()->initialize('create');

            $this->syncOrderProduct($order);

            $order->save();

            // fire event
            event(OrderPlaced::class, $order);

            DB::commit();

            // all exceptions handled by App\Exceptions\Handler
        } catch (InvalidAddressException $e) {
            DB::rollBack();
            throw ValidationException::withMessages([$e->getMessage()]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw new $e;
        }
    }

    private function syncOrderProduct(Order $order)
    {
        $user = Auth::user();
        $cartItems = $user->carts;

        foreach ($cartItems as $item) {
            /** @var $item Cart */
            
        }
    }
}
