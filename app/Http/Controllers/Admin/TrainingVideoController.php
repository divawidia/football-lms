<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingVideoRequest;
use App\Models\Player;
use App\Models\TrainingVideo;
use App\Services\TrainingVideoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class TrainingVideoController extends Controller
{
    private TrainingVideoService $trainingVideoService;

    public function __construct(TrainingVideoService $trainingVideoService){
        $this->trainingVideoService = $trainingVideoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (isAllAdmin() || isCoach()){
            $data = $this->trainingVideoService->index();
        } elseif (isPlayer())
        {
            $data = $this->trainingVideoService->playerIndex($this->getLoggedPLayerUser());
        }
        return view('pages.academies.training-videos.index',[
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.academies.training-videos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrainingVideoRequest $request)
    {
        $data = $request->validated();
        $trainingVideos = $this->trainingVideoService->store($data, $this->getLoggedUserId());
        $message = 'Training course: '. $data['trainingTitle'] .' successfully created!';
        return ApiResponse::success($trainingVideos, $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainingVideo $trainingVideo)
    {
        $player = $this->trainingVideoService->playersNotAssignedToTrainingCourse($trainingVideo);
        $playerCompletionProgress = null;
        $uncompletePlayerTrainingLesson = null;
        if (isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $playerCompletionProgress = $this->trainingVideoService->playerCompletionProgress($player, $trainingVideo);
            $uncompletePlayerTrainingLesson = $this->trainingVideoService->uncompletePlayerTrainingLesson($player, $trainingVideo);
        }

        return view('pages.academies.training-videos.detail',[
            'player' => $player,
            'data' => $trainingVideo,
            'uncompletePlayerTrainingLesson' => $uncompletePlayerTrainingLesson,
            'playerCompletionProgress' => $playerCompletionProgress,
            'totalDuration' => $this->trainingVideoService->getTotalDuration($trainingVideo)
        ]);
    }

    public function players(TrainingVideo $trainingVideo){
        return $this->trainingVideoService->players($trainingVideo);
    }

    public function showPlayer(TrainingVideo $trainingVideo, Player $player)
    {
        return view('pages.academies.training-videos.players.detail',[
            'data' => $player,
            'progress' => $this->trainingVideoService->playerCompletionProgress($player, $trainingVideo),
            'status' => $this->trainingVideoService->playerCompletionStatus($player, $trainingVideo),
            'training' => $player->trainingVideos()->where('trainingVideoId', $trainingVideo->id)->first(),
            'totalCompleted' => $player->lessons()->whereRelation('trainingVideo', 'trainingVideoId', $trainingVideo->id)->where('training_video_lessons.status', '1')->where('completionStatus', '1')->count(),
            'totalOnProgress' => $player->lessons()->whereRelation('trainingVideo', 'trainingVideoId', $trainingVideo->id)->where('training_video_lessons.status', '1')->where('completionStatus', '0')->count(),
        ]);
    }

    public function playerLessons(TrainingVideo $trainingVideo, Player $player){
        return $this->trainingVideoService->playerLessons($trainingVideo, $player);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrainingVideo $trainingVideo): JsonResponse
    {
        return ApiResponse::success($trainingVideo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrainingVideoRequest $request, TrainingVideo $trainingVideo)
    {
        $data = $request->validated();

        $trainingVideos = $this->trainingVideoService->update($data, $trainingVideo);
        $message = "Training course: ".$trainingVideo->trainingTitle." successfully updated.";
        return ApiResponse::success($trainingVideos, $message);
    }

    public function unpublish(TrainingVideo $trainingVideo){
        $this->trainingVideoService->setStatus($trainingVideo, '0');
        $message = "Training course: ".$trainingVideo->trainingTitle."'s status successfully unpublished!.";
        return ApiResponse::success(message: $message);
    }

    public function publish(TrainingVideo $trainingVideo){
        $this->trainingVideoService->setStatus($trainingVideo, '1');
        $message = "Training course: ".$trainingVideo->trainingTitle."'s status successfully published!.";
        return ApiResponse::success(message: $message);
    }

    public function assignPlayer(TrainingVideo $trainingVideo)
    {
        $players = Player::with('user')->whereDoesntHave('trainingVideos', function (Builder $query) use ($trainingVideo){
            $query->where('trainingVideoId', $trainingVideo->id);
        })->get();

        return view('pages.academies.training-videos.assign-player',[
            'data' => $trainingVideo,
            'players' => $players,
        ]);
    }

    public function updatePlayers(Request $request, TrainingVideo $trainingVideo)
    {
        $data = $request->validate([
            'players' => ['required', Rule::exists('players', 'id')],
        ]);

        $this->trainingVideoService->updatePlayer($data, $trainingVideo);
        $text = 'Players successfully added to training course: '.$trainingVideo->trainingTitle.'!';
        return ApiResponse::success(message: $text);
    }

    public function removePLayer(TrainingVideo $trainingVideo, Player $player): JsonResponse
    {
        $result = $this->trainingVideoService->removePlayer($trainingVideo, $player);
        $message = "Player ".$this->getUserFullName($player->user)." successfully removed from training course.";
        return ApiResponse::success($result, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainingVideo $trainingVideo): JsonResponse
    {
        $result = $this->trainingVideoService->destroy($trainingVideo);
        $message = "Training course ".$trainingVideo->trainingTitle." successfully deleted.";
        return ApiResponse::success($result, $message);
    }
}
