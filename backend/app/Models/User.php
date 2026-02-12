<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

/**
 * @property int $id
 * @property string $name
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $phone_number
 * @property string $password
 * @property int $role_id
 * @property bool $is_active
 * @property string|null $activation_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Role|null $role
 *
 * @method \Laravel\Sanctum\NewAccessToken createToken(string $name, array $abilities = ['*'])
 * @method \Laravel\Sanctum\PersonalAccessToken|null currentAccessToken()
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'login',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password',
        'role_id',
        'is_active',
        'activation_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function godzinyPracy(): HasMany
    {
        return $this->hasMany(GodzinaPracy::class);
    }

    public function dodaneGodziny(): HasMany
    {
        return $this->hasMany(GodzinaPracy::class, 'dodane_przez');
    }

    public function komentarze(): HasMany
    {
        return $this->hasMany(Komentarz::class);
    }

    public function isPracownik(): bool
    {
        return $this->role->name === 'pracownik';
    }

    public function isPrzelozony(): bool
    {
        return $this->role->name === 'przelozony';
    }

    public function isAdmin(): bool
    {
        return $this->role->name === 'administrator';
    }
}
