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
    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coachId');
    }
    public function matchScore()
    {
        return $this->hasMany(MatchScore::class, 'eventId');
    }
    public function participant()
    {
        return $this->belongsToMany(Player::class, 'event_participants', 'eventId', 'participantId')
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
}
