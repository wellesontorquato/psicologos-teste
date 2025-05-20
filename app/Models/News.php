<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
    ];

    // Rotas usam o slug no lugar do ID
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Accessor para retornar a URL pública da imagem (Contabo)
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            $prefix = rtrim(env('CONTABO_PUBLIC_PREFIX'), '/');
            return 'https://usc1.contabostorage.com/' . $prefix . '/' . ltrim($this->image, '/');
        }

        return null;
    }
}
