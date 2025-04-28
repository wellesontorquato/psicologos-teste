<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite; // <-- adiciona isso
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
        // Faz o Vite usar o caminho correto para o manifest
        Vite::useBuildDirectory('build/.vite'); // <-- adiciona isso

        Route::model('evolucao', Evolucao::class);
        Paginator::useBootstrap();
    }
}
