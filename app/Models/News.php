<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'subtitle', 'category', 'slug', 'excerpt', 'content', 'image',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * URL da imagem original via proxy (/cdn).
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // se já estiver salva como URL absoluta
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return url('/cdn/' . ltrim($this->image, '/'));
    }

    /**
     * URL derivada em .webp via proxy (/cdn).
     */
    public function getImageWebpUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // se já for absoluta terminando em .webp, mantém
        if (Str::startsWith($this->image, ['http://', 'https://']) && Str::endsWith($this->image, '.webp')) {
            return $this->image;
        }

        // se já for absoluta mas não .webp, só troca a extensão
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return preg_replace('/\.[a-zA-Z0-9]+$/', '.webp', $this->image);
        }

        $info = pathinfo($this->image);
        $dir  = ($info['dirname'] ?? '.') !== '.' ? $info['dirname'].'/' : '';
        $webp = $dir . ($info['filename'] ?? 'image') . '.webp';

        return url('/cdn/' . ltrim($webp, '/'));
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }
}
