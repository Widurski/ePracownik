<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'imie',
        'nazwisko',
        'email',
        'telefon',
        'password',
        'role_id',
        'is_active',
        'activation_token',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relacja: uzytkownik nalezy do roli
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relacja: uzytkownik ma wiele wpisow godzin pracy
     */
    public function godzinyPracy(): HasMany
    {
        return $this->hasMany(GodzinaPracy::class);
    }

    /**
     * Relacja: godziny dodane przez tego uzytkownika
     */
    public function dodaneGodziny(): HasMany
    {
        return $this->hasMany(GodzinaPracy::class, 'dodane_przez');
    }

    /**
     * Relacja: komentarze uzytkownika
     */
    public function komentarze(): HasMany
    {
        return $this->hasMany(Komentarz::class);
    }

    /**
     * Sprawdzenie czy uzytkownik jest pracownikiem
     */
    public function isPracownik(): bool
    {
        return $this->role->nazwa === 'pracownik';
    }

    /**
     * Sprawdzenie czy uzytkownik jest przelozonym
     */
    public function isPrzelozony(): bool
    {
        return $this->role->nazwa === 'przelozony';
    }

    /**
     * Sprawdzenie czy uzytkownik jest administratorem
     */
    public function isAdmin(): bool
    {
        return $this->role->nazwa === 'administrator';
    }
}
