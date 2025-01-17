<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerMatchStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'playerId',
        'eventId',
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

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId', 'id');
    }
    public function event()
    {
        return $this->belongsTo(MatchModel::class, 'eventId', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId', 'id');
    }
}
