<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

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

    // Descriptografar automaticamente ao acessar $evolucao->texto
    public function getTextoAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value; // Em caso de erro, retorna valor bruto
        }
    }

    // Criptografar automaticamente ao salvar $evolucao->texto = '...'
    public function setTextoAttribute($value)
    {
        $this->attributes['texto'] = Crypt::encryptString($value);
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
