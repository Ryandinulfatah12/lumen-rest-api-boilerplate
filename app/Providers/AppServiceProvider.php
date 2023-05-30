<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\Helper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // JSON Response
        $this->app->singleton(Helper::class, function ($app) {
            return new Helper;
        });
    }
}
