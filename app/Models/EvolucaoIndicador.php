<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvolucaoIndicador extends Model
{
    use HasFactory;

    protected $table = 'evolucao_indicadores';

    protected $fillable = [
        'evolucao_id',
        'paciente_id',
        'sessao_id',
        'estado_emocional',
        'intensidade',
        'alerta',
        'observacoes',
    ];

    public function evolucao()
    {
        return $this->belongsTo(Evolucao::class, 'evolucao_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function sessao()
    {
        return $this->belongsTo(Sessao::class, 'sessao_id');
    }
}