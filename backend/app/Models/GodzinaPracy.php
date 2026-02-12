<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GodzinaPracy extends Model
{
    protected $table = 'godziny_pracy';

    protected $fillable = [
        'user_id',
        'data_pracy',
        'liczba_godzin',
        'dodane_przez',
    ];

    protected $casts = [
        'data_pracy' => 'date',
        'liczba_godzin' => 'decimal:2',
    ];

    public function pracownik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dodajacy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dodane_przez');
    }

    public function komentarze(): HasMany
    {
        return $this->hasMany(Komentarz::class, 'godzina_pracy_id');
    }
}
