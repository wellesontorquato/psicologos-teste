<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Request;
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
        if (app()->environment('production')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));

            // 🔐 Força host e scheme globalmente para validação correta de assinatura
            Request::server()->set('HTTPS', 'on');
            Request::server()->set('HTTP_HOST', parse_url(config('app.url'), PHP_URL_HOST));
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
