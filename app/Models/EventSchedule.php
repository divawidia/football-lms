<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'eventType',
        'matchType',
        'eventName',
        'competitionId',
        'date',
        'startTime',
        'endTime',
        'startDatetime',
        'endDatetime',
        'place',
        'note',
        'status',
        'isOpponentTeamMatch'
    ];

    public function teams()
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
                'cleanSheets',
            )->withTimestamps();
    }

    public function matches()
    {
        return $this->hasMany(TeamMatch::class, 'eventId');
    }

    public function coachMatchStats()
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
                'cleanSheets',
            )->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competitionId');
    }

    public function coaches()
    {
        return $this->belongsToMany(Coach::class, 'coach_attendance', 'scheduleId', 'coachId')
            ->withPivot(
                'attendanceStatus',
                'note'
            )->withTimestamps();
    }

    public function matchScores()
    {
        return $this->hasMany(MatchScore::class, 'eventId');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_attendance', 'scheduleId', 'playerId')
            ->withPivot(
                'attendanceStatus',
                'note'
            )->withTimestamps();
    }

    public function playerSkillStats()
    {
        return $this->hasMany(PlayerSkillStats::class, 'eventId');
    }

    public function playerMatchStats()
    {
        return $this->belongsToMany(Player::class, 'player_match_stats', 'eventId', 'playerId')
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
            )->withTimestamps();
    }

    public function playerPerformanceReview()
    {
        return $this->hasMany(PlayerPerformanceReview::class, 'eventId');
    }

    public function notes()
    {
        return $this->hasMany(ScheduleNote::class, 'scheduleId');
    }
}
