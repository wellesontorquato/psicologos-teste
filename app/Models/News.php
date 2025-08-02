<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'category',
        'slug',
        'excerpt',
        'content',
        'image',
    ];

    // Usa slug nas rotas
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Prefixo base do Contabo
    private function getContaboPrefix()
    {
        return rtrim('https://usc1.contabostorage.com/' . trim(env('CONTABO_PUBLIC_PREFIX'), '/'), '/');
    }

    // URL da imagem original
    public function getImageUrlAttribute()
    {
        return $this->image
            ? $this->getContaboPrefix() . '/' . ltrim($this->image, '/')
            : null;
    }

    // URL da versão WebP (gera direto, sem checar fisicamente)
    public function getImageWebpUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        $pathInfo = pathinfo($this->image);
        $dir = $pathInfo['dirname'] !== '.' ? $pathInfo['dirname'].'/' : '';
        $webpFile = $dir . $pathInfo['filename'] . '.webp';

        // Assume que o arquivo WebP existe no mesmo bucket Contabo
        return $this->getContaboPrefix() . '/' . ltrim($webpFile, '/');
    }

    // Garante que o slug seja salvo em minúsculas e sem espaços
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }
}
