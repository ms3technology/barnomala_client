<?php

namespace App\Providers;

use App\Services\ThemeService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        Paginator::useTailwind();
        
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $options = $view->getData()['options'] ?? [];
            $instituteName = $options['institute.branding.name'] ?? config('app.name', 'Laravel');
            $view->with('instituteName', $instituteName);
        });

        \Illuminate\Support\Facades\View::share('theme', app(ThemeService::class));
    }
}
