<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\Evolucao;
use App\Mail\CustomVerifyEmail;

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
        // Força HTTPS e domínio correto em produção
        if (app()->environment('production')) {
            URL::forceScheme('https');

            // Importante: Garante que os links de verificação sejam gerados com o domínio correto
            URL::forceRootUrl(config('app.url'));
        }

        // Usa Bootstrap na paginação
        Paginator::useBootstrap();

        // Binding automático de modelos
        Route::model('evolucao', Evolucao::class);

        // Corrige namespace mail:: para os componentes de e-mail personalizados
        View::addNamespace('mail', resource_path('views/vendor/mail'));

        // Substitui a notificação padrão de verificação de e-mail
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new CustomVerifyEmail($url))->to($notifiable->email);
        });
    }
}
