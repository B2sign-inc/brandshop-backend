<?php

namespace Tests\Feature\Brandshop\Auth;

use App\Brandshop\Auth\ClientResolver;
use App\Brandshop\Auth\Proxy;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Token;
use Laravel\Passport\TokenRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProxyTest extends TestCase
{
    use RefreshDatabase;

    protected function getMockClientResolver($callResolveExpect = null)
    {
        if (!$callResolveExpect) {
            $callResolveExpect = $this->once();
        }

        $clientResolver = $this->getMockBuilder(ClientResolver::class)
            ->disableOriginalConstructor()
            ->setMethods(['resolve'])
            ->getMock();


        $client = (new ClientRepository)->createPasswordGrantClient(
            null,
            config('app.name') . ' Password Grant Client',
            config('app.url')
        );

        $clientResolver->expects($callResolveExpect)
            ->method('resolve')
            ->willReturn($client);

        return $clientResolver;
    }

    public function testProxyWithWrongGrantType()
    {
        $proxy = new Proxy($this->app, $this->getMockClientResolver());
        $response = $this->invokeMethod($proxy, 'proxy', ['test']);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('unsupported_grant_type', json_decode($response->getContent(), true));
    }

    public function testProxyWithEmptyData()
    {
        $proxy = new Proxy($this->app, $this->getMockClientResolver());
        $response = $this->invokeMethod($proxy, 'proxy', ['password']);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('invalid_request', json_decode($response->getContent(), true));

    }

    public function testProxyWithWrongCredentials()
    {
        $proxy = new Proxy($this->app, $this->getMockClientResolver());
        $response = $this->invokeMethod($proxy, 'proxy', ['password', ['username' => '', 'password' =>'']]);

        $this->assertEquals(400, $response->status());
        $this->assertContains('invalid_request', json_decode($response->getContent(), true));
    }

    public function testAttemptWithMissingEmailOrPassword()
    {
        $proxy = new Proxy($this->app, $this->getMockClientResolver($this->never()));
        $this->expectException(\InvalidArgumentException::class);
        $proxy->attempt([]);
    }


    public function testPassAttemptWithWrongPassword()
    {
        $firstname = 'test';
        $lastname = 'test';
        $email = 'test@gmail.com';
        $password = 'test';
        $user = User::create(compact('email', 'firstname', 'lastname') + ['password' => Hash::make($password)]);

        $proxy = new Proxy($this->app, $this->getMockClientResolver($this->once()));
        // reset password
        $password = 1;
        $response = $proxy->attempt(compact('email', 'password'));

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertContains('invalid_credentials', json_decode($response->getContent(), true));
    }

    public function testPassAttempt()
    {
        $firstname = 'test';
        $lastname = 'test';
        $email = 'test@gmail.com';
        $password = 'test';
        $user = User::create(compact('email', 'firstname', 'lastname') + ['password' => Hash::make($password)]);

        $proxy = new Proxy($this->app, $this->getMockClientResolver($this->once()));
        $response = $proxy->attempt(compact('email', 'password'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('access_token', json_decode($response->getContent(), true));
    }

    public function testLogout()
    {
        $user = User::create([
            'email' => 'test@gmail.com',
            'firstname' => 'hello',
            'lastname' => 'hello',
            'password' => Hash::make('test'),
        ]);

        $proxy = new Proxy($this->app, $this->app->make(ClientResolver::class));
        $response = $proxy->attempt(['email' => 'test@gmail.com', 'password' => 'test']);

        $tokenRepository = new TokenRepository;
        $tokens = $tokenRepository->forUser($user->id);

        // make sure only one token created
        $this->assertEquals(1, $tokens->count());

        $token = $tokens->first();
        $proxy->logout($token);

        $this->assertTrue($tokenRepository->isAccessTokenRevoked($token->id));

        $refreshTokens = DB::table('oauth_refresh_tokens')->where('access_token_id', $token->id)->get();
        $this->assertEquals(0, $refreshTokens->filter(function($refreshToken) {
            return $refreshToken->revoked === false;
        })->count());
    }


    public function testRefreshTokenWithInExistToken()
    {
        $refreshToken = '123';
        $proxy = new Proxy($this->app, $this->app->make(ClientResolver::class));
        $response = $proxy->refreshToken($refreshToken);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertContains('invalid_request', json_decode($response->getContent(), true));
    }

    public function testRefreshTokenOk()
    {
        $user = User::create([
            'email' => 'test@gmail.com',
            'firstname' => 'hello',
            'lastname' => 'hello',
            'password' => Hash::make('test'),
        ]);

        $proxy = new Proxy($this->app, $this->app->make(ClientResolver::class));
        $response = $proxy->attempt(['email' => 'test@gmail.com', 'password' => 'test']);

        $result = json_decode($response->getContent());
        $refreshToken = $result->refresh_token;

        $response = $proxy->refreshToken($refreshToken);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
