<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingVideoLesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trainingVideoId',
        'lessonTitle',
        'description',
        'lessonVideoURL',
        'totalMinutes',
        'status',
    ];
    public function trainingVideo()
    {
        return $this->belongsTo(TrainingVideo::class, 'trainingVideoId', 'id');
    }
}
