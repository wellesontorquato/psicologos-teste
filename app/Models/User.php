<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as CashierSubscription;

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

    protected $hidden = [
        'profile_photo_path',
        'google_access_token',
        'google_refresh_token',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'data_nascimento' => 'date',
        'areas' => 'array',
        'google_access_token' => 'encrypted',
        'google_refresh_token' => 'encrypted',
        'google_token_expires_at' => 'datetime',
        'google_connected' => 'boolean',
    ];

    /**
     * URL da foto de perfil via proxy (/cdn) ou avatar padrão.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo_path) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
        }

        // se já for URL absoluta (legado), retorna direto
        if (Str::startsWith($this->profile_photo_path, ['http://', 'https://'])) {
            return $this->profile_photo_path;
        }

        // senão, monta URL passando pelo proxy /cdn + cache busting
        return url('/cdn/' . ltrim($this->profile_photo_path, '/'))
            . '?v=' . ($this->updated_at?->timestamp ?? time());
    }

    public function setLinkPrincipalAttribute($value)
    {
        if ($value) {
            $limpo = preg_replace('/\D/', '', $value);

            if (is_numeric($limpo) && strlen($limpo) >= 10 && !Str::contains($value, ['http', 'www'])) {
                $this->attributes['link_principal'] = 'https://wa.me/55' . $limpo;
            } else {
                $this->attributes['link_principal'] = $value;
            }
        } else {
            $this->attributes['link_principal'] = null;
        }
    }

    public function getLinkPrincipalAttribute($value)
    {
        return $value ?? null;
    }

    public function isAdmin(): bool
    {
        return (int) $this->is_admin === 1;
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Retorna SEMPRE a assinatura mais recente e não cancelada (ends_at NULL)
     * com o nome 'default'. Evita pegar registros antigos.
     */
    public function currentSubscription(): ?CashierSubscription
    {
        return $this->subscriptions()
            ->where('name', 'default')
            ->whereNull('ends_at')
            ->latest('id')
            ->first();
    }

    /**
     * Regra central de acesso pago.
     * Libera para 'active', 'trialing' e (opcionalmente) 'past_due',
     * além do trial "global" do usuário (onTrial()).
     */
    public function hasPaidAccess(bool $allowPastDue = true): bool
    {
        $sub = $this->currentSubscription();

        $valid = ['active', 'trialing'];
        if ($allowPastDue) {
            $valid[] = 'past_due';
        }

        return ($sub && in_array($sub->stripe_status, $valid, true))
            || $this->onTrial();
    }
}
