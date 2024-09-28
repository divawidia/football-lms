<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingVideo;
use App\Services\TrainingVideoLessonService;

class TrainingVideoLessonController extends Controller
{
    private TrainingVideoLessonService $trainingVideoLessonService;

    public function __construct(TrainingVideoLessonService $trainingVideoLessonService){
        $this->trainingVideoLessonService = $trainingVideoLessonService;
    }

    public function index(TrainingVideo $trainingVideo){
        return $this->trainingVideoLessonService->index($trainingVideo);
    }
}
