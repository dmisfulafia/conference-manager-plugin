<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Mail;
use App\Mail\Transport\GoogleAppScriptTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('google_app_script', function (array $config) {
            return new GoogleAppScriptTransport(
                $config['url'],
                $config['secret_key']
            );
        });
    }
}
