<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderPlaced;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{

    use ApiResponse;

    public function place(PlaceOrderRequest $request)
    {
        $user = Auth::user();

        if ($user->carts->isEmpty()) {
            throw new \Exception('Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            $shippingAddress = new Address($request->get('shipping'));
            if (!$shippingAddress->validate(true)) {
                throw ValidationException::withMessages(['shipping' => 'Your shipping address is invalid']);
            }

            $billingAddress = new Address($request->get('use_different_billing_address')
                ? $request->get('billing') : $request->get('shipping'));
            if (!$billingAddress->validate(true)) {
                throw ValidationException::withMessages(['billing' => 'Your billing address is invalid']);
            }

            $shippingAddress->save();
            $billingAddress->save();

            $data['shipping_address_id'] = $shippingAddress->id;
            $data['billing_address_id'] = $billingAddress->id;
            $data['user_id'] = $user->id;
            $data['shipping_method_id'] = $request->get('shipping_method_id');

            $order = new Order($data);
            $order->stateMachine()->initialize('created');

            $order->save();

            // fire event
            event(new OrderPlaced($order));

            DB::commit();

            return $order;

            // all exceptions handled by App\Exceptions\Handler
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof ValidationException) {
                throw new ValidationException($e->validator, $e->response, $e->errorBag);
            }
            throw new $e;
        }
    }
}
