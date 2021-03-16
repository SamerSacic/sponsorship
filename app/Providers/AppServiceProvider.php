<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\SponsorableSponsorshipsController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SponsorableSponsorshipsController::class, function ($app) {
            return new SponsorableSponsorshipsController($app->make(PaymentGateway::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
