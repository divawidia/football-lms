<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Training extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'userId',
        'teamId',
        'topic',
        'location',
        'date',
        'startTime',
        'endTime',
        'startDatetime',
        'endDatetime',
        'status',
        'isReminderNotified',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teamId');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }


    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class, 'coach_training_attendance', 'trainingId', 'coachId')
            ->withPivot(
                'attendanceStatus',
                'note',
                'teamId',
            )->withTimestamps();
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_training_attendance', 'trainingId', 'playerId')
            ->withPivot(
                'attendanceStatus',
                'note',
                'teamId',
            )->withTimestamps();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(TrainingNote::class, 'trainingId');
    }
}
