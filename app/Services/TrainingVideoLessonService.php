<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use App\Notifications\TrainingCourseLessons\TrainingLessonCreated;
use App\Notifications\TrainingCourseLessons\TrainingLessonDeleted;
use App\Notifications\TrainingCourseLessons\TrainingLessonStatus;
use App\Notifications\TrainingCourseLessons\TrainingLessonUpdated;
use App\Repository\PlayerRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class TrainingVideoLessonService extends Service
{
    private PlayerRepository $playerRepository;
    private TrainingVideoService $trainingVideoService;
    private UserRepository $userRepository;
    private DatatablesHelper $datatablesService;
    public function __construct(PlayerRepository $playerRepository, TrainingVideoService $trainingVideoService, UserRepository $userRepository, DatatablesHelper $datatablesService)
    {
        $this->playerRepository = $playerRepository;
        $this->trainingVideoService = $trainingVideoService;
        $this->userRepository = $userRepository;
        $this->datatablesService = $datatablesService;
    }

    public function getTotalDuration(TrainingVideoLesson $trainingVideoLesson): string
    {
        return $this->secondToMinute($trainingVideoLesson->totalDuration);
    }
    public function index(TrainingVideo $trainingVideo){
        $data = $trainingVideo->lessons;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($trainingVideo) {
                if ($item->status == '1') {
                    $statusButton = '<button type="submit" class="btn btn-sm btn-outline-secondary mr-1 unpublish-lesson" id="'.$item->id.'" data-toggle="tooltip" data-placement="bottom" title="Unpublish lesson">
                                        <span class="material-icons text-danger">block</span>
                                    </button>';
                } else {
                    $statusButton = '<button type="submit" class="btn btn-sm btn-outline-secondary mr-1 publish-lesson" id="'.$item->id.'" data-toggle="tooltip" data-placement="bottom" title="Publish lesson">
                                        <span class="material-icons text-success">check_circle</span>
                                    </button>';
                }
                return '<div class="btn-toolbar" role="toolbar">
                            <button class="btn btn-sm btn-outline-secondary mr-1 editLesson" id="'.$item->id.'" type="button" data-toggle="tooltip" data-placement="bottom" title="Edit lesson">
                                <span class="material-icons">edit</span>
                             </button>
                             <a class="btn btn-sm btn-outline-secondary mr-1" id="'.$item->id.'" href="'.route('training-videos.lessons-show', ['trainingVideo'=>$trainingVideo->hash,'lesson'=>$item->hash]).'" data-toggle="tooltip" data-placement="bottom" title="View lesson">
                                <span class="material-icons">visibility</span>
                             </a>
                             '.$statusButton.'
                            <button type="button" class="btn btn-sm btn-outline-secondary deleteLesson" id="' . $item->id . '" data-toggle="tooltip" data-placement="bottom" title="Edit lesson">
                                <span class="material-icons text-danger">delete</span>
                            </button>
                        </div>';
            })
            ->editColumn('title', function ($item) {
                return '<p class="mb-0"><strong class="js-lists-values-lead">' . $item->lessonTitle . '</strong></p>';
            })
            ->editColumn('totalDuration', function ($item) {
                return $this->getTotalDuration($item);
            })
            ->editColumn('description', function ($item) {
                return $this->description($item->description);
            })
            ->editColumn('created_date', function ($item) {
                return $this->convertToDatetime($item->created_at);
            })
            ->editColumn('last_updated', function ($item) {
                return $this->convertToDatetime($item->updated_at);
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    $badge = '<span class="badge badge-pill badge-danger">Non-Active</span>';
                }
                return $badge;
            })
            ->rawColumns(['action','title','totalMinutes','description', 'created_date', 'last_updated', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function players(TrainingVideoLesson $trainingVideoLesson){
        $data = $trainingVideoLesson->players()->get();
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($trainingVideoLesson) {
                return '<div class="btn-toolbar" role="toolbar">
                             <a class="btn btn-sm btn-outline-secondary mr-1" id="'.$item->id.'" href="'.route('training-videos.show-player', ['trainingVideo' => $trainingVideoLesson->trainingVideo->hash, 'player' => $item->hash]).'" data-toggle="tooltip" data-placement="bottom" title="View Player">
                                <span class="material-icons">visibility</span>
                             </a>
                        </div>';
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
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
                if ($item->pivot->completionStatus == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Completed</span>';
                } elseif ($item->pivot->completionStatus == '0') {
                    $badge = '<span class="badge badge-pill badge-warning">On Progress</span>';
                }
                return $badge;
            })
            ->rawColumns(['action','name','assignedAt', 'completedAt','status'])
            ->addIndexColumn()
            ->make();
    }

    public function lessonUserPlayers(TrainingVideoLesson $lesson)
    {
        $playersId = collect($lesson->players)->pluck('playerId')->all();
        return $this->userRepository->getInArray('player', $playersId);
    }

    public function store(array $data, TrainingVideo $trainingVideo, $loggedUser){
        $data['trainingVideoId'] = $trainingVideo->id;
        $players = $trainingVideo->players()->select('playerId')->get();
        $playersId = $players->pluck('playerId');
        $lesson = TrainingVideoLesson::create($data);
        $lesson->players()->attach($playersId);

        $createdUserName = $this->getUserFullName($loggedUser);

        try {
            Notification::send($this->lessonUserPlayers($lesson), new TrainingLessonCreated($trainingVideo, $lesson, $createdUserName, role: 'player'));
            Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonCreated($trainingVideo, $lesson, $createdUserName, 'admin'));
            Notification::send($this->userRepository->getAll(role: 'coach'), new TrainingLessonCreated($trainingVideo, $lesson, $createdUserName, 'coach'));
        } catch (Exception $exception) {
            Log::error('Error while sending create lesson '.$lesson->lessonTitle.' notification: ' . $exception->getMessage());
        }

        return $lesson;
    }

    public function update(array $data, TrainingVideoLesson $trainingVideoLesson, $loggedUser){
        $trainingVideoLesson->update($data);

        $createdUserName = $this->getUserFullName($loggedUser);
        $trainingVideo =$trainingVideoLesson->trainingVideo;

        try {
            Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonUpdated($trainingVideo, $trainingVideoLesson, $createdUserName));
            Notification::send($this->userRepository->getAll(role: 'coach'), new TrainingLessonUpdated($trainingVideo, $trainingVideoLesson, $createdUserName));
        } catch (Exception $exception) {
            Log::error('Error while sending update lesson '.$trainingVideoLesson->lessonTitle.' notification: ' . $exception->getMessage());
        }
        return $trainingVideoLesson;
    }

    public function markAsComplete($playerId, TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $player = $this->playerRepository->find($playerId);
        $lesson->players()->updateExistingPivot($playerId, ['completionStatus' => '1', 'completed_at' => Carbon::now()]);
        $completionProgress = $this->trainingVideoService->playerCompletionProgress($player, $trainingVideo);
        $trainingVideo->players()->updateExistingPivot($playerId, ['progress' => $completionProgress]);

        if ($completionProgress == 100){
            $this->trainingVideoService->setPlayerProgressToComplete($player, $trainingVideo);
        }
        return response()->json(['message' => 'Video marked as complete']);
    }

    public function setStatus(TrainingVideoLesson $lesson, $status)
    {
        $lesson->update(['status' => $status]);
        $trainingVideo =$lesson->trainingVideo;

        if ($status == '1') {
            $statusMessage = 'published';
        } else {
            $statusMessage = 'unpublished';
        }

        try {
            Notification::send($this->lessonUserPlayers($lesson), new TrainingLessonStatus($trainingVideo, $lesson,$statusMessage));
            Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonStatus($trainingVideo, $lesson,$statusMessage));
            Notification::send($this->userRepository->getAll(role: 'coach'), new TrainingLessonStatus($trainingVideo, $lesson, $statusMessage));
        } catch (\Exception $exception) {
            Log::error('Error while sending '.$statusMessage.' lesson '.$lesson->lessonTitle.' notification: ' . $exception->getMessage());
        }
    }

    public function destroy(TrainingVideoLesson $trainingVideoLesson)
    {
        $trainingVideo =$trainingVideoLesson->trainingVideo;
        try {
            Notification::send($this->lessonUserPlayers($trainingVideoLesson), new TrainingLessonDeleted($trainingVideo, $trainingVideoLesson));
            Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonDeleted($trainingVideo, $trainingVideoLesson));
            Notification::send($this->userRepository->getAll(role:'coach'), new TrainingLessonDeleted($trainingVideo, $trainingVideoLesson));
        } catch (\Exception $exception) {
            Log::error('Error while sending deleted lesson '.$trainingVideoLesson->lessonTitle.' notification: ' . $exception->getMessage());
        }
        return $trainingVideoLesson->delete();
    }
}
