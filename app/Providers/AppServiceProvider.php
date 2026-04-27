<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // FORZAR ZONA HORARIA MÉXICO PARA TODO EL SISTEMA
        date_default_timezone_set('America/Mexico_City');
        
        // Configurar Carbon para que coincida
        Carbon::setLocale('es');
        config(['app.timezone' => 'America/Mexico_City']);
    }
}