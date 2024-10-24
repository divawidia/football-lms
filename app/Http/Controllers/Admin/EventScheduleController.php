<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceStatusRequest;
use App\Http\Requests\MatchScheduleRequest;
use App\Http\Requests\MatchScoreRequest;
use App\Http\Requests\MatchStatsRequest;
use App\Http\Requests\PlayerMatchStatsRequest;
use App\Http\Requests\ScheduleNoteRequest;
use App\Http\Requests\TrainingScheduleRequest;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\EventSchedule;
use App\Models\MatchScore;
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

    public function coachIndexTraining()
    {
        $coach = $this->getLoggedCoachUser();
        if (request()->ajax()){
            return $this->eventScheduleService->coachTeamsDataTablesTraining($coach);
        }

        $events = $this->eventScheduleService->coachTeamsTrainingCalendar($coach);

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

    public function coachIndexMatch()
    {
        $coach = $this->getLoggedCoachUser();
        if (request()->ajax()){
            return $this->eventScheduleService->coachTeamsDataTablesMatch($coach);
        }

        $events = $this->eventScheduleService->coachTeamsMatchCalendar($coach);

        return view('pages.admins.academies.schedules.matches.index', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createTraining()
    {
        if (Auth::user()->hasRole('admin')){
            $view = view('pages.admins.academies.schedules.trainings.create', [
                'teams' => $this->eventScheduleService->getAcademyTeams(),
            ]);
        } elseif (Auth::user()->hasRole('coach')){
            $view = view('pages.admins.academies.schedules.trainings.create', [
                'teams' => $this->eventScheduleService->coachManagedTeams($this->getLoggedCoachUser()),
            ]);
        }
        return $view;
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

        if (Auth::user()->hasRole('admin')){
            $redirect = redirect()->route('training-schedules.index');
        } elseif (Auth::user()->hasRole('coach')){
            $redirect = redirect()->route('coach.training-schedules.index');
        }
        return $redirect;
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
        $data = $this->eventScheduleService->show($schedule);
        $players = $data['dataSchedule']->players()->paginate(6);
        $coaches = $data['dataSchedule']->coaches;

        return view('pages.admins.academies.schedules.trainings.detail', [
            'data' => $data,
            'players' => $players,
            'coaches' => $coaches
        ]);
    }

    public function showMatch(EventSchedule $schedule)
    {
        $data = $this->eventScheduleService->show($schedule);
        $players = $data['dataSchedule']->players()->paginate(6);
        $coaches = $data['dataSchedule']->coaches;

        return view('pages.admins.academies.schedules.matches.detail', [
            'data' => $data,
            'players' => $players,
            'coaches' => $coaches
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

    public function editMatch(EventSchedule $schedule)
    {
        return view('pages.admins.academies.schedules.matches.edit', [
            'competitions' => $this->competitionService->index(),
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

    public function updateMatch(MatchScheduleRequest $request, EventSchedule $schedule)
    {
        $data = $request->validated();
        $this->eventScheduleService->updateMatch($data, $schedule);

        $text = 'Match Schedule successfully updated!';
        Alert::success($text);
        return redirect()->route('match-schedules.index');
    }

    public function activateTraining(EventSchedule $schedule)
    {
        $this->eventScheduleService->activate($schedule);

        $text = 'Schedule status successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.show', $schedule->id);
    }

    public function deactivateTraining(EventSchedule $schedule)
    {
        $this->eventScheduleService->deactivate($schedule);

        $text = 'Schedule status successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.show', $schedule->id);
    }

    public function activateMatch(EventSchedule $schedule)
    {
        $this->eventScheduleService->activate($schedule);

        $text = 'Match status successfully updated!';
        Alert::success($text);
        return redirect()->route('match-schedules.show', $schedule->id);
    }

    public function endMatch(EventSchedule $schedule)
    {
        $this->eventScheduleService->endMatch($schedule);

        $text = 'Match status successfully ended!';
        Alert::success($text);
        return redirect()->route('match-schedules.show', $schedule->id);
    }

    public function getPlayerAttendance(EventSchedule $schedule, Player $player){
        $data = $this->eventScheduleService->getPlayerAttendance($schedule, $player);

        if (request()->ajax()) {
            if ($data) {
                return response()->json([
                    'status' => '200',
                    'data' => [
                        'user' => $data->user,
                        'playerAttendance'=>$data->pivot
                    ]
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
                    'data' => [
                        'user' => $data->user,
                        'coachAttendance'=>$data->pivot
                        ]
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

    public function getAssistPlayer(EventSchedule $schedule, Player $player){
        $players = $schedule->players()->with('user', 'position')->where('players.id', '!=', $player->id)->get();
        return response()->json([
            'status' => '200',
            'data' => $players,
            'message' => 'Success'
        ]);
    }

    public function storeMatchScorer(MatchScoreRequest $request, EventSchedule $schedule){
        $data = $request->validated();
        $note = $this->eventScheduleService->storeMatchScorer($data, $schedule);
        if (request()->ajax()) {
            return response()->json([
                'status' => '200',
                'data' => $note,
                'message' => 'Success'
            ]);
        }
    }

    public function destroyMatchScorer(EventSchedule $schedule, MatchScore $scorer){
        $matchScorer = $this->eventScheduleService->destroyMatchScorer($schedule, $scorer);
        return response()->json([
            'status' => '200',
            'data' => $matchScorer,
            'message' => 'Success'
        ]);
    }

    public function updateMatchStats(MatchStatsRequest $request, EventSchedule $schedule)
    {
        $data = $request->validated();
        $matchStats = $this->eventScheduleService->updateMatchStats($data, $schedule);
        if (request()->ajax()) {
            return response()->json([
                'status' => '200',
                'data' => $matchStats,
                'message' => 'Success'
            ]);
        }
    }

    public function storeOwnGoal(MatchScoreRequest $request, EventSchedule $schedule){
        $data = $request->validated();
        $ownGoal = $this->eventScheduleService->storeOwnGoal($data, $schedule);
        if (request()->ajax()) {
            return response()->json([
                'status' => '200',
                'data' => $ownGoal,
                'message' => 'Success'
            ]);
        }
    }

    public function destroyOwnGoal(EventSchedule $schedule, MatchScore $scorer){
        $ownGoal = $this->eventScheduleService->destroyOwnGoal($schedule, $scorer);
        return response()->json([
            'status' => '200',
            'data' => $ownGoal,
            'message' => 'Success'
        ]);
    }

    public function indexPlayerMatchStats(EventSchedule $schedule)
    {
        if (request()->ajax()){
            return $this->eventScheduleService->dataTablesPlayerStats($schedule);
        }
    }

    public function getPlayerStats(EventSchedule $schedule, Player $player){
        $player = $this->eventScheduleService->getPlayerStats($schedule, $player);
        return response()->json([
            'status' => '200',
            'data' => $player,
            'message' => 'Success'
        ]);
    }

    public function updatePlayerStats(PlayerMatchStatsRequest $request, EventSchedule $schedule, Player $player)
    {
        $data = $request->validated();
        $playerStats = $this->eventScheduleService->updatePlayerStats($data, $schedule, $player);
        if (request()->ajax()) {
            return response()->json([
                'status' => '200',
                'data' => $playerStats,
                'message' => 'Success'
            ]);
        }
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
