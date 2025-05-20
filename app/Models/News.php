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

    // Opcional: rotas usam o slug em vez do ID
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
