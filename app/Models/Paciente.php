<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nome',
        'data_nascimento',
        'sexo',
        'telefone',
        'email',
        'cpf',
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'exige_nota_fiscal',
        'observacoes',
        'nome_contato_emergencia',
        'telefone_contato_emergencia',
        'parentesco_contato_emergencia',
    ];
    

    public function sessoes()
    {
        return $this->hasMany(Sessao::class);
    }

    public function evolucoes()
    {
        return $this->hasMany(Evolucao::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function arquivos()
    {
        return $this->hasMany(Arquivo::class);
    }
}
