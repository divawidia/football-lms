<?php

namespace App\Services;

use App\Models\Player;
use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use App\Notifications\TrainingCourse\TrainingCourseDeleted;
use App\Notifications\TrainingCourse\TrainingCourseStatus;
use App\Notifications\TrainingCourseLessons\TrainingLessonCreated;
use App\Notifications\TrainingCourseLessons\TrainingLessonDeleted;
use App\Notifications\TrainingCourseLessons\TrainingLessonStatus;
use App\Notifications\TrainingCourseLessons\TrainingLessonUpdated;
use App\Repository\PlayerRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TrainingVideoLessonService extends Service
{
    private PlayerRepository $playerRepository;
    private TrainingVideoService $trainingVideoService;
    private UserRepository $userRepository;
    public function __construct(PlayerRepository $playerRepository, TrainingVideoService $trainingVideoService,  UserRepository $userRepository)
    {
        $this->playerRepository = $playerRepository;
        $this->trainingVideoService = $trainingVideoService;
        $this->userRepository = $userRepository;
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
                    $statusButton = '<form action="' . route('training-videos.lessons-unpublish', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$item->id]) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Unpublish lesson">
                                            <span class="material-icons">block</span>
                                        </button>
                                    </form>';
                } else {
                    $statusButton = '<form action="' . route('training-videos.lessons-publish', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$item->id]) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Publish lesson">
                                            <span class="material-icons">check_circle</span>
                                        </button>
                                    </form>';
                }
                return '<div class="btn-toolbar" role="toolbar">
                            <button class="btn btn-sm btn-outline-secondary mr-1 editLesson" id="'.$item->id.'" type="button" data-toggle="tooltip" data-placement="bottom" title="Edit lesson">
                                <span class="material-icons">edit</span>
                             </button>
                             <a class="btn btn-sm btn-outline-secondary mr-1" id="'.$item->id.'" href="'.route('training-videos.lessons-show', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$item->id]).'" data-toggle="tooltip" data-placement="bottom" title="View lesson">
                                <span class="material-icons">visibility</span>
                             </a>
                             '.$statusButton.'
                            <button type="button" class="btn btn-sm btn-outline-secondary deleteLesson" id="' . $item->id . '" data-toggle="tooltip" data-placement="bottom" title="Edit lesson">
                                <span class="material-icons">delete</span>
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
        $data = $trainingVideoLesson->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($trainingVideoLesson) {
                return '<div class="btn-toolbar" role="toolbar">
                             <a class="btn btn-sm btn-outline-secondary mr-1" id="'.$item->id.'" href="'.route('training-videos.show-player', ['trainingVideo' => $trainingVideoLesson->trainingVideoId, 'player' => $item->id]).'" data-toggle="tooltip" data-placement="bottom" title="View Player">
                                <span class="material-icons">visibility</span>
                             </a>
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
        $lesson = TrainingVideoLesson::create($data);
        $lesson->players()->attach($players);

        $createdUserName = $this->getUserFullName($loggedUser);

        try {
            Notification::send($this->lessonUserPlayers($lesson), new TrainingLessonCreated($trainingVideo, $lesson, role: 'player'));
            Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonCreated($trainingVideo, $lesson, $createdUserName, 'admin'));
            Notification::send($this->userRepository->getAllByRole('coach'), new TrainingLessonCreated($trainingVideo, $lesson, $createdUserName, 'coach'));
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
            Notification::send($this->userRepository->getAllByRole('coach'), new TrainingLessonUpdated($trainingVideo, $trainingVideoLesson, $createdUserName));
        } catch (Exception $exception) {
            Log::error('Error while sending update lesson '.$trainingVideoLesson->lessonTitle.' notification: ' . $exception->getMessage());
        }
    }

    public function markAsComplete($playerId, TrainingVideo $trainingVideo, TrainingVideoLesson $lesson)
    {
        $player = $this->playerRepository->find($playerId);

        $lesson->players()->updateExistingPivot($playerId, ['completionStatus' => '1', 'completed_at' => Carbon::now()]);

        if ($this->trainingVideoService->playerCompletionProgress($player, $trainingVideo) == 100){
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
            Notification::send($this->userRepository->getAllByRole('coach'), new TrainingLessonStatus($trainingVideo, $lesson, $statusMessage));
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
            Notification::send($this->userRepository->getAllByRole('coach'), new TrainingLessonDeleted($trainingVideo, $trainingVideoLesson));
        } catch (\Exception $exception) {
            Log::error('Error while sending deleted lesson '.$trainingVideoLesson->lessonTitle.' notification: ' . $exception->getMessage());
        }
        return $trainingVideoLesson->delete();
    }
}
