<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Infobip\Infobip;
use Infobip\Configuration;

class InfobipServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Infobip::class, function ($app) {
            $config = new Configuration([
                'apiKey' => env('INFOBIP_API_KEY'),
                'baseUrl' => env('INFOBIP_BASE_URL'),
            ]);
            return new Infobip($config);
        });
    }

    public function boot()
    {
        //
    }
}
