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
     * Base pública para servir os arquivos.
     * Ex.: https://cdn.psigestor.com/d1f52aa...:psigestor-files
     *     (prefixo vem do .env)
     */
    private function getAssetsBaseUrl(): string
    {
        $scheme  = env('ASSET_CDN_SCHEME', 'https');
        $host    = env('ASSET_CDN_HOST', 'usc1.contabostorage.com'); // troque no .env
        $prefix  = ltrim(env('CONTABO_PUBLIC_PREFIX', ''), '/');     // ex.: d1f52...:psigestor-files

        // monta https://host/prefix  (sem barra final)
        $base = rtrim("$scheme://$host/" . $prefix, '/');
        return $base;
    }

    /**
     * Retorna URL absoluta para a imagem original.
     * Se já estiver salva como URL absoluta no banco, apenas retorna.
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return $this->getAssetsBaseUrl() . '/' . ltrim($this->image, '/');
    }

    /**
     * Retorna URL "derivada" em .webp (assume que o .webp exista no mesmo caminho).
     */
    public function getImageWebpUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // se já vier absoluta e terminar com .webp, retorna
        if (Str::startsWith($this->image, ['http://', 'https://']) && Str::endsWith($this->image, '.webp')) {
            return $this->image;
        }

        $pathInfo = pathinfo($this->image);
        $dir      = ($pathInfo['dirname'] ?? '.') !== '.' ? $pathInfo['dirname'].'/' : '';
        $webpFile = $dir . ($pathInfo['filename'] ?? 'image') . '.webp';

        // se for absoluta, apenas troca a extensão mantendo host/caminho
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return preg_replace('/\.[a-zA-Z0-9]+$/', '.webp', $this->image);
        }

        return $this->getAssetsBaseUrl() . '/' . ltrim($webpFile, '/');
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }
}
