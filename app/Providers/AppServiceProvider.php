<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Force HTTPS untuk semua URL
        if (
            $this->app->environment('production') ||
            request()->server('HTTP_X_FORWARDED_PROTO') == 'https'
        ) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        // Vite wajib definisikan host yang benar
        if (config('app.env') !== 'local') {
            \Illuminate\Support\Facades\Vite::useScriptTagAttributes([
                'crossorigin' => 'anonymous',
            ]);
        }
    }
}
