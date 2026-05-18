<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Força HTTPS em produção
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Proteções de Eloquent em produção
        Model::shouldBeStrict(! app()->isProduction());

        // Log de queries lentas (> 1s) em dev
        if (app()->isLocal()) {
            DB::whenQueryingForLongerThan(1000, function () {
                logger()->warning('Query lenta detectada');
            });
        }
    }
}
