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
    }

    public function provides()
    {
        return [BrandshopConfig::class];
    }
}