<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'nome',
        'caminho',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function getUrlAttribute()
    {
        $publicPrefix = env('CONTABO_PUBLIC_PREFIX');

        // URL CONTABO
        return 'https://usc1.contabostorage.com/' . $publicPrefix . '/' . ltrim($this->caminho, '/');
    }
}
