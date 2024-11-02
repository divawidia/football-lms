<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingVideoLessonRequest;
use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use App\Services\TrainingVideoLessonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function players(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson){
        return $this->trainingVideoLessonService->players($lesson);
    }

    public function store(TrainingVideoLessonRequest $request, TrainingVideo $trainingVideo)
    {
        $data = $request->validated();
        $lesson = $this->trainingVideoLessonService->store($data, $trainingVideo);
        return response()->json($lesson);
    }

    public function show(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $previousId = $trainingVideo->lessons()->where('id', '<', $lesson->id)->orderBy('id', 'desc')->first();
        $nextId = $trainingVideo->lessons()->where('id', '>', $lesson->id)->orderBy('id', 'desc')->first();
        $loggedPlayerUser = null;
        if (isPlayer()){
            $loggedPlayerUser = $this->getLoggedPLayerUser();
            $lessonCompletionStatus = $lesson->players()->where('playerId', $loggedPlayerUser->id)->first()->pivot->completionStatus;
        }

        return view('pages.academies.training-videos.lessons.detail',[
            'previousId' => $previousId,
            'nextId' => $nextId,
            'trainingVideo' => $trainingVideo,
            'data' => $lesson,
            'totalDuration' => $this->trainingVideoLessonService->getTotalDuration($lesson),
            'loggedPlayerUser' => $loggedPlayerUser
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

    public function markAsComplete(Request $request, TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $userId = $request->input('userId');

        $lesson->players()->updateExistingPivot($userId, ['completionStatus' => '1']);
        return response()->json(['message' => 'Video marked as complete']);
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

    public function destroy(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): JsonResponse
    {
        return response()->json($this->trainingVideoLessonService->destroy($lesson));
    }
}
