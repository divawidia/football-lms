<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model implements \Countable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teamName',
        'ageGroup',
        'logo',
        'status',
        'teamSide',
        'academyId',
    ];

    public function academy()
    {
        return $this->belongsTo(Academy::class, 'academyId');
    }
    public function schedules()
    {
        return $this->belongsToMany(EventSchedule::class, 'team_schedule', 'teamId', 'eventId')
            ->withPivot(
                'teamScore',
                'teamOwnGoal',
                'teamPossesion',
                'teamShotOnTarget',
                'teamShots',
                'teamTouches',
                'teamTackles',
                'teamClearances',
                'teamCorners',
                'teamOffsides',
                'teamYellowCards',
                'teamRedCards',
                'teamFoulsConceded',
                'resultStatus',
                'teamPasses',
            )->withTimestamps();
    }

    public function coachMatchStats()
    {
        return $this->hasMany(CoachMatchStat::class, 'teamId');
    }

    public function matches(){
        return $this->hasMany(TeamMatch::class, 'teamId');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_teams', 'teamId', 'playerId')->withTimestamps();
    }
    public function coaches()
    {
        return $this->belongsToMany(Coach::class, 'coach_team', 'teamId', 'coachId')->withTimestamps();
    }

    public function divisions()
    {
        return $this->belongsToMany(GroupDivision::class, 'competition_team', 'teamId', 'divisionId')
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
                'competitionResult'
            )
            ->withTimestamps();
    }
}
