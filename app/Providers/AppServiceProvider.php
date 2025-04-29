<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use App\Models\Evolucao;
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
    public function boot()
    {
        // NÃO precisa alterar a build directory manualmente
        // o config/vite.php já está correto apontando para build/
        
        Route::model('evolucao', Evolucao::class);
        Paginator::useBootstrap();
    }
}
