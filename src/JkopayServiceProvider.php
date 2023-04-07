<?php
namespace LouisLun\LaravelJkopay;

use Illuminate\Support\ServiceProvider;

class JkopayServiceProvider extends ServiceProvider
{
    /**
     * Register services
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Jkopay::class, function ($app) {
            return new Jkopay($app['config']['linepay']);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('jkopay.php'),
        ], 'jkopay');
    }
}
