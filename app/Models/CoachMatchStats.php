<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachMatchStats extends Model
{
    use HasFactory;
    protected $table ='coach_match_stats';

    protected $fillable = [
        'coachId',
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
        'goalConceded',
        'goalScored',
        'cleanSheets',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'coachId', 'id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teamId', 'id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'matchId', 'id');
    }
}
