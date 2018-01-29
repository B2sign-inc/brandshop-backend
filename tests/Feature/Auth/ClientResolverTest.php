<?php


namespace Tests\Feature\Auth;

use App\Brandshop\Auth\ClientResolver;
use App\Brandshop\Auth\Proxy;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientResolverTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatePasswordGrantClient()
    {
        $resolver = $this->app->make(ClientResolver::class);
        $client = $this->invokeMethod($resolver, 'createPasswordGrantClient');
        $this->assertTrue($client instanceof Client);
    }

    public function testGetPasswordGrandClientWithoutInitialClient()
    {

        $resolver = $this->getMockBuilder(ClientResolver::class)
            ->disableOriginalConstructor()
            ->setMethods(['createPasswordGrantClient'])
            ->getMock();

        $mockClient = new Client();
        $resolver->expects($this->once())
            ->method('createPasswordGrantClient')
            ->willReturn($mockClient);

        $client = $this->invokeMethod($resolver,'retrieveClientFromDb');

        $this->assertEquals($mockClient, $client);
    }

    public function testGetPasswordGrandClientWithInitialClient()
    {
        (new ClientRepository)->createPasswordGrantClient(
            null,
            config('app.name') . ' Password Grant Client',
            config('app.url')
        );

        $resolver = $this->getMockBuilder(ClientResolver::class)
            ->disableOriginalConstructor()
            ->setMethods(['createPasswordGrantClient'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('createPasswordGrantClient');

        $client = $this->invokeMethod($resolver,'retrieveClientFromDb');
        $this->assertTrue($client instanceof Client);
    }

    public function testRetrieveClientFromCache()
    {
        $mockClient = new Client();
        $cache = $this->app->make('cache');
        $cache->add(ClientResolver::CACHE_PASSWORD_CLIENT_NAME, $mockClient, ClientResolver::CACHE_PASSWORD_CLIENT_EXPIRED_TIME);

        $resolver = $this->getMockBuilder(ClientResolver::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$cache])
            ->setMethods(['retrieveClientFromDb'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('retrieveClientFromDb');

        $this->assertEquals($mockClient, $resolver->resolve());
    }

    public function testRetrieveClientFromDb()
    {
        $mockClient = new Client();
        $cache = $this->app->make('cache');
        $cache->pull(ClientResolver::CACHE_PASSWORD_CLIENT_NAME);

        $resolver = $this->getMockBuilder(ClientResolver::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$cache])
            ->setMethods(['retrieveClientFromDb'])
            ->getMock();

        $resolver->expects($this->once())
            ->method('retrieveClientFromDb')
            ->willReturn($mockClient);

        $this->assertEquals($mockClient, $resolver->resolve());
    }
}