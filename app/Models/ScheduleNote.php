<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'competitionId',
    ];

    public function schedule()
    {
        return $this->belongsTo(EventSchedule::class, 'competitionId');
    }
}
