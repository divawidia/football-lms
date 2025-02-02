<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceStatusRequest;
use App\Http\Requests\ScheduleNoteRequest;
use App\Http\Requests\TrainingScheduleRequest;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Training;
use App\Models\TrainingNote;
use App\Services\TrainingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TrainingController extends Controller
{
    private TrainingService $trainingService;
    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->isAllAdmin()){
            $events = $this->trainingService->trainingCalendar();
            $tableRoute = route('training-schedules.admin-index');
        } elseif ($this->isCoach()){
            $events = $this->trainingService->coachTeamsTrainingCalendar($this->getLoggedCoachUser());
            $tableRoute = route('training-schedules.coach-index');
        } else {
            $events = $this->trainingService->playerTeamsTrainingCalendar($this->getLoggedPLayerUser());
            $tableRoute = route('training-schedules.player-index');
        }

        return view('pages.academies.schedules.trainings.index', [
            'events' => $events,
            'tableRoute' => $tableRoute,
        ]);
    }

    public function adminIndexTraining(): JsonResponse
    {
        return $this->trainingService->dataTablesTraining();
    }

    public function coachIndexTraining(): JsonResponse
    {
        $coach = $this->getLoggedCoachUser();
        return $this->trainingService->coachTeamsDataTablesTraining($coach);
    }
    public function playerIndexTraining(): JsonResponse
    {
        $player = $this->getLoggedPLayerUser();
        return $this->trainingService->playerTeamsDataTablesTraining($player);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.academies.schedules.trainings.create', [
            'teams' => (isAllAdmin()) ? $this->trainingService->createTraining() : $this->trainingService->createTraining($this->getLoggedCoachUser()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrainingScheduleRequest $request): JsonResponse
    {
        $this->trainingService->storeTraining($request->validated(), $this->getLoggedUser());
        return ApiResponse::success(message: 'Training schedule successfully added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Training $training)
    {
        return view('pages.academies.schedules.trainings.detail', [
            'schedule' => $training,
            'players' => $training->players,
            'coaches' => $training->coaches,
            'allSkills' => $this->trainingService->allSkills($training, $this->getLoggedPLayerUser()),
            'playerPerformanceReview' => $this->trainingService->playerPerformanceReviews($training, $this->getLoggedPLayerUser()),
            'totalParticipant' => $this->trainingService->totalParticipant($training),
            'totalAttend' => $this->trainingService->totalAttend($training),
            'totalDidntAttend' => $this->trainingService->totalDidntAttend($training),
            'totalIllness' => $this->trainingService->totalIllness($training),
            'totalInjured' => $this->trainingService->totalInjured($training),
            'totalOther' => $this->trainingService->totalOther($training),
            'playerDidntAttend' => $this->trainingService->playerDidntAttend($training),
            'coachDidntAttend' => $this->trainingService->coachDidntAttend($training),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Training $training): JsonResponse
    {
        return ApiResponse::success($training);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrainingScheduleRequest $request, Training $training): JsonResponse
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        $this->trainingService->updateTraining($data, $training, $loggedUser);
        return ApiResponse::success(message: 'Training session successfully updated!');
    }

    public function status(Training $training, $status): JsonResponse
    {
        try {
            $this->trainingService->setStatus($training, $status, $this->getLoggedUser());
            return ApiResponse::success(message: $training->topic.' training session status successfully set to '.$status.'!');

        } catch (Exception $e) {
            Log::error('Error marking '.$training->topic.' session as '.$status.': ' . $e->getMessage());
            return ApiResponse::error('An error occurred while marking the competition '.$training->eventType.' session as '.$status.'.');
        }
    }

    public function scheduled(Training $training): JsonResponse
    {
        if ($training->startDatetime < Carbon::now()) {
            return ApiResponse::error("You cannot set the match session to scheduled because the match date has passed, please change the match start date to a future date.");
        } else {
            return $this->status($training, 'Scheduled');
        }
    }

    public function ongoing(Training $training): JsonResponse
    {
        return $this->status($training, 'Ongoing');
    }
    public function completed(Training $training): JsonResponse
    {
        return $this->status($training, 'Completed');
    }
    public function cancelled(Training $training): JsonResponse
    {
        return $this->status($training, 'Cancelled');
    }

    public function getPlayerAttendance(Training $training, Player $player): JsonResponse
    {
        try {
            $data = $this->trainingService->getPlayerAttendance($training, $player);
            $data = [
                'user' => $data->user,
                'playerAttendance'=>$data->pivot
            ];
            return ApiResponse::success($data, message:  'Successfully retrieved player attendance data');

        } catch (Exception $e){
            $message = "Error while retrieving player attendance data: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function getCoachAttendance(Training $training, Coach $coach): JsonResponse
    {
        try {
            $data = $this->trainingService->getCoachAttendance($training, $coach);
            $data = [
                'user' => $data->user,
                'coachAttendance'=>$data->pivot
            ];
            return ApiResponse::success($data, message:  'Successfully retrieved coach attendance data');

        } catch (Exception $e){
            $message = "Error while retrieving coach attendance data: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updatePlayerAttendance(AttendanceStatusRequest $request, Training $training, Player $player): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->trainingService->updatePlayerAttendanceStatus($data, $training, $player);
            $message = "Player ".$this->getUserFullName($player->user)."'s attendance successfully set to ".$data['attendanceStatus'].".";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating attendance for player ".$this->getUserFullName($player->user).": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updateCoachAttendance(AttendanceStatusRequest $request, Training $training, Coach $coach): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->trainingService->updateCoachAttendanceStatus($data, $training, $coach);
            $message = "Coach ".$this->getUserFullName($coach->user)."'s attendance successfully set to ".$data['attendanceStatus'].".";
            return ApiResponse::success(message:  $message);
        } catch (Exception $e){
            $message = "Error while updating attendance for coach ".$this->getUserFullName($coach->user).": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function createNote(ScheduleNoteRequest $request, Training $training): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->trainingService->createNote($data, $training);
            $message = "Note for this ".$training->eventType." session successfully created.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while creating a note for this session: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function editNote(Training $training, TrainingNote $note): JsonResponse
    {
        try {
            $message = "Note data successfully retrieved.";
            return ApiResponse::success($note, message:  $message);

        } catch (Exception $e){
            $message = "Error while retrieving note data: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updateNote(ScheduleNoteRequest $request, Training $training, TrainingNote $note): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->trainingService->updateNote($data, $training, $note);
            $message = "Note successfully updated.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating note data: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
    public function destroyNote(Training $training, TrainingNote $note): JsonResponse
    {
        try {
            $this->trainingService->destroyNote($training, $note);
            $message = "Note for this session successfully deleted.";
            return ApiResponse::success(message:  $message);
        } catch (Exception $e){
            $message = "Error while deleting note data: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Training $training): JsonResponse
    {
        try {
            $message = "Training session ".$training->eventName." successfully deleted.";
            $data = $this->trainingService->destroy($training, $this->getLoggedUser());
            return ApiResponse::success($data, message:  $message);

        } catch (Exception $e){
            $message = "Error while deleting training session ".$training->eventName."." . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
}
