<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingVideoLessonRequest;
use App\Models\TrainingVideo;
use App\Services\TrainingVideoLessonService;
use RealRashid\SweetAlert\Facades\Alert;

class TrainingVideoLessonController extends Controller
{
    private TrainingVideoLessonService $trainingVideoLessonService;

    public function __construct(TrainingVideoLessonService $trainingVideoLessonService){
        $this->trainingVideoLessonService = $trainingVideoLessonService;
    }

    public function index(TrainingVideo $trainingVideo){
        return $this->trainingVideoLessonService->index($trainingVideo);
    }

    public function store(TrainingVideoLessonRequest $request, TrainingVideo $trainingVideo)
    {
        $data = $request->validated();
        $lesson = $this->trainingVideoLessonService->store($data, $trainingVideo);
        return response()->json($lesson);
    }

    public function edit( $trainingVideo)
    {
        return view('pages.admins.academies.training-videos.lessons.show',[
            'data' => $trainingVideo
        ]);
    }
}
