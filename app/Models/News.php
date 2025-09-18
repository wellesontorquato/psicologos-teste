<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // ⬅️ importa Storage

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

    /** URL da imagem original via proxy (/cdn). */
    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image; // legado absoluto
        }

        return url('/cdn/' . ltrim($this->image, '/'));
    }

    /** URL .webp via proxy (/cdn) — só retorna se existir; senão, null (fallback usa a original). */
    public function getImageWebpUrlAttribute()
    {
        if (!$this->image) return null;

        // Se já for absoluta e terminar com .webp, mantém (não tem como checar existência externa)
        if (Str::startsWith($this->image, ['http://', 'https://']) && Str::endsWith($this->image, '.webp')) {
            return $this->image;
        }

        // Caminho relativo: calcula o .webp
        $info = pathinfo($this->image);
        $dir  = ($info['dirname'] ?? '.') !== '.' ? $info['dirname'].'/' : '';
        $webpRel = $dir . ($info['filename'] ?? 'image') . '.webp';

        // Só retorna se existir no bucket; senão, null
        try {
            if (Storage::disk('s3')->exists($webpRel)) {
                return url('/cdn/' . ltrim($webpRel, '/'));
            }
        } catch (\Throwable $e) {
            // se der algum erro na checagem, silenciosamente não usa webp
        }

        return null; // força o <img> usar $this->image_url
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }
}
