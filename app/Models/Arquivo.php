<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


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
        return Storage::disk('s3')->url($this->caminho);
    }
}
