<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingVideoLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainingVideoId',
        'lessonTitle',
        'description',
        'lessonVideoURL',
        'totalDuration',
        'status',
        'videoId'
    ];
    public function trainingVideo()
    {
        return $this->belongsTo(TrainingVideo::class, 'trainingVideoId', 'id');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_lesson', 'lessonId', 'playerId')
            ->withPivot('completionStatus', 'completed_at')
            ->withTimestamps();
    }
}
