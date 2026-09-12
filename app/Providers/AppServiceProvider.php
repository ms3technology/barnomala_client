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
        
        // Only force HTTPS URLs when the running request is actually HTTPS
        // AND the configured APP_URL is HTTPS. Forcing it whenever
        // APP_ENV=production breaks local development served over plain
        // HTTP from `php artisan serve` — every `url()` call (asset URLs,
        // form actions, redirects) returns `https://...` against an HTTP
        // server, causing ERR_CONNECTION_CLOSED on every page.
        $appUrl = (string) config('app.url');
        if ($appUrl !== '' && str_starts_with($appUrl, 'https://') && request()->isSecure()) {
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
