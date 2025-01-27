<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Team extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'teamName',
        'ageGroup',
        'logo',
        'status',
        'teamSide',
        'academyId',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academy::class, 'academyId');
    }
    public function matches(): BelongsToMany
    {
        return $this->belongsToMany(MatchModel::class, 'team_match', 'teamId', 'matchId')
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

    public function coachMatchStats(): HasMany
    {
        return $this->hasMany(CoachMatchStats::class, 'teamId');
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(MatchModel::class, 'homeTeamId');
    }
    public function awayMatches(): HasMany
    {
        return $this->hasMany(MatchModel::class, 'awayTeamId');
    }
    public function winnerMatches(): HasMany
    {
        return $this->hasMany(MatchModel::class, 'winnerTeamId');
    }

    public function trainings()
    {
        return $this->hasMany(Training::class, 'teamId');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_teams', 'teamId', 'playerId')->withTimestamps();
    }
    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class, 'coach_team', 'teamId', 'coachId')->withTimestamps();
    }
}
