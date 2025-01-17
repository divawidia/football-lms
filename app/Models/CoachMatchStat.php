<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachMatchStat extends Model
{
    use HasFactory;
    protected $table ='coach_match_stats';

    protected $fillable = [
        'coachId',
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
        'goalConceded',
        'cleanSheets',
    ];

    public function coach()
    {
        $this->belongsTo(Coach::class, 'coachId', 'id');
    }

    public function team()
    {
        $this->belongsTo(Team::class, 'teamId', 'id');
    }

    public function match()
    {
        $this->belongsTo(Match::class, 'eventId', 'id');
    }
}
