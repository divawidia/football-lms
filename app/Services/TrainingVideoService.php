<?php

namespace App\Services;

use App\Models\Player;
use App\Models\TrainingVideo;
use App\Notifications\TrainingCourse\AssignPlayersToTrainingCourse;
use App\Notifications\TrainingCourse\RemovePlayersFromTrainingCourse;
use App\Notifications\TrainingCourse\TrainingCourseCreated;
use App\Notifications\TrainingCourse\TrainingCourseDeleted;
use App\Notifications\TrainingCourse\TrainingCourseStatus;
use App\Notifications\TrainingCourse\TrainingCourseUpdated;
use App\Repository\TrainingVideoRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TrainingVideoService extends Service
{
    private TrainingVideoRepository $trainingVideoRepository;
    private UserRepository $userRepository;
    public function __construct(TrainingVideoRepository $trainingVideoRepository, UserRepository $userRepository)
    {
        $this->trainingVideoRepository = $trainingVideoRepository;
        $this->userRepository = $userRepository;
    }

    public function index(){
        return $this->trainingVideoRepository->paginate(9);
    }
    public function playerIndex(Player $player)
    {
        return $player->trainingVideos()->paginate(16);
    }

    public function players(TrainingVideo $trainingVideo){
        $data = $trainingVideo->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($trainingVideo) {
                return '<div class="btn-toolbar" role="toolbar">
                             <a class="btn btn-sm btn-outline-secondary mr-1" id="'.$item->id.'" href="'.route('training-videos.show-player', ['trainingVideo' => $trainingVideo->id, 'player' => $item->id]).'" data-toggle="tooltip" data-placement="bottom" title="View Player">
                                <span class="material-icons">visibility</span>
                             </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary deletePlayer" id="' . $item->id . '" data-toggle="tooltip" data-placement="bottom" title="Remove Player">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>';
            })
            ->editColumn('name', function ($item) {
                return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' '.$item->user->lastName.'</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('progress', function ($item) {
                return $item->pivot->progress .'%';
            })
            ->editColumn('assignedAt', function ($item) {
                return $this->convertToDatetime($item->pivot->created_at);
            })
            ->editColumn('completedAt', function ($item) {
                if ($item->pivot->completed_at == null){
                    $data = 'Not completed yet';
                }else{
                    $data = $this->convertToDatetime($item->pivot->completed_at);
                }
                return $data;
            })
            ->editColumn('status', function ($item) {
                if ($item->pivot->status == 'Completed') {
                    $badge = '<span class="badge badge-pill badge-success">Completed</span>';
                } elseif ($item->pivot->status == 'On Progress') {
                    $badge = '<span class="badge badge-pill badge-warning">On Progress</span>';
                }
                return $badge;
            })
            ->rawColumns(['action','name','progress','assignedAt', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function getTotalDuration(TrainingVideo $trainingVideo){
        return $this->secondToMinute($trainingVideo->lessons()->sum('totalDuration'));
    }

    public function playerLessons(TrainingVideo $trainingVideo, Player $player){
        $data = $player->lessons;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($trainingVideo) {
                return '<div class="btn-toolbar" role="toolbar">
                             <a class="btn btn-sm btn-outline-secondary mr-1" id="'.$item->id.'" href="'.route('training-videos.lessons-show', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$item->id]).'" data-toggle="tooltip" data-placement="bottom" title="View lesson">
                                <span class="material-icons">visibility</span>
                             </a>
                        </div>';
            })
            ->editColumn('lessonTitle', function ($item) {
                return '<p class="mb-0"><strong class="js-lists-values-lead">' . $item->lessonTitle . '</strong></p>';
            })
            ->editColumn('totalDuration', function ($item) {
                return $this->secondToMinute($item->totalDuration);
            })
            ->editColumn('completedAt', function ($item) {
                if ($item->pivot->completed_at == null){
                    $data = 'Not completed yet';
                }else{
                    $data = $this->convertToDatetime($item->pivot->completed_at);
                }
                return $data;
            })
            ->editColumn('assignedAt', function ($item) {
                return $this->convertToDatetime($item->pivot->created_at);
            })
            ->editColumn('status', function ($item) {
                if ($item->pivot->completionStatus == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Completed</span>';
                } elseif ($item->pivot->completionStatus == '0') {
                    $badge = '<span class="badge badge-pill badge-warning">On Progress</span>';
                }
                return $badge;
            })
            ->rawColumns(['action','lessonTitle','totalDuration','completedAt', 'assignedAt', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function playerShow(TrainingVideo $trainingVideo, Player $player)
    {
        $playerCompletionProgress = $this->playerCompletionProgress($player, $trainingVideo);
        $uncompletePlayerTrainingLesson = $this->uncompletePlayerTrainingLesson($player);
    }
    public function playerCompletionProgress(Player $player, TrainingVideo $trainingVideo)
    {
        $totalLesson = $trainingVideo->lessons()->count();
        $totalPlayerComplete = $player->lessons()->where('completionStatus', '1')->count();
        $progress = $totalPlayerComplete/$totalLesson*100;
        return round($progress, 2);
    }

    public function setPlayerProgressToComplete(Player $player, TrainingVideo $trainingVideo)
    {
        return $trainingVideo->players()->updateExistingPivot($player->id, ['status' => 'Completed', 'completed_at' => Carbon::now()]);
    }

    public function uncompletePlayerTrainingLesson(Player $player)
    {
        return $player->lessons()->where('completionStatus', '0')->first();
    }


    public function store(array $data, $userId){
        $data['previewPhoto'] = $this->storeImage($data, 'previewPhoto', 'assets/training-videos', 'images/video-preview.png');
        $data['userId'] = $userId;
        $training = $this->trainingVideoRepository->create($data);

        $createdUser = $this->userRepository->find($userId);
        $createdUserName = $this->getUserFullName($createdUser);

        try {
            Notification::send($this->userRepository->getAllAdminUsers(), new TrainingCourseCreated($training, $createdUserName));
            Notification::send($this->userRepository->getAllByRole('coach'), new TrainingCourseCreated($training, $createdUserName));
        } catch (Exception $exception) {
            Log::error('Error while sending create training course '.$training->trainingTitle.' notification: ' . $exception->getMessage());
        }
        return $training;
    }

    public function update(array $data, TrainingVideo $trainingVideo){
        $data['previewPhoto'] = $this->updateImage($data, 'previewPhoto', 'assets/training-videos', $trainingVideo->previewPhoto);
        $trainingVideo->update($data);

        try {
            Notification::send($this->userRepository->getAllAdminUsers(), new TrainingCourseUpdated($trainingVideo));
            Notification::send($this->userRepository->getAllByRole('coach'), new TrainingCourseUpdated($trainingVideo));
        } catch (\Exception $exception) {
            Log::error('Error while sending update training course '.$trainingVideo->trainingTitle.' notification: ' . $exception->getMessage());
        }

        return $trainingVideo;
    }

    public function setStatus(TrainingVideo $trainingVideo, $status)
    {
        $trainingVideo->update(['status' => $status]);

        if ($status == '1') {
            $statusMessage = 'published';
        } else {
            $statusMessage = 'unpublished';
        }

        try {
            Notification::send($this->userRepository->getAllAdminUsers(), new TrainingCourseStatus($trainingVideo, $statusMessage));
            Notification::send($this->userRepository->getAllByRole('coach'), new TrainingCourseStatus($trainingVideo, $statusMessage));
        } catch (\Exception $exception) {
            Log::error('Error while sending '.$statusMessage.' training course '.$trainingVideo->trainingTitle.' notification: ' . $exception->getMessage());
        }
    }

    public function updatePlayer(array $data, TrainingVideo $trainingVideo){
        $trainingVideo->players()->attach($data['players']);
        foreach($trainingVideo->lessons as $lesson){
            $lesson->players()->attach($data['players']);
        }

        $players = $this->userRepository->getInArray('player', $data['player']);

        try {
            Notification::send($players, new AssignPlayersToTrainingCourse($trainingVideo));
        } catch (\Exception $exception) {
            Log::error('Error while sending training course notification to the assigned players: ' . $exception->getMessage());
        }

        return $trainingVideo;
    }

    public function removePlayer(TrainingVideo $trainingVideo, Player $player){
        $trainingVideo->players()->detach($player);
        foreach($trainingVideo->lessons as $lesson){
            $lesson->players()->detach($player);
        }
        try {
            $player->user->notify(new RemovePlayersFromTrainingCourse($trainingVideo));
        } catch (\Exception $exception) {
            Log::error('Error while sending removed training course notification to the players: ' . $exception->getMessage());
        }

        return $trainingVideo;
    }

    public function destroy(TrainingVideo $trainingVideo){
        $trainingVideo->players()->detach();
        $this->deleteImage($trainingVideo->previewPhoto);

        $playersId = collect($trainingVideo->players)->pluck('playerId')->all();
        $assignedPlayers = $this->userRepository->getInArray('player', $playersId);

        try {
            Notification::send($assignedPlayers, new TrainingCourseDeleted($trainingVideo));
            Notification::send($this->userRepository->getAllAdminUsers(), new TrainingCourseDeleted($trainingVideo));
            Notification::send($this->userRepository->getAllByRole('coach'), new TrainingCourseDeleted($trainingVideo));
        } catch (\Exception $exception) {
            Log::error('Error while sending deleted training course notification: ' . $exception->getMessage());
        }

        return $trainingVideo->delete();
    }
}
