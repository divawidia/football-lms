<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalTeamMatch extends Model
{
    use HasFactory;

    protected $table = 'external_team_matches';

    protected $fillable = [
        'teamName',
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
        'goalScored',
        'cleanSheets',
    ];

    public function match()
    {
        return $this->hasOne(Match::class, 'eventId');
    }
}
