<?php

namespace App\Services;

use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;

class TrainingVideoLessonService
{
    public function store(array $data, TrainingVideo $trainingVideo){
        $data['trainingVideoId'] = $trainingVideo->id;
        return TrainingVideoLesson::create($data);
    }
}
