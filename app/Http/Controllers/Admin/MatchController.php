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
use App\Models\Coach;
use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\MatchScore;
use App\Models\Player;
use App\Models\MatchNote;
use App\Models\Team;
use App\Services\CompetitionService;
use App\Services\MatchService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class MatchController extends Controller
{
    private MatchService $matchService;
    private CompetitionService $competitionService;
    public function __construct(
        MatchService $matchService,
        CompetitionService $competitionService
    )
    {
        $this->matchService = $matchService;
        $this->competitionService = $competitionService;
    }

    public function indexMatch()
    {
        if ($this->isAllAdmin()){
            $events = $this->matchService->matchCalendar();
            $tableRoute = route('admin.match-schedules.index');

        } elseif ($this->isCoach()){
            $coach = $this->getLoggedCoachUser();
            $events = $this->matchService->coachTeamsMatchCalendar($coach);
            $tableRoute = route('coach.match-schedules.index');

        } else {
            $player = $this->getLoggedPLayerUser();
            $events = $this->matchService->playerTeamsMatchCalendar($player);
            $tableRoute = route('player.match-schedules.index');
        }

        return view('pages.academies.schedules.matches.index', [
            'events' => $events,
            'tableRoute' => $tableRoute,
        ]);
    }

    public function adminIndexMatch(): JsonResponse
    {
        return $this->matchService->dataTablesMatch();
    }
    public function coachIndexMatch(): JsonResponse
    {
        $coach = $this->getLoggedCoachUser();
            return $this->matchService->coachTeamsDataTablesMatch($coach);
    }
    public function playerIndexMatch(): JsonResponse
    {
        $player = $this->getLoggedPLayerUser();
        return $this->matchService->playerTeamsDataTablesMatch($player);
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
        $data = $this->matchService->internalMatchTeams($exceptTeamId);
        return ApiResponse::success($data);
    }

    public function storeMatch(MatchScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $userId = $this->getLoggedUserId();
        $this->matchService->storeMatch($data, $userId);

        $text = 'Match schedule successfully added!';
        Alert::success($text);
        return redirect()->route('match-schedules.index');
    }

    public function showMatch(MatchModel $schedule)
    {
        $data = $this->matchService->show($schedule);

        $homeTeam = $this->matchService->homeTeamMatch($schedule);
        $homePlayers = $this->matchService->homeTeamPlayers($schedule);
        $homeCoaches = $this->matchService->homeTeamCoaches($schedule);
        $homeTeamMatchScorers = $this->matchService->homeTeamMatchScorers($schedule);
        $homeTeamAttendance = $this->matchService->eventAttendance($schedule, $schedule->homeTeam);
        $homeTeamNotes = $this->matchService->homeTeamNotes($schedule);

        $awayTeam = $this->matchService->awayTeamMatch($schedule);
        $awayPlayers = null;
        $awayCoaches = null;
        $awayTeamMatchScorers = null;
        $awayTeamAttendance = null;
        $awayTeamNotes = null;
        $userTeams = null;

        if ($schedule->matchType == 'Internal Match'){
            $awayPlayers = $this->matchService->awayTeamPlayers($schedule);
            $awayCoaches = $this->matchService->awayTeamCoaches($schedule);
            $awayTeamMatchScorers = $this->matchService->awayTeamMatchScorers($schedule);
            $awayTeamAttendance = $this->matchService->eventAttendance($schedule, $schedule->awayTeam);
            $awayTeamNotes = $this->matchService->awayTeamNotes($schedule);

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
            $data = $this->matchService->show($schedule, $player);
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

    public function getMatchDetail(MatchModel $schedule): JsonResponse
    {
        $data = $this->matchService->getMatchDetail($schedule);
        return ApiResponse::success($data);
    }

    public function getTeamMatchStats(Request $request, MatchModel $schedule): JsonResponse
    {
        $team = $request->input('team');
        $data = $this->matchService->getTeamMatchStats($schedule, $team);
        return ApiResponse::success($data);
    }

    public function editMatch(MatchModel $schedule)
    {
        return view('pages.academies.schedules.matches.edit', [
            'competitions' => $this->competitionService->getActiveCompetition(),
            'data' => $schedule
        ]);
    }

    public function updateMatch(CompetitionMatchRequest $request, MatchModel $schedule): JsonResponse
    {
        $data = $request->validated();
        $this->matchService->updateMatch($data, $schedule);
        return ApiResponse::success(message: 'Match session successfully updated!');
    }

    public function status(MatchModel $schedule, $status): JsonResponse
    {
        try {
            $this->matchService->setStatus($schedule, $status);
            return ApiResponse::success(message: $schedule->eventType.' session status successfully mark to '.$status.'!');

        } catch (Exception $e) {
            Log::error('Error marking '.$schedule->eventType.' session as '.$status.': ' . $e->getMessage());
            return ApiResponse::error('An error occurred while marking the competition '.$schedule->eventType.' session as '.$status.'.');
        }
    }

    public function scheduled(MatchModel $schedule): JsonResponse
    {
        if ($schedule->startDatetime < Carbon::now()) {
            return ApiResponse::error("You cannot set the match session to scheduled because the match date has passed, please change the match start date to a future date.");
        } else {
            return $this->status($schedule, 'scheduled');
        }
    }

    public function ongoing(MatchModel $schedule): JsonResponse
    {
        return $this->status($schedule, 'ongoing');
    }
    public function completed(MatchModel $schedule): JsonResponse
    {
        return $this->status($schedule, 'completed');
    }
    public function cancelled(MatchModel $schedule): JsonResponse
    {
        return $this->status($schedule, 'cancelled');
    }

    public function endMatch(MatchModel $schedule)
    {
        $this->matchService->endMatch($schedule);

        $text = 'Match status successfully ended!';
        Alert::success($text);
        return redirect()->route('match-schedules.show', $schedule->id);
    }

    public function getPlayerAttendance(MatchModel $schedule, Player $player): JsonResponse
    {
        try {
            $data = $this->matchService->getPlayerAttendance($schedule, $player);
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

    public function getCoachAttendance(MatchModel $schedule, Coach $coach): JsonResponse
    {
        try {
            $data = $this->matchService->getCoachAttendance($schedule, $coach);
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
            $this->matchService->updatePlayerAttendanceStatus($data, $schedule, $player);
            $message = "Player ".$this->getUserFullName($player->user)."'s attendance successfully set to ".$data['attendanceStatus'].".";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating attendance for player ".$this->getUserFullName($player->user).": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updateCoachAttendance(AttendanceStatusRequest $request, MatchModel $schedule, Coach $coach): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->matchService->updateCoachAttendanceStatus($data, $schedule, $coach);
            $message = "Coach ".$this->getUserFullName($coach->user)."'s attendance successfully set to ".$data['attendanceStatus'].".";
            return ApiResponse::success(message:  $message);
        } catch (Exception $e){
            $message = "Error while updating attendance for coach ".$this->getUserFullName($coach->user).": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function createNote(ScheduleNoteRequest $request, MatchModel $schedule): JsonResponse
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        try {
            $this->matchService->createNote($data, $schedule, $loggedUser);
            $message = "Note for this ".$schedule->eventType." session successfully created.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while creating a note for this session: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function editNote(MatchModel $schedule, MatchNote $note): JsonResponse
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

    public function updateNote(ScheduleNoteRequest $request, MatchModel $schedule, MatchNote $note): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->matchService->updateNote($data, $schedule, $note, $this->getLoggedUser());
            $message = "Note successfully updated.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating note data: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
    public function destroyNote(MatchModel $schedule, MatchNote $note): JsonResponse
    {
        try {
            $this->matchService->destroyNote($schedule, $note, $this->getLoggedUser());
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
        $data = $this->matchService->getFriendlyMatchTeam();
        $data = [
            'teams' => $data['teams'],
            'opponentTeams' => $data['opponentTeams'],
        ];
        return ApiResponse::success($data, message:  "Successfully retrieved friendly match team data");
    }

    public function getEventPLayers(Request $request, MatchModel $schedule): JsonResponse
    {
        $team = $request->input('team');
        $exceptPlayerId = $request->input('exceptPlayerId');

        $data = $this->matchService->getMatchPLayers($schedule, $team, $exceptPlayerId);

        return ApiResponse::success($data, message:  "Successfully retrieved player data");
    }

    public function storeMatchScorer(MatchScoreRequest $request, MatchModel $schedule): JsonResponse
    {
        $data = $request->validated();
        try {
            if ($data['dataTeam'] == 'awayTeam') {
                $scorer = $this->matchService->storeMatchScorer($data, $schedule, true);
            } else {
                $scorer = $this->matchService->storeMatchScorer($data, $schedule);
            }

            $message = $this->getUserFullName($scorer->player->user)."'s score successfully added.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while adding match scorer ".$this->getUserFullName($scorer->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function destroyMatchScorer(MatchModel $schedule, MatchScore $scorer): JsonResponse
    {
        try {
            if ($scorer->teamId == $schedule->awayTeamId) {
                $this->matchService->destroyMatchScorer($schedule, $scorer, true);
            } else {
                $this->matchService->destroyMatchScorer($schedule, $scorer);
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
        $this->matchService->updateMatchStats($data, $schedule);
        return ApiResponse::success(message:  "Team match stats successfully updated.");
    }

    public function updateExternalTeamScore(ExternalTeamScoreRequest $request, MatchModel $schedule): JsonResponse
    {
        $data = $request->validated();
        $this->matchService->updateExternalTeamScore($data, $schedule);
        return ApiResponse::success(message:  "Team ".$schedule->externalTeam->teamName." score successfully updated.");
    }

    public function storeOwnGoal(MatchScoreRequest $request, MatchModel $schedule): JsonResponse
    {
        $data = $request->validated();
        try {
            if ($data['dataTeam'] == 'awayTeam') {
                $ownGoal = $this->matchService->storeOwnGoal($data, $schedule, true);
            } else {
                $ownGoal = $this->matchService->storeOwnGoal($data, $schedule);
            }
            $message = $this->getUserFullName($ownGoal->player->user)."'s own goal successfully added.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while adding own goal ".$this->getUserFullName($ownGoal->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function destroyOwnGoal(MatchModel $schedule, MatchScore $scorer): JsonResponse
    {
        try {
            if ($scorer->teamId == $schedule->awayTeamId) {
                $this->matchService->destroyOwnGoal($schedule, $scorer, true);
            } else {
                $this->matchService->destroyOwnGoal($schedule, $scorer);
            }
            $message = $this->getUserFullName($scorer->player->user)."'s own goal successfully deleted.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while deleting own goal ".$this->getUserFullName($scorer->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function indexPlayerMatchStats(Request $request, MatchModel $schedule): JsonResponse
    {
        $teamId = $request->input('teamId');
        return $this->matchService->dataTablesPlayerStats($schedule, $teamId);
    }

    public function getPlayerStats(MatchModel $schedule, Player $player): JsonResponse
    {
        $player = $this->matchService->getPlayerStats($schedule, $player);
        return ApiResponse::success($player, message:  "Successfully retrieved player stats");
    }

    public function updatePlayerStats(PlayerMatchStatsRequest $request, MatchModel $schedule, Player $player): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->matchService->updatePlayerStats($data, $schedule, $player);
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
    public function destroy(MatchModel $schedule): JsonResponse
    {
        try {
            $message = "Match ".$schedule->homeTeam->teamName." Vs. ".$this->getAwayTeamName($schedule)." successfully deleted.";
            $data = $this->matchService->destroy($schedule, $this->getLoggedUser());
            return ApiResponse::success($data, message:  $message);

        } catch (Exception $e){
            $message = "Error while deleting match ".$schedule->teams[0]->teamName." Vs. ".$schedule->teams[1]->teamName." : " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    private function getAwayTeamName(MatchModel $schedule): string
    {
        ($schedule->matchType == 'Internal Match') ? $away = $schedule->awayTeam->teamName : $away = $schedule->externalTeam->teamName;
        return $away;
    }
}
