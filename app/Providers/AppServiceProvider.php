<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        if (!class_exists('AdminHelper')) {
            class_alias(\App\Helpers\AdminHelper::class, 'AdminHelper');
        }
        if (!class_exists('CustomHelper')) {
            class_alias(\App\Helpers\CustomHelper::class, 'CustomHelper');
        }
    }
}
