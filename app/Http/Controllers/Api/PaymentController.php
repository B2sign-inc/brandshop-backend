<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function paid(Request $request, Order $order)
    {
        $user = Auth::user();

        $order->syncToOrderProduct($user);
        $order->transition('pay');
        $order->save();

        // TODO create payment

    }
}
