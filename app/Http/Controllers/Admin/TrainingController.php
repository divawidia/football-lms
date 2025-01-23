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
    public function indexTraining()
    {
        if ($this->isAllAdmin()){
            $events = $this->trainingService->trainingCalendar();
            $tableRoute = route('admin.training-schedules.index');

        } elseif ($this->isCoach()){
            $coach = $this->getLoggedCoachUser();
            $events = $this->trainingService->coachTeamsTrainingCalendar($coach);
            $tableRoute = route('coach.training-schedules.index');

        } else {
            $player = $this->getLoggedPLayerUser();
            $events = $this->trainingService->playerTeamsTrainingCalendar($player);
            $tableRoute = route('player.training-schedules.index');
        }

        return view('pages.academies.schedules.trainings.index', [
            'events' => $events,
            'tableRoute' => $tableRoute,
        ]);
    }

    public function adminIndexTraining()
    {
        return $this->trainingService->dataTablesTraining();
    }

    public function coachIndexTraining()
    {
        $coach = $this->getLoggedCoachUser();
        return $this->trainingService->coachTeamsDataTablesTraining($coach);
    }
    public function playerIndexTraining()
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
    public function store(TrainingScheduleRequest $request)
    {
        $data = $request->validated();
        $userId = $this->getLoggedUserId();
        $this->trainingService->storeTraining($data, $userId);
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
            'totalParticipant' => $this->trainingService->totalParticipant($training),
            'totalAttend' => $this->trainingService->totalAttend($training),
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
    public function edit(Training $training)
    {
        $data = [
            'schedule' => $training,
            'teamId' => $training->teamId
        ];
        return ApiResponse::success($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrainingScheduleRequest $request, Training $training)
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        $this->trainingService->updateTraining($data, $training, $loggedUser);
        return ApiResponse::success(message: 'Training session successfully updated!');
    }

    public function status(Training $training, $status)
    {
        try {
            $this->trainingService->setStatus($training, $status);
            return ApiResponse::success(message: $training->eventType.' session status successfully mark to '.$status.'!');

        } catch (Exception $e) {
            Log::error('Error marking '.$training->eventType.' session as '.$status.': ' . $e->getMessage());
            return ApiResponse::error('An error occurred while marking the competition '.$training->eventType.' session as '.$status.'.');
        }
    }

    public function scheduled(Training $training)
    {
        if ($training->startDatetime < Carbon::now()) {
            return ApiResponse::error("You cannot set the match session to scheduled because the match date has passed, please change the match start date to a future date.");
        } else {
            return $this->status($training, 'scheduled');
        }
    }

    public function ongoing(Training $training)
    {
        return $this->status($training, 'ongoing');
    }
    public function completed(Training $training)
    {
        return $this->status($training, 'completed');
    }
    public function cancelled(Training $training)
    {
        return $this->status($training, 'cancelled');
    }

    public function getPlayerAttendance(Training $training, Player $player){
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

    public function getCoachAttendance(Training $training, Coach $coach){
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

    public function updateCoachAttendance(AttendanceStatusRequest $request, Training $training, Coach $coach)
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

    public function createNote(ScheduleNoteRequest $request, Training $training){
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        try {
            $this->trainingService->createNote($data, $training, $loggedUser);
            $message = "Note for this ".$training->eventType." session successfully created.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while creating a note for this session: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function editNote(Training $training, TrainingNote $note)
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

    public function updateNote(ScheduleNoteRequest $request, Training $training, TrainingNote $note){
        $data = $request->validated();
        try {
            $this->trainingService->updateNote($data, $training, $note, $this->getLoggedUser());
            $message = "Note successfully updated.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating note data: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
    public function destroyNote(Training $training, TrainingNote $note)
    {
        try {
            $this->trainingService->destroyNote($training, $note, $this->getLoggedUser());
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
    public function destroy(Training $training)
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
