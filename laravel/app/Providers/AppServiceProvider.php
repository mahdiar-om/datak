<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Set the environment variable before the client is initialized
        putenv('ELASTIC_CLIENT_APIVERSIONING=true');
    }

    public function boot()
    {
        //
    }
}
