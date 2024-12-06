<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'scheduleId',
        'teamId',
    ];

    public function schedule()
    {
        return $this->belongsTo(EventSchedule::class, 'competitionId');
    }
    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId');
    }
}
