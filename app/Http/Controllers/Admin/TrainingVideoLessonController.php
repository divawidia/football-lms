<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingVideoLessonRequest;
use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use App\Services\TrainingVideoLessonService;
use App\Services\TrainingVideoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

    public function index(TrainingVideo $trainingVideo): JsonResponse
    {
        return $this->trainingVideoLessonService->index($trainingVideo);
    }

    public function players(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): JsonResponse
    {
        return $this->trainingVideoLessonService->players($lesson);
    }

    public function store(TrainingVideoLessonRequest $request, TrainingVideo $trainingVideo): JsonResponse
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        $lesson = $this->trainingVideoLessonService->store($data, $trainingVideo, $loggedUser);
        return ApiResponse::success($lesson, 'Lesson: '.$lesson->lessonTitle.' successfully added!');
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
        return ApiResponse::success($lesson);
    }

    public function update(TrainingVideoLessonRequest $request, TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): JsonResponse
    {
        $data = $request->validated();
        $this->trainingVideoLessonService->update($data, $lesson, $this->getLoggedUser());
        return ApiResponse::success(message: "Lesson {$lesson->lessonTitle} successfully updated.");
    }

    public function markAsComplete(Request $request, TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): JsonResponse
    {
        try {
            $playerId = $request->input('playerId');
            $result = $this->trainingVideoLessonService->markAsComplete($playerId, $trainingVideo, $lesson);
            return ApiResponse::success($result, "Lesson ".$lesson->lessonTitle." marked as complete.");
        } catch (Exception $e) {
            Log::error('Error marking video as complete: ' . $e->getMessage());
            return ApiResponse::error('An error occurred while marking the video as complete.');
        }
    }

    public function trainingVideoCompleted(TrainingVideo $trainingVideo): RedirectResponse
    {
        Alert::success('Congrats! You have been completed this '.$trainingVideo->trainingTitle.' training');
        return redirect()->route('training-videos.show', $trainingVideo->hash);
    }

    public function publish(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): JsonResponse
    {
        $this->trainingVideoLessonService->setStatus($lesson, $this->getLoggedUser(),'1');
        return ApiResponse::success(message: 'Training video lesson '.$lesson->lessonTitle.' status successfully published!');
    }

    public function unpublish(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): JsonResponse
    {
        $this->trainingVideoLessonService->setStatus($lesson, $this->getLoggedUser(), '0');
        return ApiResponse::success(message: 'Training video lesson '.$lesson->lessonTitle.' status successfully unpublished!');
    }

    public function destroy(TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): JsonResponse
    {
        $this->trainingVideoLessonService->destroy($lesson, $this->getLoggedUser());
        return ApiResponse::success(message: "Lesson ".$lesson->lessonTitle." successfully deleted.");
    }
}
