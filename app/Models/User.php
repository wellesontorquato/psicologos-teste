<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

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

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
    
    public function isAdmin(): bool
    {
        return (int) $this->is_admin === 1;
    }

}

