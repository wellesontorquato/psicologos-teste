<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sessao extends Model
{
    use HasFactory;

    protected $table = 'sessoes';

    protected $fillable = [
        'user_id',
        'paciente_id',
        'data_hora',
        'data_hora_original',
        'duracao',
        'valor',
        'foi_pago',
        'observacoes',
        'status_confirmacao',
        'lembrete_enviado', 
    ];

    protected $casts = [
        'foi_pago' => 'boolean',
        'data_hora' => 'datetime',
        'data_hora_original' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function evolucoes()
    {
        return $this->hasMany(Evolucao::class, 'sessao_id');
    }
}
