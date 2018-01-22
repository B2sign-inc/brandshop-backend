<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\RouteRegistrar;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes(function (RouteRegistrar $router) {
            $router->forAccessTokens();
        }, ['prefix' => 'api', 'middleware' => ['cors']]);

        Passport::tokensExpireIn(now()->addSeconds(config('ACCESS_TOKEN_EXPIRED', 3600)));
        Passport::refreshTokensExpireIn(now()->addDays(config('REFRESH_TOKEN_EXPIRED', 30)));
    }
}
