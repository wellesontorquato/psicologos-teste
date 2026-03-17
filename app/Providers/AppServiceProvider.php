<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade; // ✅ Importado
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\Evolucao;
use App\Notifications\CustomVerifyEmail;
use App\Helpers\AssetHelper; // ✅ Importado

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Força HTTPS em produção
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // Usa Bootstrap na paginação
        Paginator::useBootstrap();

        // Binding automático de modelos
        Route::model('evolucao', Evolucao::class);

        // Corrige namespace mail:: para os componentes de e-mail personalizados
        View::addNamespace('mail', resource_path('views/vendor/mail'));

        // Substitui a notificação padrão de verificação de e-mail
        VerifyEmail::toMailUsing(function ($notifiable) {
            return (new CustomVerifyEmail)->toMail($notifiable);
        });

        // ✅ Registra a diretiva @versao('caminho/do/arquivo.ext')
        Blade::directive('versao', function ($expression) {
            return "<?php echo \\App\\Helpers\\AssetHelper::versao($expression); ?>";
        });
    }
}
