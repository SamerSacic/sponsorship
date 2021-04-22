<?php

namespace App\Providers;

use Tests\FakePaymentGateway;
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
        // TODO: Bind real Stripe implementation when it's ready.
        $this->app->bind(PaymentGateway::class, function() {
            return new FakePaymentGateway;
        });
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
