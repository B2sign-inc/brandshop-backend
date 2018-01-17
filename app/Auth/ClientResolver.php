<?php


namespace App\Auth;


use Illuminate\Cache\CacheManager as Cache;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;


class ClientResolver
{
    const CACHE_PASSWORD_CLIENT_EXPIRED_TIME = 3600 * 12; // seconds
    const CACHE_PASSWORD_CLIENT_NAME = 'oauth.password.client';

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function resolve()
    {
        // client id is not found in cache
        if (!$client = $this->cache->get(self::CACHE_PASSWORD_CLIENT_NAME)) {
            $client = $this->retrieveClientFromDb();
            $this->cache->add(
                self::CACHE_PASSWORD_CLIENT_NAME,
                $client,
                now()->addSeconds(self::CACHE_PASSWORD_CLIENT_EXPIRED_TIME)
            );
        }
        
        return $client;
    }

    protected function retrieveClientFromDb()
    {
        // first or create
        if (!$client = Client::where([['password_client', true], ['revoked', false]])->first()) {
            $client = $this->createPasswordGrantClient();
        }
        return $client;
    }

    protected function createPasswordGrantClient()
    {
        return (new ClientRepository)->createPasswordGrantClient(
            null,
            config('app.name') . ' Password Grant Client',
            config('app.url')
        );
    }
}