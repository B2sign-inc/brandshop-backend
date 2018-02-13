<?php


namespace Tests\Feature\Controllers\Api;


use App\Brandshop\Config\BrandshopConfig;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Auth;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase, Auth;

    public function setUp()
    {
        parent::setUp();

        $brandshopConfig = $this->app->make(BrandshopConfig::class);
        $brandshopConfig->set('braintree.merchantId', env('BRAINTREE_MERCHANT_ID'));
        $brandshopConfig->set('braintree.publicKey', env('BRAINTREE_PUBLIC_KEY'));
        $brandshopConfig->set('braintree.privateKey', env('BRAINTREE_PRIVATE_KEY'));
    }

    public function testToken()
    {
        $response = $this->requestAsLogined('get', route('api.payments.token'));
        $response->assertSuccessful();
        $response->assertJsonStructure(['token']);
    }

    private function createOrder(User $user = null, $amount = null)
    {
        $user = $user ?? factory(User::class)->create();
        $cart = factory(Cart::class)->create(['user_id' => $user->id]);
        $shippingMethod = factory(ShippingMethod::class)->create();

        $address = factory(Address::class)->create();
        $order = new Order();
        $order->user_id = $user->id;
        $order->shipping_address_id = $address->id;
        $order->billing_address_id = $address->id;
        $order->shipping_method_id = $shippingMethod->id;
        $order->amount = $amount ?? $user->calculateCart() + $shippingMethod->calculate();
        $order->save();
        $order->syncToOrderProduct($user->carts);
        return $order;
    }

    public function testPaidMissingNonce()
    {
        $response = $this->requestAsLogined('post', route('api.payments.paid', ['order' => $this->createOrder()]), []);
        $response->assertStatus(500);
        $response->assertSeeText('Missing nonce');
    }

    public function testPaidUsingOtherOrder()
    {
        $response = $this->requestAsLogined('post', route('api.payments.paid', ['order' => $this->createOrder()]), ['payment_method_nonce' => 'hello']);
        $response->assertStatus(403);
    }

    public function testPaidWithValidNonce()
    {
        $user = factory(User::class)->create();
        $order = $this->createOrder($user);
        $accessToken = $this->login(['email' => $user->email, 'password' => 'secret']);
        $response = $this->requestAsToken(
            $accessToken,
            'post',
            route('api.payments.paid', ['order' => $order]),
            ['payment_method_nonce' => 'fake-valid-nonce']
        );

        $response->assertSuccessful();

        $order->refresh();
        $this->assertEquals('paid', $order->getState());
    }

    /**
     * TODO complete all invalid payment test
     * @see https://developers.braintreepayments.com/reference/general/testing/php
     */
    public function testPaidWithInvalidNonce()
    {

        $user = factory(User::class)->create();
        // Different test amount will trigger the associated authorization response,
        // regardless of the processing currency.
        $validOrder = $this->createOrder($user, 100);
        $accessToken = $this->login(['email' => $user->email, 'password' => 'secret']);


        $response = $this->requestAsToken(
            $accessToken,
            'post',
            route('api.payments.paid', ['order' => $validOrder]),
            ['payment_method_nonce' => 'fake-consumed-nonce']
        );
        $response->assertStatus(500);
        $response->assertSeeText('Cannot use a payment_method_nonce more than once');


        $response = $this->requestAsToken(
            $accessToken,
            'post',
            route('api.payments.paid', ['order' => $validOrder]),
            ['payment_method_nonce' => 'fake-luhn-invalid-nonce']
        );
        $response->assertStatus(500);
        $response->assertSeeText('Credit card number is invalid');


        // Test Amount

        // Processor Declined with a processor response equal to the amount
        $response = $this->requestAsToken(
            $accessToken,
            'post',
            route('api.payments.paid', ['order' => $this->createOrder($user, 2000)]),
            ['payment_method_nonce' => 'fake-valid-nonce']
        );
        $response->assertStatus(500);
        $response->assertSeeText('Do Not Honor');

        $response = $this->requestAsToken(
            $accessToken,
            'post',
            route('api.payments.paid', ['order' => $this->createOrder($user, 5001 )]),
            ['payment_method_nonce' => 'fake-valid-nonce']
        );
        $response->assertStatus(500);
        $response->assertSeeText('Gateway Rejected: application_incomplete');
    }
}