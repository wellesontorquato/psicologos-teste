<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Billable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'is_admin',
        'trial_ends_at',
        'genero',
        'cpf',
        'crp',
        'data_nascimento',
    ];

    protected $appends = ['profile_photo_url'];

    protected $hidden = ['profile_photo_path'];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'data_nascimento' => 'date',
    ];

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            $prefix = rtrim(env('CONTABO_PUBLIC_PREFIX'), '/');
            return 'https://usc1.contabostorage.com/' . $prefix . '/' . ltrim($this->profile_photo_path, '/');
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
 

    public function isAdmin(): bool
    {
        return (int) $this->is_admin === 1;
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}
