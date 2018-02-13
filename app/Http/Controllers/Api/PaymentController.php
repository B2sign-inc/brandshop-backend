<?php

namespace App\Http\Controllers\Api;

use App\Brandshop\Config\BrandshopConfig;
use App\Models\Order;
use App\Models\Payment;
use Braintree\ClientToken;
use Braintree\Configuration;
use Braintree\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

// TODO payment log
class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(BrandshopConfig $brandshopConfig)
    {
        Configuration::environment($brandshopConfig->get('braintree.environment'));
        Configuration::merchantId($brandshopConfig->get('braintree.merchantId'));
        Configuration::publicKey($brandshopConfig->get('braintree.publicKey'));
        Configuration::privateKey($brandshopConfig->get('braintree.privateKey'));
    }

    public function generateBrainTreeToken()
    {
        return response()->json(['token' => ClientToken::generate()]);
    }

    public function paid(Request $request, Order $order)
    {
        if (!$nonceFromTheClient = $request->get('payment_method_nonce')) {
            throw new \Exception('Missing nonce');
        }

        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            return $this->respondForbidden();
        }

        $response = Transaction::sale([
            'amount' => $order->amount,
            'paymentMethodNonce' => $nonceFromTheClient,
            'options' => [
                'submitForSettlement' => True
            ]
        ]);

        if (!$response->success) {
            throw new \Exception('Braintree was unable to perform a charge: ' . $response->message);
        }
        $payment = new Payment();
        $payment->amount = $order->amount;
        $payment->transaction = $response->transaction;
        $payment->save();

        $order->transition('pay');
        $order->payment_id = $payment->id;
        $order->save();

        return $this->respondSuccess();
    }
}
