<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Komentarz extends Model
{
    protected $table = 'komentarze';

    protected $fillable = [
        'godzina_pracy_id',
        'user_id',
        'tresc',
    ];

    public function godzinaPracy(): BelongsTo
    {
        return $this->belongsTo(GodzinaPracy::class, 'godzina_pracy_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
