<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceStatusRequest;
use App\Http\Requests\MatchScheduleRequest;
use App\Http\Requests\MatchScoreRequest;
use App\Http\Requests\MatchStatsRequest;
use App\Http\Requests\PlayerMatchStatsRequest;
use App\Http\Requests\ScheduleNoteRequest;
use App\Http\Requests\TrainingScheduleRequest;
use App\Http\Requests\UpdateMatchScheduleRequest;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\EventSchedule;
use App\Models\MatchScore;
use App\Models\Player;
use App\Models\ScheduleNote;
use App\Services\CompetitionService;
use App\Services\EventScheduleService;
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

        return view('pages.admins.academies.schedules.trainings.index', [
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

        return view('pages.admins.academies.schedules.matches.index', [
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
        return view('pages.admins.academies.schedules.trainings.create', [
            'teams' => $teams,
        ]);
    }

    public function createMatch()
    {
        return view('pages.admins.academies.schedules.matches.create', [
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

        $text = 'Training schedule successfully added!';
        Alert::success($text);
        return redirect()->route('training-schedules.index');
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
    public function showTraining(EventSchedule $schedule)
    {
        $data = $this->eventScheduleService->show($schedule);
        $players = $data['dataSchedule']->players;
        $coaches = $data['dataSchedule']->coaches;

        if ($this->isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $data = $this->eventScheduleService->show($schedule, $player);
        }

        return view('pages.admins.academies.schedules.trainings.detail', [
            'data' => $data,
            'players' => $players,
            'coaches' => $coaches,
        ]);
    }

    public function showMatch(EventSchedule $schedule)
    {
        $data = $this->eventScheduleService->show($schedule);
        $homePlayers = $schedule->players()->where('teamId', $schedule->teams[0]->id)->get();
        $awayPlayers = null;
        $homeCoaches = $schedule->coaches()->where('teamId', $schedule->teams[0]->id)->get();
        $awayCoaches = null;
        $homeTeamMatchScorers = $this->eventScheduleService->getmatchScorers($schedule, $schedule->teams[0]);
        $awayTeamMatchScorers = null;
        $homeTeamAttendance = $this->eventScheduleService->eventAttendance($schedule, $schedule->teams[0]);
        $awayTeamAttendance = null;
        
        if ($schedule->matchType == 'Internal Match'){
            $awayPlayers = $schedule->players()->where('teamId', $schedule->teams[1]->id)->get();
            $awayCoaches = $schedule->coaches()->where('teamId', $schedule->teams[1]->id)->get();
            $awayTeamMatchScorers = $this->eventScheduleService->getmatchScorers($schedule, $schedule->teams[1]);
            $awayTeamAttendance = $this->eventScheduleService->eventAttendance($schedule, $schedule->teams[1]);
        }
    
        if ($this->isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $data = $this->eventScheduleService->show($schedule, $player);
        }

        return view('pages.admins.academies.schedules.matches.detail', [
            'data' => $data,
            'schedule' => $schedule,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
            'homeCoaches' => $homeCoaches,
            'awayCoaches' => $awayCoaches,
            'homeTeamMatchScorers' => $homeTeamMatchScorers,
            'awayTeamMatchScorers' => $awayTeamMatchScorers,
            'homeTeamAttendance' => $homeTeamAttendance,
            'awayTeamAttendance' => $awayTeamAttendance,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editTraining(EventSchedule $schedule)
    {
        if (isAllAdmin()){
            $teams = $this->eventScheduleService->createTraining();
        } elseif (isCoach()){
            $coach = $this->getLoggedCoachUser();
            $teams = $this->eventScheduleService->createTraining($coach);
        }
        return view('pages.admins.academies.schedules.trainings.edit', [
            'teams' => $teams,
            'data' => $schedule
        ]);
    }

    public function editMatch(EventSchedule $schedule)
    {
        return view('pages.admins.academies.schedules.matches.edit', [
            'competitions' => $this->competitionService->getActiveCompetition(),
            'data' => $schedule
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateTraining(TrainingScheduleRequest $request, EventSchedule $schedule)
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        $this->eventScheduleService->updateTraining($data, $schedule, $loggedUser);

        $text = 'Schedule successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.index');
    }

    public function updateMatch(UpdateMatchScheduleRequest $request, EventSchedule $schedule)
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        $this->eventScheduleService->updateMatch($data, $schedule, $loggedUser);

        $text = 'Match Schedule successfully updated!';
        Alert::success($text);
        return redirect()->route('match-schedules.index');
    }

    public function status(EventSchedule $schedule, $status)
    {
        try {
            $this->eventScheduleService->setStatus($schedule, $status);
            return response()->json(['message' =>  $schedule->eventType.' session status successfully mark to '.$status.'!']);

        } catch (Exception $e) {
            Log::error('Error marking '.$schedule->eventType.' session as '.$status.': ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while marking the competition '.$schedule->eventType.' session as '.$status.'.'], 500);
        }
    }

    public function scheduled(EventSchedule $schedule)
    {
        return $this->status($schedule, 'Scheduled');
    }

    public function ongoing(EventSchedule $schedule)
    {
        return $this->status($schedule, 'Ongoing');
    }
    public function completed(EventSchedule $schedule)
    {
        return $this->status($schedule, 'Completed');
    }
    public function cancelled(EventSchedule $schedule)
    {
        return $this->status($schedule, 'Cancelled');
    }

    public function endMatch(EventSchedule $schedule)
    {
        $this->eventScheduleService->endMatch($schedule);

        $text = 'Match status successfully ended!';
        Alert::success($text);
        return redirect()->route('match-schedules.show', $schedule->id);
    }

    public function getPlayerAttendance(EventSchedule $schedule, Player $player){
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

    public function getCoachAttendance(EventSchedule $schedule, Coach $coach){
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

    public function updatePlayerAttendance(AttendanceStatusRequest $request, EventSchedule $schedule, Player $player): JsonResponse
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

    public function updateCoachAttendance(AttendanceStatusRequest $request, EventSchedule $schedule, Coach $coach)
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

    public function createNote(ScheduleNoteRequest $request, EventSchedule $schedule){
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        try {
            $this->eventScheduleService->createNote($data, $schedule, $loggedUser);
            $message = "Note for this session successfully created.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while creating a note for this session: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function editNote(EventSchedule $schedule, ScheduleNote $note)
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

    public function updateNote(ScheduleNoteRequest $request, EventSchedule $schedule, ScheduleNote $note){
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
    public function destroyNote(EventSchedule $schedule, ScheduleNote $note)
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

    public function getEventPLayers(Request $request, EventSchedule $schedule)
    {
        $team = $request->input('team');
        $exceptPlayerId = $request->input('exceptPlayerId');

        if ($team == 'homeTeam') {
            $teamData = $schedule->teams[0];

        } else {
            $teamData = $schedule->teams[1];
        }
        $players = $schedule->players()
            ->with('user', 'position')
            ->whereRelation('teams', 'teamId', $teamData->id)
            ->where('players.id', '!=', $exceptPlayerId)
            ->get();

        $responseData = [
            'players' => $players,
            'team' => $teamData
        ];

        return ApiResponse::success($responseData, message:  "Successfully retrieved player data");
    }

    public function storeMatchScorer(MatchScoreRequest $request, EventSchedule $schedule){
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

    public function destroyMatchScorer(EventSchedule $schedule, MatchScore $scorer){
        try {
            if ($scorer->teamId == $schedule->teams[1]->id) {
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

    public function updateMatchStats(MatchStatsRequest $request, EventSchedule $schedule)
    {
        $data = $request->validated();

        try {
            $this->eventScheduleService->updateMatchStats($data, $schedule);
            $message = "Match stats successfully updated.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating match stats:" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function storeOwnGoal(MatchScoreRequest $request, EventSchedule $schedule){
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

    public function destroyOwnGoal(EventSchedule $schedule, MatchScore $scorer){
        try {
            if ($scorer->teamId == $schedule->teams[1]->id) {
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

    public function indexPlayerMatchStats(Request $request, EventSchedule $schedule)
    {
        $teamId = $request->input('teamId');
        return $this->eventScheduleService->dataTablesPlayerStats($schedule, $teamId);
    }

    public function getPlayerStats(EventSchedule $schedule, Player $player){
        $player = $this->eventScheduleService->getPlayerStats($schedule, $player);
        return ApiResponse::success($player, message:  "Successfully retrieved player stats");
    }

    public function updatePlayerStats(PlayerMatchStatsRequest $request, EventSchedule $schedule, Player $player)
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
    public function destroy(EventSchedule $schedule)
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
