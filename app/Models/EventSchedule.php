<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'userId',
        'eventType',
        'matchType',
        'eventName',
        'competitionId',
        'date',
        'startTime',
        'endTime',
        'place',
        'note',
        'status'
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
    public function matchScore()
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
    public function playerPerformanceReview()
    {
        return $this->hasMany(PlayerPerformanceReview::class, 'eventId');
    }
    public function notes()
    {
        return $this->hasMany(ScheduleNote::class, 'scheduleId');
    }
}
