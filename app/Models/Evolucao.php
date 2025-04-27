<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evolucao extends Model
{
    use HasFactory;

    protected $table = 'evolucoes'; // <- isso é ESSENCIAL!

    protected $fillable = [
        'paciente_id',
        'data',
        'texto',
        'tipo',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}
