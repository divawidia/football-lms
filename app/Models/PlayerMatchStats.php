<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMatchStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'playerId',
        'matchId',
        'teamId',
        'minutesPlayed',
        'goals',
        'assists',
        'ownGoal',
        'shots',
        'passes',
        'fouls',
        'yellowCards',
        'redCards',
        'saves',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'playerId', 'id');
    }
    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'matchId', 'id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teamId', 'id');
    }
}
