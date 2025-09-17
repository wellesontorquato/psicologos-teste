<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'is_admin',
        'trial_ends_at',
        'genero',
        'cpf',
        'data_nascimento',
        'tipo_profissional',
        'registro_profissional',
        'slug',
        'link_principal',
        'link_extra1',
        'link_extra2',
        'bio',
        'whatsapp',
        'areas',

        // Integração Google Calendar
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
        'google_calendar_id',
        'google_connected',
    ];

    protected $appends = ['profile_photo_url'];

    // Esconde tokens sensíveis quando o model é serializado
    protected $hidden = [
        'profile_photo_path',
        'google_access_token',
        'google_refresh_token',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'data_nascimento' => 'date',
        'areas' => 'array',

        // Segurança e tipos da integração Google
        'google_access_token' => 'encrypted',
        'google_refresh_token' => 'encrypted',
        'google_token_expires_at' => 'datetime',
        'google_connected' => 'boolean',
    ];

    /**
     * URL da foto de perfil (CDN/Contabo ou avatar padrão).
     */
    public function getProfilePhotoUrlAttribute()
    {
        // 1) Se não há foto, gera avatar padrão
        if (!$this->profile_photo_path) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
        }

        // 2) Se já está salva como URL absoluta, retorna direto
        if (Str::startsWith($this->profile_photo_path, ['http://', 'https://'])) {
            return $this->profile_photo_path;
        }

        // 3) Monta a base a partir do .env (mesmo padrão dos outros models)
        $scheme = env('ASSET_CDN_SCHEME', 'https');
        $host   = env('ASSET_CDN_HOST', 'usc1.contabostorage.com');
        $prefix = ltrim(env('CONTABO_PUBLIC_PREFIX', ''), '/'); // ex.: d1f52...:psigestor-files

        $base = rtrim("$scheme://$host/$prefix", '/');

        return $base . '/' . ltrim($this->profile_photo_path, '/');
    }

    /**
     * Define automaticamente o link principal
     * Se for apenas um número, converte em link do WhatsApp
     */
    public function setLinkPrincipalAttribute($value)
    {
        if ($value) {
            $limpo = preg_replace('/\D/', '', $value);

            // Se for apenas número e não contiver http
            if (is_numeric($limpo) && strlen($limpo) >= 10 && !Str::contains($value, ['http', 'www'])) {
                $this->attributes['link_principal'] = 'https://wa.me/55' . $limpo;
            } else {
                $this->attributes['link_principal'] = $value;
            }
        } else {
            $this->attributes['link_principal'] = null;
        }
    }

    /**
     * Retorna o link principal ou null
     */
    public function getLinkPrincipalAttribute($value)
    {
        return $value ?? null;
    }

    /**
     * Verifica se o usuário é admin
     */
    public function isAdmin(): bool
    {
        return (int) $this->is_admin === 1;
    }

    /**
     * Envia e-mail de verificação customizado
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}
