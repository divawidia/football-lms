<?php

namespace App\Http\Controllers\Admin;

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
        $lesson = $this->trainingVideoLessonService->store($data, $trainingVideo);
        return response()->json($lesson);
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

        return response()->json($this->trainingVideoLessonService->update($data, $lesson));
    }

    public function markAsComplete(Request $request, TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        try {
            $playerId = $request->input('playerId');
            $this->trainingVideoLessonService->markAsComplete($playerId, $trainingVideo, $lesson);
            return response()->json(['message' => 'Video marked as complete']);
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
