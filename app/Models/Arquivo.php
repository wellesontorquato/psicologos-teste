<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Arquivo extends Model
{
    use HasFactory;

    protected $fillable = ['paciente_id','nome','caminho'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * URL pública via proxy do Laravel (/cdn/{path}).
     * Não expõe Contabo.
     */
    public function getUrlAttribute()
    {
        if (!$this->caminho) {
            return null;
        }

        // Se já for URL absoluta, retorna como está (caso legado)
        if (Str::startsWith($this->caminho, ['http://', 'https://'])) {
            return $this->caminho;
        }

        // Sempre aponta para o proxy público
        return url('/cdn/' . ltrim($this->caminho, '/'));
    }
}
