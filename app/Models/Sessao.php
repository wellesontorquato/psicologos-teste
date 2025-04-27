<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sessao extends Model
{
    use HasFactory;

    // 👇 isso aqui resolve o problema de nome da tabela
    protected $table = 'sessoes';

    protected $fillable = [
        'paciente_id',
        'data_hora',
        'duracao',
        'valor',
        'foi_pago',
        'observacoes',
    ];

    protected $casts = [
        'foi_pago' => 'boolean',
        'data_hora' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}