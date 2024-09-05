<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'logo',
        'startDate',
        'endDate',
        'location',
        'contactName',
        'contactPhone',
        'description',
        'status',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'competition_team', 'competitionId', 'teamId')
            ->withPivot(
                'matchPlayed',
                'won',
                'drawn',
                'lost',
                'goalsFor',
                'goalsAgaints',
                'goalsDifference',
                'points',
                'redCards',
                'yellowCards',
                'groupDivision',
                'competitionResult',
            )->withTimestamps();
    }
    public function opponentTeams()
    {
        return $this->belongsToMany(OpponentTeam::class, 'competition_team', 'competitionId', 'teamId')
            ->withPivot(
                'matchPlayed',
                'won',
                'drawn',
                'lost',
                'goalsFor',
                'goalsAgaints',
                'goalsDifference',
                'points',
                'redCards',
                'yellowCards',
                'groupDivision',
                'competitionResult'
            )->withTimestamps();
    }
}
