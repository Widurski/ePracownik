<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Komentarz extends Model
{
    protected $table = 'komentarze';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'godzina_pracy_id',
        'user_id',
        'tresc',
    ];

    /**
     * Relacja: komentarz nalezy do wpisu godzin
     */
    public function godzinaPracy(): BelongsTo
    {
        return $this->belongsTo(GodzinaPracy::class, 'godzina_pracy_id');
    }

    /**
     * Relacja: komentarz nalezy do autora
     */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
