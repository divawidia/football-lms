<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class ScheduleNote extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'note',
        'scheduleId',
        'teamId',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(EventSchedule::class, 'scheduleId');
    }
    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId');
    }
}
