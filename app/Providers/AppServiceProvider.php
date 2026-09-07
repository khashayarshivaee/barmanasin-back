<?php

namespace App\Providers;

use App\Services\Mail\Providers\MailProviderInterface;
use App\Services\Mail\Providers\StalwartProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            MailProviderInterface::class,
            StalwartProvider::class
        );
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
