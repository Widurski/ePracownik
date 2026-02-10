<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GodzinaPracy extends Model
{
    protected $table = 'godziny_pracy';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'data_pracy',
        'liczba_godzin',
        'dodane_przez',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'data_pracy' => 'date',
        'liczba_godzin' => 'decimal:2',
    ];

    /**
     * Relacja: wpis nalezy do pracownika
     */
    public function pracownik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacja: wpis dodany przez uzytkownika
     */
    public function dodajacy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dodane_przez');
    }

    /**
     * Relacja: wpis ma wiele komentarzy
     */
    public function komentarze(): HasMany
    {
        return $this->hasMany(Komentarz::class, 'godzina_pracy_id');
    }
}
