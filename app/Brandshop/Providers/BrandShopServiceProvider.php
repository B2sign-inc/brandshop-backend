<?php


namespace App\Brandshop\Providers;


use Illuminate\Support\ServiceProvider;

class BrandShopServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        $this->mergeConfigFrom(config_path('brandshop.php'), 'brandshop');
    }
}