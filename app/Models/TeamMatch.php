<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMatch extends Model
{
    use HasFactory;

    protected $table = 'team_schedule';
    protected $fillable = [
        'teamId',
        'matchId',
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

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teamId');
    }
    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'matchId');
    }
}
