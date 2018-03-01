<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Attribute;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\User;
use Tests\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartControllerTest extends TestCase
{
    use Auth, RefreshDatabase;

    public function testAddToCartWithInvalidField()
    {
        $token = $this->login();

        // missing product id
        $response = $this->requestAsToken($token, 'POST', route('api.carts.store'), []);
        $response->assertJsonValidationErrors('product_id');
        $response->assertStatus(422);

        // product is not integer
        $response = $this->requestAsToken($token, 'POST', route('api.carts.store'), ['product_id' => 'hello']);
        $response->assertJsonValidationErrors('product_id');
        $response->assertStatus((422));

        // product does not exist
        $response = $this->requestAsToken($token, 'POST', route('api.carts.store'), ['product_id' => 1]);
        $response->assertJsonValidationErrors('product_id');
        $response->assertStatus((422));

        $product = factory(Product::class)->create();

        // missing quantity
        $response = $this->requestAsToken($token, 'POST', route('api.carts.store'), ['product_id' => $product->id]);
        $response->assertJsonValidationErrors('quantity');
        $response->assertStatus((422));

        // missing is not integer
        $response = $this->requestAsToken($token, 'POST', route('api.carts.store'), ['product_id' => $product->id, 'quantity' => 'test']);
        $response->assertJsonValidationErrors('quantity');
        $response->assertStatus((422));

        // quantity is less than 1
        $response = $this->requestAsToken($token, 'POST', route('api.carts.store'), ['product_id' => $product->id, 'quantity' => -1]);
        $response->assertJsonValidationErrors('quantity');
        $response->assertStatus((422));

        // have attributes but don't have value
        $response = $this->requestAsToken($token, 'POST', route('api.carts.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'attributes' => ['1' => null],
        ]);
        $response->assertJsonValidationErrors('attributes.1');
        $response->assertStatus(422);

        // attributes do not exist in database
        // illegal request
        $response = $this->requestAsToken($token, 'POST', route('api.carts.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'attributes' => ['1' => 'red'],
        ]);
        $response->assertJsonValidationErrors('attributes');
        $response->assertStatus(422);
    }


    public function testAddNonAttributesProductToCartSuccessFully()
    {
        $product = factory(Product::class)->create();

        $response = $this->requestAsLogined('POST', route('api.carts.store'), ['product_id' => $product->id, 'quantity'=> 3]);
        $response->assertSuccessful();

        $carts = Cart::all();
        $this->assertEquals(1, $carts->count());

        $cart = $carts->first();
        $this->assertEquals($product->id, $cart->product_id);
        $this->assertEquals(3, $cart->quantity);
    }

    public function addProductWithAttributesToCartSuccessfully()
    {
        $product = factory(Product::class)->create();
        $attribute = factory(Attribute::class)->create();
        $productAttribute = ProductAttribute::create([
            'product_id' => $product->id,
            'attribute_id' => $attribute->id,
        ]);
        $productAttribute->values()->save([
            'value' =>  'hello world',
        ]);


        $response = $this->requestAsLogined('POST', route('api.carts.store'), [
            'product_id' => $product->id,
            'quantity'=> 3,
            'attributes' => [
                $attribute->id => 'hello world'
            ]
        ]);
        $response->assertSuccessful();

        $carts = Cart::all();
        $this->assertEquals(1, $carts->count());

        $cart = $carts->first();
        $this->assertEquals($product->id, $cart->product_id);
        $this->assertEquals(3, $cart->quantity);
        $this->assertArraySubset(['attribute_id' => $attribute->id, 'value' => 'hello world'], $cart->attributes);
    }

    public function testUpdate()
    {
        $product = factory(Product::class)->create();
        $owner = factory(User::class)->create(['password' => $this->getHashedPassword()]);

        $accessToken = $this->login(['email' => $owner->email, 'password' => $this->password]);


        $cart = new Cart();
        $cart->quantity = 1;
        $cart->product_id = $product->id;
        $cart->user_id = 100;
        $cart->save();

        // update cart which is not belong to user
        $response = $this->requestAsToken($accessToken, 'PUT', route('api.carts.update', compact('cart')));
        $response->assertStatus(403);


        $cart->user_id = $owner->id;
        $cart->save();

        // update quantity less than 0
        $response = $this->requestAsToken($accessToken, 'PUT', route('api.carts.update', compact('cart')), ['quantity' => 0]);
        $response->assertStatus(403);
        $response->assertSeeText('Cant set quantity less than 0');

        $response = $this->requestAsToken($accessToken, 'PUT', route('api.carts.update', compact('cart')), ['quantity' => 10]);
        $response->assertSuccessful();
        $cart->refresh();
        $this->assertEquals(10, $cart->quantity);
    }

    public function testDestroy()
    {
        $owner = factory(User::class)->create(['password' => $this->getHashedPassword()]);

        $cart = new Cart();
        $cart->quantity = 1;
        $cart->product_id = 100;
        $cart->user_id = 100;
        $cart->save();

        $accessToken = $this->login(['email' => $owner->email, 'password' => $this->password]);

        // delete cart which is not belong to user
        $response = $this->requestAsToken($accessToken, 'DELETE', route('api.carts.destroy', compact('cart')));
        $response->assertStatus(403);


        $cart->user_id = $owner->id;
        $cart->save();

        $response = $this->requestAsToken($accessToken, 'DELETE', route('api.carts.destroy', compact('cart')));
        $response->assertSuccessful();
        $this->assertEmpty(Cart::find($cart->id));
    }

}
