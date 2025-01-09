<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId');
    }
    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competitionId');
    }
}
