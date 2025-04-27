<?php

namespace App\Services;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Registra uma ação no sistema.
     *
     * @param  string       $action        Nome da ação (ex: updated_profile, deleted_patient)
     * @param  string|null  $modelType     Classe do modelo envolvido (ex: App\Models\Paciente)
     * @param  int|null     $modelId       ID do modelo envolvido
     * @param  string|null  $description   Descrição personalizada da ação
     * @return void
     */
    public static function log(string $action, ?string $modelType = null, ?int $modelId = null, ?string $description = null): void
    {
        Audit::create([
            'user_id'     => Auth::check() ? Auth::id() : null,
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'description' => $description,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::header('User-Agent'),
        ]);
    }

    /**
     * Log simplificado com modelo Eloquent.
     *
     * @param  string        $action
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string|null   $description
     * @return void
     */
    public static function logModel(string $action, $model, ?string $description = null): void
    {
        self::log($action, get_class($model), $model->id, $description);
    }
}
