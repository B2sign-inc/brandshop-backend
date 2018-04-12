<?php


namespace App\Brandshop\Providers;


use App\Brandshop\Config\BrandshopConfig;
use Illuminate\Support\ServiceProvider;

class BrandshopConfigServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(BrandshopConfig::class, function () {
            return new BrandshopConfig();
        });
    }

    public function boot()
    {
        // TODO fetch configuration of current shop from remote agent database.
        $this->app->make(BrandshopConfig::class)->set('braintree.environment', $this->app['config']['brandshop.braintree.environment']);
        $this->app->make(BrandshopConfig::class)->set('braintree.merchantId', $this->app['config']['brandshop.braintree.merchantId']);
        $this->app->make(BrandshopConfig::class)->set('braintree.publicKey', $this->app['config']['brandshop.braintree.publicKey']);
        $this->app->make(BrandshopConfig::class)->set('braintree.privateKey', $this->app['config']['brandshop.braintree.privateKey']);

        // TODO set cache prefix
        // $this->app['config']['cache.prefix'] = '';
    }

    public function provides()
    {
        return [BrandshopConfig::class];
    }
}