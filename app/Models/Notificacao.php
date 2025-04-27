<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notificacao extends Model
{
    use HasFactory;

    protected $table = 'notificacoes';

    protected $fillable = [
        'user_id',
        'titulo',
        'mensagem',
        'tipo',
        'lida',
        'visto_em',
        'relacionado_id',
        'relacionado_type',
    ];

    protected $casts = [
        'lida' => 'boolean',
        'visto_em' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relacionado()
    {
        return $this->morphTo();
    }

    /**
     * Retorna a rota para redirecionamento da notificação
     */
    public function redirecionamento()
    {
        $tipo = class_basename($this->relacionado_type);

        return match ($tipo) {
            'Sessao'   => route('sessoes.edit', $this->relacionado_id),
            'Paciente' => route('pacientes.edit', $this->relacionado_id),
            default    => route('dashboard'),
        };
    }
}
