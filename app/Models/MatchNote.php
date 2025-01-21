<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class MatchNote extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'note',
        'matchId',
        'teamId',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'matchId');
    }
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teamId');
    }
}
