<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceStatusRequest;
use App\Http\Requests\CompetitionMatchRequest;
use App\Http\Requests\ExternalTeamScoreRequest;
use App\Http\Requests\MatchScheduleRequest;
use App\Http\Requests\MatchScoreRequest;
use App\Http\Requests\MatchStatsRequest;
use App\Http\Requests\PlayerMatchStatsRequest;
use App\Http\Requests\ScheduleNoteRequest;
use App\Http\Requests\TrainingScheduleRequest;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\MatchScore;
use App\Models\Player;
use App\Models\MatchNote;
use App\Models\Team;
use App\Services\CompetitionService;
use App\Services\EventScheduleService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        if ($this->isAllAdmin()){
            $events = $this->eventScheduleService->trainingCalendar();
            $tableRoute = url()->route('admin.training-schedules.index');

        } elseif ($this->isCoach()){
            $coach = $this->getLoggedCoachUser();
            $events = $this->eventScheduleService->coachTeamsTrainingCalendar($coach);
            $tableRoute = url()->route('coach.training-schedules.index');

        } elseif ($this->isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $events = $this->eventScheduleService->playerTeamsTrainingCalendar($player);
            $tableRoute = url()->route('player.training-schedules.index');
        }

        return view('pages.academies.schedules.trainings.index', [
            'events' => $events,
            'tableRoute' => $tableRoute,
        ]);
    }

    public function adminIndexTraining()
    {
        return $this->eventScheduleService->dataTablesTraining();
    }

    public function coachIndexTraining()
    {
        $coach = $this->getLoggedCoachUser();
        return $this->eventScheduleService->coachTeamsDataTablesTraining($coach);
    }
    public function playerIndexTraining()
    {
        $player = $this->getLoggedPLayerUser();
        return $this->eventScheduleService->playerTeamsDataTablesTraining($player);
    }

    public function indexMatch()
    {
        if ($this->isAllAdmin()){
            $events = $this->eventScheduleService->matchCalendar();
            $tableRoute = url()->route('admin.match-schedules.index');

        } elseif ($this->isCoach()){
            $coach = $this->getLoggedCoachUser();
            $events = $this->eventScheduleService->coachTeamsMatchCalendar($coach);
            $tableRoute = url()->route('coach.match-schedules.index');

        } elseif ($this->isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $events = $this->eventScheduleService->playerTeamsMatchCalendar($player);
            $tableRoute = url()->route('player.match-schedules.index');
        }

        return view('pages.academies.schedules.matches.index', [
            'events' => $events,
            'tableRoute' => $tableRoute,
        ]);
    }

    public function adminIndexMatch()
    {
        return $this->eventScheduleService->dataTablesMatch();
    }
    public function coachIndexMatch()
    {
        $coach = $this->getLoggedCoachUser();
            return $this->eventScheduleService->coachTeamsDataTablesMatch($coach);
    }
    public function playerIndexMatch()
    {
        $player = $this->getLoggedPLayerUser();
        return $this->eventScheduleService->playerTeamsDataTablesMatch($player);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createTraining()
    {
        if (isAllAdmin()){
            $teams = $this->eventScheduleService->createTraining();
        } elseif (isCoach()){
            $coach = $this->getLoggedCoachUser();
            $teams = $this->eventScheduleService->createTraining($coach);
        }
        return view('pages.academies.schedules.trainings.create', [
            'teams' => $teams,
        ]);
    }

    public function createMatch()
    {
        return view('pages.academies.schedules.matches.create', [
            'competitions' => $this->competitionService->getActiveCompetition(),
        ]);
    }
    public function getInternalMatchTeams(Request $request)
    {
        $exceptTeamId = $request->input('exceptTeamId');
        $data = $this->eventScheduleService->internalMatchTeams($exceptTeamId);
        return ApiResponse::success($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeTraining(TrainingScheduleRequest $request)
    {
        $data = $request->validated();
        $userId = $this->getLoggedUserId();
        $this->eventScheduleService->storeTraining($data, $userId);
        return ApiResponse::success(message: 'Training schedule successfully added!');
    }

    public function storeMatch(MatchScheduleRequest $request)
    {
        $data = $request->validated();
        $userId = $this->getLoggedUserId();
        $this->eventScheduleService->storeMatch($data, $userId);

        $text = 'Match schedule successfully added!';
        Alert::success($text);
        return redirect()->route('match-schedules.index');
    }

    /**
     * Display the specified resource.
     */
    public function showTraining(MatchModel $schedule)
    {
        $data = $this->eventScheduleService->show($schedule);
        $players = $schedule->players;
        $coaches = $schedule->coaches;
        $attendance = $this->eventScheduleService->eventAttendance($schedule, $schedule->teams[0]);

        if ($this->isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $data = $this->eventScheduleService->show($schedule, $player);
        }

        return view('pages.academies.schedules.trainings.detail', [
            'data' => $data,
            'schedule' => $schedule,
            'players' => $players,
            'coaches' => $coaches,
            'attendance' => $attendance
        ]);
    }

    public function showMatch(MatchModel $schedule)
    {
        $data = $this->eventScheduleService->show($schedule);

        $homeTeam = $this->eventScheduleService->homeTeamMatch($schedule);
        $homePlayers = $this->eventScheduleService->homeTeamPlayers($schedule);
        $homeCoaches = $this->eventScheduleService->homeTeamCoaches($schedule);
        $homeTeamMatchScorers = $this->eventScheduleService->homeTeamMatchScorers($schedule);
        $homeTeamAttendance = $this->eventScheduleService->eventAttendance($schedule, $schedule->homeTeam);
        $homeTeamNotes = $this->eventScheduleService->homeTeamNotes($schedule);

        $awayTeam = $this->eventScheduleService->awayTeamMatch($schedule);
        $awayPlayers = null;
        $awayCoaches = null;
        $awayTeamMatchScorers = null;
        $awayTeamAttendance = null;
        $awayTeamNotes = null;
        $userTeams = null;

        if ($schedule->matchType == 'Internal Match'){
            $awayPlayers = $this->eventScheduleService->awayTeamPlayers($schedule);
            $awayCoaches = $this->eventScheduleService->awayTeamCoaches($schedule);
            $awayTeamMatchScorers = $this->eventScheduleService->awayTeamMatchScorers($schedule);
            $awayTeamAttendance = $this->eventScheduleService->eventAttendance($schedule, $schedule->awayTeam);
            $awayTeamNotes = $this->eventScheduleService->awayTeamNotes($schedule);

            if ($this->isCoach()) {
                $coach = $this->getLoggedCoachUser();
                $userTeams = collect($coach->teams)->pluck('id')->all();

            } elseif ($this->isPlayer()) {
                $player = $this->getLoggedPLayerUser();
                $userTeams = collect($player->teams)->pluck('id')->all();
            } else {
                $userTeams = collect(Team::all())->pluck('id')->all();
            }
        }

        if ($this->isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $data = $this->eventScheduleService->show($schedule, $player);
        }

        return view('pages.academies.schedules.matches.detail', [
            'data' => $data,
            'schedule' => $schedule,
            'homeTeam' => $homeTeam,
            'homePlayers' => $homePlayers,
            'homeCoaches' => $homeCoaches,
            'homeTeamMatchScorers' => $homeTeamMatchScorers,
            'homeTeamAttendance' => $homeTeamAttendance,
            'homeTeamNotes' => $homeTeamNotes,
            'awayTeam' => $awayTeam,
            'awayCoaches' => $awayCoaches,
            'awayPlayers' => $awayPlayers,
            'awayTeamMatchScorers' => $awayTeamMatchScorers,
            'awayTeamAttendance' => $awayTeamAttendance,
            'awayTeamNotes' => $awayTeamNotes,
            'userTeams' => $userTeams,
        ]);
    }

    public function getMatchDetail(MatchModel $schedule)
    {
        $data = $this->eventScheduleService->getMatchDetail($schedule);
        return ApiResponse::success($data);
    }

    public function getTeamMatchStats(Request $request, MatchModel $schedule)
    {
        $team = $request->input('team');
        $data = $this->eventScheduleService->getTeamMatchStats($schedule, $team);
        return ApiResponse::success($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editTraining(MatchModel $schedule)
    {
        $data = [
            'schedule' => $schedule,
            'teamId' => $schedule->teams[0]->id
        ];
        return ApiResponse::success($data);
    }

    public function editMatch(MatchModel $schedule)
    {
        return view('pages.academies.schedules.matches.edit', [
            'competitions' => $this->competitionService->getActiveCompetition(),
            'data' => $schedule
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateTraining(TrainingScheduleRequest $request, MatchModel $schedule)
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        $this->eventScheduleService->updateTraining($data, $schedule, $loggedUser);
        return ApiResponse::success(message: 'Training session successfully updated!');
    }

    public function updateMatch(CompetitionMatchRequest $request, MatchModel $schedule)
    {
        $data = $request->validated();
        $this->eventScheduleService->updateMatch($data, $schedule);
        return ApiResponse::success(message: 'Match session successfully updated!');
    }

    public function status(MatchModel $schedule, $status)
    {
        try {
            $this->eventScheduleService->setStatus($schedule, $status);
            return ApiResponse::success(message: $schedule->eventType.' session status successfully mark to '.$status.'!');

        } catch (Exception $e) {
            Log::error('Error marking '.$schedule->eventType.' session as '.$status.': ' . $e->getMessage());
            return ApiResponse::error('An error occurred while marking the competition '.$schedule->eventType.' session as '.$status.'.');
        }
    }

    public function scheduled(MatchModel $schedule)
    {
        if ($schedule->startDatetime < Carbon::now()) {
            return ApiResponse::error("You cannot set the match session to scheduled because the match date has passed, please change the match start date to a future date.");
        } else {
            return $this->status($schedule, 'scheduled');
        }
    }

    public function ongoing(MatchModel $schedule)
    {
        return $this->status($schedule, 'ongoing');
    }
    public function completed(MatchModel $schedule)
    {
        return $this->status($schedule, 'completed');
    }
    public function cancelled(MatchModel $schedule)
    {
        return $this->status($schedule, 'cancelled');
    }

    public function endMatch(MatchModel $schedule)
    {
        $this->eventScheduleService->endMatch($schedule);

        $text = 'Match status successfully ended!';
        Alert::success($text);
        return redirect()->route('match-schedules.show', $schedule->id);
    }

    public function getPlayerAttendance(MatchModel $schedule, Player $player){
        try {
            $data = $this->eventScheduleService->getPlayerAttendance($schedule, $player);
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

    public function getCoachAttendance(MatchModel $schedule, Coach $coach){
        try {
            $data = $this->eventScheduleService->getCoachAttendance($schedule, $coach);
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

    public function updatePlayerAttendance(AttendanceStatusRequest $request, MatchModel $schedule, Player $player): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->eventScheduleService->updatePlayerAttendanceStatus($data, $schedule, $player);
            $message = "Player ".$this->getUserFullName($player->user)."'s attendance successfully set to ".$data['attendanceStatus'].".";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating attendance for player ".$this->getUserFullName($player->user).": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updateCoachAttendance(AttendanceStatusRequest $request, MatchModel $schedule, Coach $coach)
    {
        $data = $request->validated();
        try {
            $this->eventScheduleService->updateCoachAttendanceStatus($data, $schedule, $coach);
            $message = "Coach ".$this->getUserFullName($coach->user)."'s attendance successfully set to ".$data['attendanceStatus'].".";
            return ApiResponse::success(message:  $message);
        } catch (Exception $e){
            $message = "Error while updating attendance for coach ".$this->getUserFullName($coach->user).": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function createNote(ScheduleNoteRequest $request, MatchModel $schedule){
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        try {
            $this->eventScheduleService->createNote($data, $schedule, $loggedUser);
            $message = "Note for this ".$schedule->eventType." session successfully created.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while creating a note for this session: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function editNote(MatchModel $schedule, MatchNote $note)
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

    public function updateNote(ScheduleNoteRequest $request, MatchModel $schedule, MatchNote $note){
        $data = $request->validated();
        try {
            $this->eventScheduleService->updateNote($data, $schedule, $note, $this->getLoggedUser());
            $message = "Note successfully updated.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating note data: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
    public function destroyNote(MatchModel $schedule, MatchNote $note)
    {
        try {
            $this->eventScheduleService->destroyNote($schedule, $note, $this->getLoggedUser());
            $message = "Note for this session successfully deleted.";
            return ApiResponse::success(message:  $message);
        } catch (Exception $e){
            $message = "Error while deleting note data: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function getCompetitionTeam(Competition $competition){
        $groups = $competition->groups()->with('teams')->get();
        $teams = [];
        $opponentTeams = [];
        foreach ($groups as $group){
            $teams[] = $group->teams()->where('teamSide', 'Academy Team')->get();
            $opponentTeams[] = $group->teams()->where('teamSide', 'Opponent Team')->get();
        }
        $data = [
                'teams' => $teams,
                'opponentTeams' => $opponentTeams,
            ];
        return ApiResponse::success($data, message:  "Successfully retrieved competition team data");
    }

    public function getFriendlyMatchTeam(){
        $data = $this->eventScheduleService->getFriendlyMatchTeam();
        $data = [
            'teams' => $data['teams'],
            'opponentTeams' => $data['opponentTeams'],
        ];
        return ApiResponse::success($data, message:  "Successfully retrieved friendly match team data");
    }

    public function getEventPLayers(Request $request, MatchModel $schedule)
    {
        $team = $request->input('team');
        $exceptPlayerId = $request->input('exceptPlayerId');

        $data = $this->eventScheduleService->getEventPLayers($schedule, $team, $exceptPlayerId);

        return ApiResponse::success($data, message:  "Successfully retrieved player data");
    }

    public function storeMatchScorer(MatchScoreRequest $request, MatchModel $schedule){
        $data = $request->validated();
        try {
            if ($data['dataTeam'] == 'awayTeam') {
                $scorer = $this->eventScheduleService->storeMatchScorer($data, $schedule, true);
            } else {
                $scorer = $this->eventScheduleService->storeMatchScorer($data, $schedule);
            }

            $message = $this->getUserFullName($scorer->player->user)."'s score successfully added.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while adding match scorer ".$this->getUserFullName($scorer->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function destroyMatchScorer(MatchModel $schedule, MatchScore $scorer){
        try {
            if ($scorer->teamId == $schedule->awayTeamId) {
                $this->eventScheduleService->destroyMatchScorer($schedule, $scorer, true);
            } else {
                $this->eventScheduleService->destroyMatchScorer($schedule, $scorer);
            }

            $message = $this->getUserFullName($scorer->player->user)."'s score successfully deleted.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while deleting match scorer ".$this->getUserFullName($scorer->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updateMatchStats(MatchStatsRequest $request, MatchModel $schedule): JsonResponse
    {
        $data = $request->validated();
        $this->eventScheduleService->updateMatchStats($data, $schedule);
        return ApiResponse::success(message:  "Team match stats successfully updated.");
    }

    public function updateExternalTeamScore(ExternalTeamScoreRequest $request, MatchModel $schedule): JsonResponse
    {
        $data = $request->validated();
        $this->eventScheduleService->updateExternalTeamScore($data, $schedule);
        return ApiResponse::success(message:  "Team ".$schedule->externalTeam->teamName." score successfully updated.");
    }

    public function storeOwnGoal(MatchScoreRequest $request, MatchModel $schedule){
        $data = $request->validated();
        try {
            if ($data['dataTeam'] == 'awayTeam') {
                $ownGoal = $this->eventScheduleService->storeOwnGoal($data, $schedule, true);
            } else {
                $ownGoal = $this->eventScheduleService->storeOwnGoal($data, $schedule);
            }
            $message = $this->getUserFullName($ownGoal->player->user)."'s own goal successfully added.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while adding own goal ".$this->getUserFullName($ownGoal->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function destroyOwnGoal(MatchModel $schedule, MatchScore $scorer){
        try {
            if ($scorer->teamId == $schedule->awayTeamId) {
                $this->eventScheduleService->destroyOwnGoal($schedule, $scorer, true);
            } else {
                $this->eventScheduleService->destroyOwnGoal($schedule, $scorer);
            }
            $message = $this->getUserFullName($scorer->player->user)."'s own goal successfully deleted.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while deleting own goal ".$this->getUserFullName($scorer->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function indexPlayerMatchStats(Request $request, MatchModel $schedule)
    {
        $teamId = $request->input('teamId');
        return $this->eventScheduleService->dataTablesPlayerStats($schedule, $teamId);
    }

    public function getPlayerStats(MatchModel $schedule, Player $player){
        $player = $this->eventScheduleService->getPlayerStats($schedule, $player);
        return ApiResponse::success($player, message:  "Successfully retrieved player stats");
    }

    public function updatePlayerStats(PlayerMatchStatsRequest $request, MatchModel $schedule, Player $player)
    {
        $data = $request->validated();
        try {
            $this->eventScheduleService->updatePlayerStats($data, $schedule, $player);
            $message = "Player ".$this->getUserFullName($player->user)." stats successfully updated.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating player ".$this->getUserFullName($player->user)." stats:" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MatchModel $schedule)
    {
        try {
            if ($schedule->eventType == 'Training') {
                $message = "Training session ".$schedule->eventName." successfully deleted.";
            } else {
                $message = "Match ".$schedule->teams[0]->teamName." Vs. ".$schedule->teams[1]->teamName." successfully deleted.";
            }
            $data = $this->eventScheduleService->destroy($schedule, $this->getLoggedUser());
            return ApiResponse::success($data, message:  $message);

        } catch (Exception $e){
            if ($schedule->eventType == 'Training') {
                $message = "Error while deleting training session ".$schedule->eventName."." . $e->getMessage();
            } else {
                $message = "Error while deleting match ".$schedule->teams[0]->teamName." Vs. ".$schedule->teams[1]->teamName." : " . $e->getMessage();
            }
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
}
