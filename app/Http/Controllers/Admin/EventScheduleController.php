<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceStatusRequest;
use App\Http\Requests\MatchScheduleRequest;
use App\Http\Requests\ScheduleNoteRequest;
use App\Http\Requests\TrainingScheduleRequest;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\ScheduleNote;
use App\Services\CompetitionService;
use App\Services\EventScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class EventScheduleController extends Controller
{
    private EventScheduleService $eventScheduleService;
    private CompetitionService $competitionService;
    public function __construct(EventScheduleService $eventScheduleService, CompetitionService $competitionService)
    {
        $this->eventScheduleService = $eventScheduleService;
        $this->competitionService = $competitionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function indexTraining()
    {
        if (request()->ajax()){
            return $this->eventScheduleService->dataTablesTraining();
        }

        $events = $this->eventScheduleService->trainingCalendar();

        return view('pages.admins.academies.schedules.trainings.index', [
            'events' => $events
        ]);
    }

    public function indexMatch()
    {
        if (request()->ajax()){
            return $this->eventScheduleService->dataTablesMatch();
        }

        $events = $this->eventScheduleService->matchCalendar();

        return view('pages.admins.academies.schedules.matches.index', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createTraining()
    {
        return view('pages.admins.academies.schedules.trainings.create', [
            'teams' => $this->eventScheduleService->getAcademyTeams(),
        ]);
    }

    public function createMatch()
    {
        return view('pages.admins.academies.schedules.matches.create', [
            'competitions' => $this->competitionService->index(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeTraining(TrainingScheduleRequest $request)
    {
        $data = $request->validated();
        $userId = Auth::user()->id;
        $this->eventScheduleService->storeTraining($data, $userId);

        $text = 'Training schedule successfully added!';
        Alert::success($text);
        return redirect()->route('training-schedules.index');
    }

    public function storeMatch(MatchScheduleRequest $request)
    {
        $data = $request->validated();
        $userId = Auth::user()->id;
        $this->eventScheduleService->storeMatch($data, $userId);

        $text = 'Match schedule successfully added!';
        Alert::success($text);
        return redirect()->route('match-schedules.index');
    }

    /**
     * Display the specified resource.
     */
    public function showTraining(EventSchedule $schedule)
    {
        return view('pages.admins.academies.schedules.trainings.detail', [
            'data' => $this->eventScheduleService->show($schedule),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editTraining(EventSchedule $schedule)
    {
        return view('pages.admins.academies.schedules.trainings.edit', [
            'teams' => $this->eventScheduleService->getAcademyTeams(),
            'data' => $schedule
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateTraining(TrainingScheduleRequest $request, EventSchedule $schedule)
    {
        $data = $request->validated();
        $this->eventScheduleService->updateTraining($data, $schedule);

        $text = 'Schedule successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.index');
    }
    public function activate(EventSchedule $schedule)
    {
        $this->eventScheduleService->activate($schedule);

        $text = 'Schedule status successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.show', $schedule->id);
    }

    public function deactivate(EventSchedule $schedule)
    {
        $this->eventScheduleService->deactivate($schedule);

        $text = 'Schedule status successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.show', $schedule->id);
    }

    public function getPlayerAttendance(EventSchedule $schedule, Player $player){
        $data = $this->eventScheduleService->getPlayerAttendance($schedule, $player);

        if (request()->ajax()) {
            if ($data) {
                return response()->json([
                    'status' => '200',
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'status' => '404',
                    'message' => 'Data not found!'
                ]);
            }
        } else {
            abort(404);
        }
    }

    public function getCoachAttendance(EventSchedule $schedule, Coach $coach){
        $data = $this->eventScheduleService->getCoachAttendance($schedule, $coach);

        if (request()->ajax()) {
            if ($data) {
                return response()->json([
                    'status' => '200',
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'status' => '404',
                    'message' => 'Data not found!'
                ]);
            }
        } else {
            abort(404);
        }
    }

    public function updatePlayerAttendance(AttendanceStatusRequest $request, EventSchedule $schedule, Player $player): JsonResponse
    {
        $data = $request->validated();
        $attendance = $this->eventScheduleService->updatePlayerAttendanceStatus($data, $schedule, $player);
        return response()->json($attendance);
    }

    public function updateCoachAttendance(AttendanceStatusRequest $request, EventSchedule $schedule, Coach $coach)
    {
        $data = $request->validated();
        $attendance = $this->eventScheduleService->updateCoachAttendanceStatus($data, $schedule, $coach);

        return response()->json($attendance);
    }

    public function createNote(ScheduleNoteRequest $request, EventSchedule $schedule){
        $data = $request->validated();
        $note = $this->eventScheduleService->createNote($data, $schedule);
        if (request()->ajax()) {
            return response()->json([
                'status' => '200',
                'data' => $note,
                'message' => 'Success'
            ]);
        }
    }

    public function editNote(EventSchedule $schedule, ScheduleNote $note)
    {
        try {
            return response()->json([
                'status' => '200',
                'data' => $note,
                'message' => 'Success'
            ]);
        }catch (\Throwable $throwable){
            return response()->json([
                'status' => '400',
                'data' => $throwable,
                'message' => 'Error'
            ]);
        }
    }

    public function updateNote(ScheduleNoteRequest $request, EventSchedule $schedule, ScheduleNote $note){
        $data = $request->validated();
        $note = $this->eventScheduleService->updateNote($data, $schedule, $note);
        return response()->json([
            'status' => '200',
            'data' => $note,
            'message' => 'Success'
        ]);
    }
    public function destroyNote(EventSchedule $schedule, ScheduleNote $note)
    {
        $this->eventScheduleService->destroyNote($schedule, $note);

        return response()->json(['success' => true]);
    }

    public function getCompetitionTeam(Competition $competition){
        $groups = $competition->groups()->with('teams')->get();
        $teams = [];
        $opponentTeams = [];
        foreach ($groups as $group){
            $teams[] = $group->teams()->where('teamSide', 'Academy Team')->get();
            $opponentTeams[] = $group->teams()->where('teamSide', 'Opponent Team')->get();
        }
        return response()->json([
            'status' => '200',
            'data' => [
                'teams' => $teams,
                'opponentTeams' => $opponentTeams,
                ],
            'message' => 'Success'
        ]);
    }

    public function getFriendlyMatchTeam(){
        $teams = $this->competitionService->getTeams();
        $opponentTeams = $this->competitionService->getOpponentTeams();
        return response()->json([
            'status' => '200',
            'data' => [
                'teams' => $teams,
                'opponentTeams' => $opponentTeams,
            ],
            'message' => 'Success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventSchedule $schedule)
    {
        $this->eventScheduleService->destroy($schedule);

        return response()->json(['success' => true]);
    }
}
