<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evolucao extends Model
{
    use HasFactory;

    protected $table = 'evolucoes';

    protected $fillable = [
        'paciente_id',
        'sessao_id',
        'data',
        'texto',
        'tipo',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function sessao()
    {
        return $this->belongsTo(Sessao::class, 'sessao_id');
    }
}
