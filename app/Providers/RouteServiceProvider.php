<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        // ✅ FORÇA o Laravel a NÃO singularizar 'evolucoes' errado
        Route::resourceVerbs([
            'create' => 'create',
            'edit' => 'edit',
        ]);

        // ✅ Força a substituição de binding padrão para evolucao
        Route::bind('evolucoes', function ($value) {
            return \App\Models\Evolucao::where('id', $value)->firstOrFail();
        });

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
