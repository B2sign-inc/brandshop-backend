<?php


namespace Tests\Feature\Controllers\Api;


use App\Events\OrderPlaced;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\Auth;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use Auth, RefreshDatabase;

    public function testPlaceOrderValidation()
    {
        $accessToken = $this->login();

        $response = $this->requestAsToken($accessToken, 'post', route('api.orders.place'), []);
        $response->assertStatus(422);


        $address = [
            'first_name' => 'ben',
            'last_name' => 'waht',
            'phone_number' => 62612345678,
            'street_address' => 'what',
            'city' => 'In',
            'state' => 'CA',
            'postcode' => '91780',
        ];

        $response = $this->requestAsToken($accessToken, 'post', route('api.orders.place'), [
            'shipping' => $address,
            'use_different_billing_address' => 1,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['billing.first_name', 'billing.last_name', 'billing.phone_number', 'billing.street_address', 'billing.city', 'billing.state', 'billing.postcode']);

        $response = $this->requestAsToken($accessToken, 'post', route('api.orders.place'), [
            'shipping' => $address,
            'shipping_method_id' => 100,
        ]);
        $response->assertJsonValidationErrors(['shipping_method_id']);
    }

    public function testPlaceOrderWithEmptyCart()
    {
        $address = [
            'first_name' => 'ben',
            'last_name' => 'waht',
            'phone_number' => 62612345678,
            'street_address' => 'hello',
            'city' => 'In',
            'state' => 'CA',
            'postcode' => '91780',
        ];

        $response = $this->requestAsLogined('post', route('api.orders.place'), [
            'shipping' => $address,
            'shipping_method_id' => factory(ShippingMethod::class)->create()->id,
        ]);
        $response->assertSeeText('Your cart is empty');
        $response->assertStatus(500);
    }

    public function testPlaceOrderWithErrorAddress()
    {
        $address = [
            'first_name' => 'ben',
            'last_name' => 'waht',
            'phone_number' => 62612345678,
            'street_address' => 'test',
            'city' => 'In',
            'state' => 'CA',
            'postcode' => '91780',
        ];

        $response = $this->requestAsLogined('post', route('api.orders.place'), [
            'shipping' => $address,
            'shipping_method_id' => factory(ShippingMethod::class)->create()->id,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('shipping');
    }

    public function testPlaceOrderUseSameAddress()
    {
        Event::fake();

        $user = factory(User::class)->create();
        factory(Cart::class)->create(['user_id' => $user->id]);

        $accessToken = $this->login(['email' => $user->email, 'password' => 'secret']);

        $address = [
            'first_name' => 'ben',
            'last_name' => 'waht',
            'phone_number' => 62612345678,
            'street_address' => 'hello',
            'city' => 'In',
            'state' => 'CA',
            'postcode' => '91780',
        ];

        $response = $this->requestAsToken($accessToken, 'post', route('api.orders.place'), [
            'shipping' => $address,
            'shipping_method_id' => factory(ShippingMethod::class)->create()->id,
        ]);

        $response->assertSuccessful();
        Event::assertDispatched(OrderPlaced::class, function ($event) use ($user) {
            /** @var OrderPlaced $event */
            return $event->getOrder()->id === Order::first()->id;
        });
        $this->assertEquals(1, Order::count());
    }

    public function testPlaceOrderUseDifferentAddress()
    {
        Event::fake();

        $user = factory(User::class)->create();
        factory(Cart::class)->create(['user_id' => $user->id]);

        $accessToken = $this->login(['email' => $user->email, 'password' => 'secret']);

        $address = [
            'first_name' => 'ben',
            'last_name' => 'waht',
            'phone_number' => 62612345678,
            'street_address' => 'hello',
            'city' => 'In',
            'state' => 'CA',
            'postcode' => '91780',
        ];

        $this->assertEquals(0, Address::count());
        $response = $this->requestAsToken($accessToken, 'post', route('api.orders.place'), [
            'shipping' => $address,
            'billing' => $address,
            'shipping_method_id' => factory(ShippingMethod::class)->create()->id,
            'use_different_billing_address' => 1,
        ]);
        $response->assertSuccessful();

        $this->assertEquals(2, Address::count());

        Event::assertDispatched(OrderPlaced::class, function ($event) use ($user) {
            /** @var OrderPlaced $event */
            return $event->getOrder()->id === Order::first()->id;
        });
        $this->assertEquals(1, Order::count());
        $this->assertNotEquals(Order::first()->shipping_address_id, Order::first()->billing_address_id);
    }
}