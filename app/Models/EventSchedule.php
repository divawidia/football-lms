<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teamId',
        'userId',
        'coachId',
        'opponentTeamsId',
        'eventType',
        'matchType',
        'eventName',
        'startDateTime',
        'endDateTime',
        'place',
        'note',
        'status'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId');
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
    public function teamMatchStats()
    {
        return $this->hasMany(TeamMatchStats::class, 'eventId');
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
