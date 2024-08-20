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
    public function opponentTeam()
    {
        return $this->belongsTo(OpponentTeam::class, 'opponentTeamsId');
    }
    public function matchScore()
    {
        return $this->hasMany(MatchScore::class, 'eventId');
    }
}
