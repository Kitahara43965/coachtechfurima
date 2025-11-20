<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(RegisterViewResponse::class, function ($app) {
            return new class implements RegisterViewResponse {
                public function toResponse($request)
                {
                    return view('auth.register');
                }
            };
        });
    }
    
    public function boot()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
}
