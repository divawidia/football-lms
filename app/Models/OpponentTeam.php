<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpponentTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teamName',
        'ageGroup',
        'logo',
        'status',
        'coachName',
        'directorName',
        'academyName',
        'totalPlayers'
    ];

    public function event()
    {
        return $this->hasMany(EventSchedule::class, 'opponentTeamId');
    }

    public function competitions()
    {
        return $this->belongsToMany(Competition::class, 'competition_team', 'teamId', 'competitionId')
            ->withPivot(
                'matchPlayed',
                'won',
                'drawn',
                'lost',
                'goalsFor',
                'goalsAgainsts',
                'goalsDifference',
                'points',
                'redCards',
                'yellowCards',
                'groupDivision',
                'competitionResult'
            )
            ->withTimestamps;
    }
}
