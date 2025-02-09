<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Player;
use App\Models\TrainingVideo;
use App\Notifications\TrainingCourse\Admin\PlayerAssignedToTrainingCourseForAdmins;
use App\Notifications\TrainingCourse\Admin\PlayerRemovedFromTrainingCourseForAdmins;
use App\Notifications\TrainingCourse\Admin\PlayersCompleteTrainingCourseForAdmin;
use App\Notifications\TrainingCourse\Admin\TrainingCourseCreated;
use App\Notifications\TrainingCourse\Admin\TrainingCourseDeleted;
use App\Notifications\TrainingCourse\Admin\TrainingCoursePublished;
use App\Notifications\TrainingCourse\Admin\TrainingCourseUnpublished;
use App\Notifications\TrainingCourse\Admin\TrainingCourseUpdated;
use App\Notifications\TrainingCourse\Player\AssignPlayersToTrainingCourseForPlayers;
use App\Notifications\TrainingCourse\Player\PlayersCompleteTrainingCourseForPlayer;
use App\Notifications\TrainingCourse\Player\RemovePlayersFromTrainingCourseForPlayers;
use App\Repository\PlayerRepository;
use App\Repository\TrainingVideoRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class TrainingVideoService extends Service
{
    private TrainingVideoRepository $trainingVideoRepository;
    private UserRepository $userRepository;
    private PlayerRepository $playerRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(TrainingVideoRepository $trainingVideoRepository, UserRepository $userRepository, PlayerRepository $playerRepository, DatatablesHelper $datatablesHelper)
    {
        $this->trainingVideoRepository = $trainingVideoRepository;
        $this->userRepository = $userRepository;
        $this->playerRepository = $playerRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function index(){
        return $this->trainingVideoRepository->paginate(9);
    }
    public function playerIndex(Player $player): LengthAwarePaginator
    {
        return $this->trainingVideoRepository->playerPaginate($player, 9);
    }

    public function players(TrainingVideo $trainingVideo): JsonResponse
    {
        $data = $trainingVideo->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($trainingVideo) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('training-videos.show-player', ['trainingVideo' => $trainingVideo->hash, 'player' => $item->hash]), icon: 'visibility', btnText: 'View player');
                (isAllAdmin()) ? $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete', $item->id, iconColor: 'danger', icon: 'delete', btnText: 'Remove Player') : $dropdownItem.= "";
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('progress', function ($item) use ($trainingVideo) {
                return $this->playerCompletionProgress($item, $trainingVideo) .'%';
            })
            ->editColumn('assignedAt', function ($item) {
                return $this->convertToDatetime($item->pivot->created_at);
            })
            ->editColumn('completedAt', function ($item) {
                return ($item->pivot->completed_at == null) ? 'Not completed yet' : $this->convertToDatetime($item->pivot->completed_at);
            })
            ->editColumn('status', function ($item) use ($trainingVideo){
                return $this->playerCompletionStatus($item, $trainingVideo);
            })
            ->rawColumns(['action','name', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function getTotalDuration(TrainingVideo $trainingVideo): string
    {
        return $this->secondToMinute($trainingVideo->lessons()->sum('totalDuration'));
    }

    public function playerLessons(TrainingVideo $trainingVideo, Player $player): JsonResponse
    {
        $data = $player->lessons()
            ->whereRelation('trainingVideo', 'trainingVideoId', $trainingVideo->id)
            ->where('training_video_lessons.status', '1')
            ->get();
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($trainingVideo) {
                return $this->datatablesHelper->buttonTooltips(route('training-videos.lessons-show', ['trainingVideo'=>$trainingVideo->hash,'lesson'=>$item->hash]), 'View lesson', 'visibility');
            })
            ->editColumn('lessonTitle', function ($item) {
                return '<h6>' . $item->lessonTitle . '</h6>';
            })
            ->editColumn('totalDuration', function ($item) {
                return $this->secondToMinute($item->totalDuration);
            })
            ->editColumn('completedAt', function ($item) {
                return ($item->pivot->completed_at == null) ? 'Not completed yet' : $this->convertToDatetime($item->pivot->completed_at);
            })
            ->editColumn('assignedAt', function ($item) {
                return $this->convertToDatetime($item->pivot->created_at);
            })
            ->editColumn('status', function ($item) {
                return ($item->pivot->completionStatus == '1') ? '<span class="badge badge-pill badge-success">Completed</span>' : '<span class="badge badge-pill badge-warning">On Progress</span>';
            })
            ->rawColumns(['action','lessonTitle', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function playerCompletionOverview(TrainingVideo $trainingVideo): array
    {
        $playersCount = $trainingVideo->players()->count();
        $playerCompleted = [];
        $playerUncompleted = [];
        foreach ($trainingVideo->players as $player){
            if ($this->playerCompletionProgress($player, $trainingVideo) == 100){
                $playerCompleted[] = $player;
            } else {
                $playerUncompleted[] = $player;
            }
        }
        return compact('playersCount', 'playerCompleted', 'playerUncompleted');
    }

    public function playerShow(TrainingVideo $trainingVideo, Player $player)
    {
        $playerCompletionProgress = $this->playerCompletionProgress($player, $trainingVideo);
        $uncompletePlayerTrainingLesson = $this->uncompletePlayerTrainingLesson($player, $trainingVideo);
    }

    public function playersNotAssignedToTrainingCourse(TrainingVideo $trainingVideo)
    {
        return $this->playerRepository->getPlayerNotAssignedTrainingCourse($trainingVideo);
    }
    public function playerCompletionProgress(Player $player, TrainingVideo $trainingVideo): float
    {
        $totalLesson = $trainingVideo->lessons()->where('status', '1')->count();
        $totalPlayerComplete = $player->lessons()
            ->whereRelation('trainingVideo', 'trainingVideoId', $trainingVideo->id)
            ->where('training_video_lessons.status', '1')
            ->where('completionStatus', '1')
            ->count();
        $progress = $totalPlayerComplete/$totalLesson*100;
        return round($progress, 2);
    }

    public function playerCompletionStatus(Player $player, TrainingVideo $trainingVideo): string
    {
        return ($this->playerCompletionProgress($player, $trainingVideo) == 100) ? '<span class="badge badge-pill badge-success">Completed</span>' : '<span class="badge badge-pill badge-warning">On Progress</span>';
    }

    public function setPlayerProgressToComplete(Player $player, TrainingVideo $trainingVideo): int
    {
        Notification::send($this->userRepository->getAllAdminUsers(), new PlayersCompleteTrainingCourseForAdmin($trainingVideo, $this->convertToDatetime(Carbon::now()), $player->user));
        $player->user->notify(new PlayersCompleteTrainingCourseForPlayer($trainingVideo, $this->convertToDatetime(Carbon::now())));
        return $trainingVideo->players()->updateExistingPivot($player->id, ['status' => 'Completed', 'completed_at' => Carbon::now()]);
    }

    public function uncompletePlayerTrainingLesson(Player $player, TrainingVideo $trainingVideo)
    {
        return $player->lessons()->whereRelation('trainingVideo', 'trainingVideoId', $trainingVideo->id)->where('completionStatus', '0')->first();
    }


    public function store(array $data, $loggedUser){
        $data['previewPhoto'] = $this->storeImage($data, 'previewPhoto', 'assets/training-videos', 'images/video-preview.png');
        $data['userId'] = $loggedUser->id;
        $data['status'] = 1;
        $training = $this->trainingVideoRepository->create($data);
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingCourseCreated($training, $loggedUser));
        return $training;
    }

    public function update(array $data, TrainingVideo $trainingVideo, $loggedUser): bool
    {
        $data['previewPhoto'] = $this->updateImage($data, 'previewPhoto', 'assets/training-videos', $trainingVideo->previewPhoto);
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingCourseUpdated($trainingVideo, $loggedUser));
        return $trainingVideo->update($data);
    }

    public function setStatus(TrainingVideo $trainingVideo, $status, $loggedUser): bool
    {
        ($status == '1') ? $this->sendPublishedNotification($trainingVideo, $loggedUser) : $this->sendUnpublishedNotification($trainingVideo, $loggedUser);
        return $trainingVideo->update(['status' => $status]);
    }
    private function sendPublishedNotification(TrainingVideo $trainingVideo, $loggedUser): void
    {
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingCoursePublished($trainingVideo, $loggedUser));
    }
    private function sendUnpublishedNotification(TrainingVideo $trainingVideo, $loggedUser): void
    {
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingCourseUnpublished($trainingVideo, $loggedUser));
    }


    public function updatePlayer(array $data, TrainingVideo $trainingVideo, $loggedUser): TrainingVideo
    {
        $trainingVideo->players()->attach($data['players']);
        foreach($trainingVideo->lessons as $lesson){
            $lesson->players()->attach($data['players']);
        }

        $players = $this->userRepository->getInArray('player', $data['players']);

        Notification::send($players, new AssignPlayersToTrainingCourseForPlayers($trainingVideo));
        Notification::send($this->userRepository->getAllAdminUsers(), new PlayerAssignedToTrainingCourseForAdmins($trainingVideo, $loggedUser));

        return $trainingVideo;
    }

    public function removePlayer(TrainingVideo $trainingVideo, Player $player, $loggedUser): TrainingVideo
    {
        $trainingVideo->players()->detach($player);
        foreach($trainingVideo->lessons as $lesson){
            $lesson->players()->detach($player);
        }
        $player->user->notify(new RemovePlayersFromTrainingCourseForPlayers($trainingVideo));
        Notification::send($this->userRepository->getAllAdminUsers(), new PlayerRemovedFromTrainingCourseForAdmins($trainingVideo, $loggedUser));
        return $trainingVideo;
    }

    public function destroy(TrainingVideo $trainingVideo, $loggedUser): ?bool
    {
        $trainingVideo->players()->detach();
        $this->deleteImage($trainingVideo->previewPhoto);

        $playersId = collect($trainingVideo->players)->pluck('id')->all();
        $assignedPlayers = $this->userRepository->getInArray('player', $playersId);

        Notification::send($assignedPlayers, new RemovePlayersFromTrainingCourseForPlayers($trainingVideo));
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingCourseDeleted($trainingVideo, $loggedUser));

        return $trainingVideo->delete();
    }
}
