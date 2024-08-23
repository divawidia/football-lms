<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'position',
        'skill',
        'strongFoot',
        'height',
        'weight',
        'joinDate',
        'userId'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function parrents()
    {
        return $this->hasMany(PlayerParrent::class, 'playerId');
    }
    public function playerMatchStats()
    {
        return $this->hasMany(PlayerMatchStats::class, 'playerId');
    }
    public function playerSkillStats()
    {
        return $this->hasMany(PlayerSkillStats::class, 'playerId');
    }
    public function playerPerformanceReview()
    {
        return $this->hasMany(PlayerPerformanceReview::class, 'playerId');
    }
    public function event()
    {
        return $this->belongsToMany(EventSchedule::class, 'event_participants', 'participantId', 'eventId')
            ->withPivot(
                'attendanceStatus',
                'note'
            )->withTimestamps();
    }
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'player_teams', 'playerId', 'teamId')->withTimestamps();
    }
    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'playerId', 'id');
    }
    public function trainingVideo()
    {
        return $this->belongsToMany(TrainingVideo::class, 'training_video_players', 'playerId', 'trainingVideoId')
            ->withPivot('progress', 'status')->withTimestamps();
    }
}
