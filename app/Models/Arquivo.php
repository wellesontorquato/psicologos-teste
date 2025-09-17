<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    /**
     * URL absoluta para o arquivo no storage/CDN.
     */
    public function getUrlAttribute()
    {
        if (!$this->caminho) {
            return null;
        }

        // se já estiver salvo como URL completa, apenas retorna
        if (Str::startsWith($this->caminho, ['http://', 'https://'])) {
            return $this->caminho;
        }

        $scheme  = env('ASSET_CDN_SCHEME', 'https');
        $host    = env('ASSET_CDN_HOST', 'usc1.contabostorage.com');
        $prefix  = ltrim(env('CONTABO_PUBLIC_PREFIX', ''), '/');

        $baseUrl = rtrim("$scheme://$host/$prefix", '/');

        return $baseUrl . '/' . ltrim($this->caminho, '/');
    }
}
