<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sessao extends Model
{
    use HasFactory;

    protected $table = 'sessoes';

    protected $fillable = [
        'paciente_id',
        'data_hora',
        'data_hora_original',
        'duracao',
        'valor',
        'foi_pago',
        'observacoes',
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
}
