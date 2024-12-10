<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingVideoLessonRequest;
use App\Models\Player;
use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use App\Services\TrainingVideoLessonService;
use App\Services\TrainingVideoService;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class TrainingVideoLessonController extends Controller
{
    private TrainingVideoLessonService $trainingVideoLessonService;
    private TrainingVideoService $trainingVideoService;

    public function __construct(TrainingVideoLessonService $trainingVideoLessonService, TrainingVideoService $trainingVideoService){
        $this->trainingVideoLessonService = $trainingVideoLessonService;
        $this->trainingVideoService = $trainingVideoService;
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
        $loggedUser = $this->getLoggedUser();
        $lesson = $this->trainingVideoLessonService->store($data, $trainingVideo, $loggedUser);
        $message = 'Lesson: '.$lesson->lessonTitle.' successfully added!';
        return ApiResponse::success($lesson, $message);
    }

    public function show(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        return view('pages.academies.training-videos.lessons.detail',[
            'trainingVideo' => $trainingVideo,
            'data' => $lesson,
            'totalDuration' => $this->trainingVideoLessonService->getTotalDuration($lesson),
        ]);
    }

    public function showPlayerLesson(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $previousId = $trainingVideo->lessons()->where('id', '<', $lesson->id)->orderBy('id', 'desc')->first();
        $nextId = $trainingVideo->lessons()->where('id', '>', $lesson->id)->orderBy('id')->first();
        $loggedPlayerUser = $this->getLoggedPLayerUser();
        $lessonCompletionStatus = $lesson->players()->where('playerId', $loggedPlayerUser->id)->first()->pivot->completionStatus;
        $playerCompletionProgress = $this->trainingVideoService->playerCompletionProgress($loggedPlayerUser, $trainingVideo);

        return view('pages.academies.training-videos.lessons.detail-for-player',[
            'previousId' => $previousId,
            'nextId' => $nextId,
            'trainingVideo' => $trainingVideo,
            'data' => $lesson,
            'totalDuration' => $this->trainingVideoLessonService->getTotalDuration($lesson),
            'loggedPlayerUser' => $loggedPlayerUser,
            'playerCompletionProgress' => $playerCompletionProgress,
            'lessonCompletionStatus' => $lessonCompletionStatus,
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
        $loggedUser = $this->getLoggedUser();
        $result = $this->trainingVideoLessonService->update($data, $lesson, $loggedUser);
        $message = "Lesson ".$lesson->lessonTitle." successfully updated.";
        return ApiResponse::success($result, $message);
    }

    public function markAsComplete(Request $request, TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        try {
            $playerId = $request->input('playerId');
            $result = $this->trainingVideoLessonService->markAsComplete($playerId, $trainingVideo, $lesson);
            $message = "Lesson ".$lesson->lessonTitle." marked as complete.";
            return ApiResponse::success($result, $message);
        } catch (Exception $e) {
            Log::error('Error marking video as complete: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while marking the video as complete.'], 500);
        }
    }

    public function trainingVideoCompleted(TrainingVideo $trainingVideo)
    {
        $text = 'Congrats! You have been completed this '.$trainingVideo->trainingTitle.' training';
        Alert::success($text);
        return redirect()->route('training-videos.show', $trainingVideo->id);
    }

    public function publish(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $this->trainingVideoLessonService->setStatus($lesson, '1');

        $text = 'Lesson '.$lesson->lessonTitle.' status successfully published!';
        Alert::success($text);
        return redirect()->route('training-videos.lessons-show', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$lesson->id]);
    }

    public function unpublish(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $this->trainingVideoLessonService->setStatus($lesson, '0');

        $text = 'Lesson '.$lesson->lessonTitle.' status successfully unpublished!';
        Alert::success($text);
        return redirect()->route('training-videos.lessons-show', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$lesson->id]);
    }

    public function destroy(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): JsonResponse
    {
        $result = $this->trainingVideoLessonService->destroy($lesson);
        $message = "Lesson ".$lesson->lessonTitle." successfully deleted.";
        return ApiResponse::success($result, $message);
    }
}
