<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class LeagueStanding extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'teamId',
        'competitionId',
        'matchPlayed',
        'won',
        'drawn',
        'lost',
        'goalsFor',
        'goalsAgainst',
        'goalsDifference',
        'points',
        'standingPositions'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teamId');
    }
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competitionId');
    }
}
