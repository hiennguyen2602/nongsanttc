<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
        $this->app->singleton(CartService::class);
    }

    public function boot(): void
    {
        View::composer(['store.*', 'store.layouts.*', 'store.partials.*'], function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });
    }
}
