<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMatchStats extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teamId',
        'eventId',
        'teamScore',
        'opponentTeamScore',
        'teamOwnGoal',
        'opponentTeamOwnGoal',
        'teamPossesion',
        'opponentTeamPossesion',
        'teamShotOnTarget',
        'opponentTeamShotOnTarget',
        'teamShots',
        'opponentTeamShots',
        'teamTouches',
        'opponentTeamTouches',
        'teamTackles',
        'opponentTeamTackles',
        'teamClearances',
        'opponentTeamClearances',
        'teamCorners',
        'opponentTeamCorners',
        'teamOffsides',
        'opponentOffsides',
        'teamYellowCards',
        'opponentTeamYellowCards',
        'teamRedCards',
        'opponentTeamRedCards',
        'teamFoulsConceded',
        'opponentFoulsConceded',
        'resultStatus'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId', 'id');
    }
    public function event()
    {
        return $this->belongsTo(EventSchedule::class, 'eventId', 'id');
    }
}
