<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMatch extends Model
{
    use HasFactory;

    protected $table = 'team_schedule';
    protected $fillable = [
        'teamId',
        'eventId',
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
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId');
    }
    public function match()
    {
        return $this->belongsTo(EventSchedule::class, 'eventId');
    }
}
