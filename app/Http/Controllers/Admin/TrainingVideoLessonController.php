<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingVideoLessonRequest;
use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use App\Services\TrainingVideoLessonService;
use Illuminate\Http\JsonResponse;
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

    public function show(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        return view('pages.admins.academies.training-videos.lessons.show',[
            'data' => $lesson
        ]);
    }

    public function edit(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): JsonResponse
    {
        return response()->json([
            'status' => '200',
            'data' => $lesson,
            'message' => 'Success'
        ]);
    }

    public function update(TrainingVideoLessonRequest $request, TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $data = $request->validated();

        return response()->json($this->trainingVideoLessonService->update($data, $lesson));
    }

    public function publish(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $this->trainingVideoLessonService->publish($lesson);

        $text = 'Lesson status successfully published!';
        Alert::success($text);
        return redirect()->route('training-videos.lessons-show', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$lesson->id]);
    }

    public function unpublish(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $this->trainingVideoLessonService->unpublish($lesson);

        $text = 'Lesson status successfully unpublished!';
        Alert::success($text);
        return redirect()->route('training-videos.lessons-show', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$lesson->id]);
    }
}
