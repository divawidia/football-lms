<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use App\Notifications\TrainingCourseLessons\Admin\TrainingLessonCreatedForAdmin;
use App\Notifications\TrainingCourseLessons\Admin\TrainingLessonDeletedForAdmin;
use App\Notifications\TrainingCourseLessons\Admin\TrainingLessonPublishedForAdmin;
use App\Notifications\TrainingCourseLessons\Admin\TrainingLessonUnpublishedForAdmin;
use App\Notifications\TrainingCourseLessons\Admin\TrainingLessonUpdatedForAdmin;
use App\Notifications\TrainingCourseLessons\Player\TrainingLessonCreatedForPlayer;
use App\Repository\PlayerRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class TrainingVideoLessonService extends Service
{
    private PlayerRepository $playerRepository;
    private TrainingVideoService $trainingVideoService;
    private UserRepository $userRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(PlayerRepository $playerRepository, TrainingVideoService $trainingVideoService, UserRepository $userRepository, DatatablesHelper $datatablesHelper)
    {
        $this->playerRepository = $playerRepository;
        $this->trainingVideoService = $trainingVideoService;
        $this->userRepository = $userRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function getTotalDuration(TrainingVideoLesson $trainingVideoLesson): string
    {
        return $this->secondToMinute($trainingVideoLesson->totalDuration);
    }
    public function index(TrainingVideo $trainingVideo): JsonResponse
    {
        return Datatables::of($trainingVideo->lessons)
            ->addColumn('action', function ($item) use ($trainingVideo) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('training-videos.lessons-show', ['trainingVideo'=>$trainingVideo->hash,'lesson'=>$item->hash]), icon: 'visibility', btnText: 'View video lesson');
                if (isAllAdmin()) {
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('editLesson', $item->id, icon: 'edit', btnText: 'Edit video lesson');
                    ($item->status == '1') ? $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('unpublish-lesson', $item->id, icon: 'block', iconColor: 'danger', btnText: 'Unpublish video lesson') : $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('publish-lesson', $item->id, icon: 'check_circle', iconColor: 'success', btnText: 'Publish video lesson');
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('deleteLesson', $item->id, iconColor: 'danger', icon: 'delete', btnText: 'Remove video lesson');
                }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('title', function ($item) {
                return '<h6>' . $item->lessonTitle . '</h6>';
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
                return ($item->status == '1') ? '<span class="badge badge-pill badge-success">Active</span>' : '<span class="badge badge-pill badge-danger">Non-Active</span>';
            })
            ->rawColumns(['action','title','description', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function players(TrainingVideoLesson $trainingVideoLesson): JsonResponse
    {
        return Datatables::of($trainingVideoLesson->players)
            ->addColumn('action', function ($item) use ($trainingVideoLesson) {
                return $this->datatablesHelper->buttonTooltips(route('training-videos.show-player', ['trainingVideo' => $trainingVideoLesson->trainingVideo->hash, 'player' => $item->hash]), 'View Player', 'visibility');
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('assignedAt', function ($item) {
                return $this->convertToDatetime($item->pivot->created_at);
            })
            ->editColumn('completedAt', function ($item) {
                return ($item->pivot->completed_at == null) ? 'Not completed yet' : $this->convertToDatetime($item->pivot->completed_at);
            })
            ->editColumn('status', function ($item) {
                return ($item->pivot->completionStatus == '1') ? '<span class="badge badge-pill badge-success">Completed</span>' : '<span class="badge badge-pill badge-warning">On Progress</span>';
            })
            ->rawColumns(['action','name','status'])
            ->addIndexColumn()
            ->make();
    }

    public function lessonUserPlayers(TrainingVideoLesson $lesson)
    {
        $playersId = collect($lesson->players)->pluck('id')->all();
        return $this->userRepository->getInArray('player', $playersId);
    }

    public function store(array $data, TrainingVideo $trainingVideo, $loggedUser){
        $lesson = $trainingVideo->lessons()->create($data);
        $playersId =  collect($trainingVideo->players)->pluck('id')->all();
        $lesson->players()->attach($playersId);

        Notification::send($this->lessonUserPlayers($lesson), new TrainingLessonCreatedForPlayer($trainingVideo, $lesson));
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonCreatedForAdmin($trainingVideo, $lesson, $loggedUser));

        return $lesson;
    }

    public function update(array $data, TrainingVideoLesson $trainingVideoLesson, $loggedUser): bool
    {
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonUpdatedForAdmin($trainingVideoLesson->trainingVideo, $trainingVideoLesson, $loggedUser));
        return $trainingVideoLesson->update($data);
    }

    public function markAsComplete($playerId, TrainingVideo $trainingVideo, TrainingVideoLesson $lesson): TrainingVideoLesson
    {
        $player = $this->playerRepository->find($playerId);
        $lesson->players()->updateExistingPivot($playerId, ['completionStatus' => '1', 'completed_at' => Carbon::now()]);
        $completionProgress = $this->trainingVideoService->playerCompletionProgress($player, $trainingVideo);
        $trainingVideo->players()->updateExistingPivot($playerId, ['progress' => $completionProgress]);

        if ($completionProgress == 100){
            $this->trainingVideoService->setPlayerProgressToComplete($player, $trainingVideo);
        }
        return $lesson;
    }

    public function setStatus(TrainingVideoLesson $trainingVideoLesson, $loggedUser, $status): bool
    {
        $trainingVideo =$trainingVideoLesson->trainingVideo;
        ($status == '1') ? $this->sendPublishedNotification($trainingVideo, $trainingVideoLesson, $loggedUser) : $this->sendUnpublishedNotification($trainingVideo, $trainingVideoLesson, $loggedUser);
        return $trainingVideoLesson->update(['status' => $status]);
    }
    private function sendPublishedNotification(TrainingVideo $trainingVideo, TrainingVideoLesson $trainingVideoLesson, $loggedUser): void
    {
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonPublishedForAdmin($trainingVideo, $trainingVideoLesson, $loggedUser));
    }
    private function sendUnpublishedNotification(TrainingVideo $trainingVideo, TrainingVideoLesson $trainingVideoLesson, $loggedUser): void
    {
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonUnpublishedForAdmin($trainingVideo, $trainingVideoLesson, $loggedUser));
    }

    public function destroy(TrainingVideoLesson $trainingVideoLesson, $loggedUser): bool
    {
        Notification::send($this->userRepository->getAllAdminUsers(), new TrainingLessonDeletedForAdmin($trainingVideoLesson->trainingVideo, $trainingVideoLesson, $loggedUser));
        return $trainingVideoLesson->delete();
    }
}
