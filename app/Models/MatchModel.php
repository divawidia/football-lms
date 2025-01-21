<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class MatchModel extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'userId',
        'matchType',
        'competitionId',
        'date',
        'startTime',
        'endTime',
        'startDatetime',
        'endDatetime',
        'place',
        'status',
        'isReminderNotified',
        'homeTeamId',
        'awayTeamId',
        'winnerTeamId',
        'isExternalTeamWinner'
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_schedule', 'eventId', 'teamId')
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
                'goalConceded',
                'goalScored',
                'cleanSheets',
            )->withTimestamps();
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'homeTeamId');
    }
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'awayTeamId');
    }
    public function winnerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winnerTeamId');
    }

    public function externalTeam(): HasOne
    {
        return $this->hasOne(ExternalTeamMatch::class, 'eventId');
    }

    public function coachMatchStats(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class, 'coach_match_stats', 'eventId', 'coachId')
            ->withPivot(
                'teamId',
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
            )->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competitionId');
    }

    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class, 'coach_match_attendance', 'matchId', 'coachId')
            ->withPivot(
                'attendanceStatus',
                'note',
                'teamId',
            )->withTimestamps();
    }

    public function matchScores(): HasMany
    {
        return $this->hasMany(MatchScore::class, 'matchId');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_match_attendance', 'matchId', 'playerId')
            ->withPivot(
                'attendanceStatus',
                'note',
                'teamId',
            )->withTimestamps();
    }

    public function playerSkillStats(): HasMany
    {
        return $this->hasMany(PlayerSkillStats::class, 'matchId');
    }

    public function playerMatchStats(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_match_stats', 'matchId', 'playerId')
            ->withPivot(
                'minutesPlayed',
                'goals',
                'assists',
                'ownGoal',
                'shots',
                'passes',
                'fouls',
                'yellowCards',
                'redCards',
                'saves',
                'teamId',
            )->withTimestamps();
    }

    public function playerPerformanceReview(): HasMany
    {
        return $this->hasMany(PlayerPerformanceReview::class, 'matchId');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(MatchNote::class, 'matchId');
    }
}
