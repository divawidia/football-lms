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
use App\Services\TeamService;
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
    private TeamService $teamService;
    private CompetitionService $competitionService;
    public function __construct(
        MatchService $matchService,
        TeamService $teamService,
        CompetitionService $competitionService
    )
    {
        $this->matchService = $matchService;
        $this->teamService = $teamService;
        $this->competitionService = $competitionService;
    }

    public function indexMatch()
    {
        if ($this->isAllAdmin()){
            $events = $this->matchService->matchCalendar();
            $tableRoute = route('match-schedules.admin-index');
        } elseif ($this->isCoach()){
            $events = $this->matchService->coachTeamsMatchCalendar($this->getLoggedCoachUser());
            $tableRoute = route('match-schedules.coach-index');
        } else {
            $events = $this->matchService->playerTeamsMatchCalendar($this->getLoggedPLayerUser());
            $tableRoute = route('match-schedules.player-index');
        }

        return view('pages.academies.schedules.matches.index', [
            'events' => $events,
            'tableRoute' => $tableRoute,
        ]);
    }

    public function indexMatchHistories()
    {
        if ($this->isAllAdmin()){
            $events = $this->matchService->matchCalendarHistories();
            $tableRoute = route('match-histories.admin-index');
        } elseif ($this->isCoach()){
            $events = $this->matchService->coachTeamsMatchCalendarHistories($this->getLoggedCoachUser());
            $tableRoute = route('match-histories.coach-index');
        } else {
            $events = $this->matchService->playerTeamsMatchCalendarHistories($this->getLoggedPLayerUser());
            $tableRoute = route('match-histories.player-index');
        }

        return view('pages.academies.match-histories.index', [
            'events' => $events,
            'tableRoute' => $tableRoute,
        ]);
    }

    public function adminIndexMatch(): JsonResponse
    {
        return $this->matchService->adminDataTablesMatch();
    }
    public function coachIndexMatch(): JsonResponse
    {
            return $this->matchService->coachTeamsDataTablesMatch($this->getLoggedCoachUser());
    }
    public function playerIndexMatch(): JsonResponse
    {
        return $this->matchService->playerTeamsDataTablesMatch($this->getLoggedPLayerUser());
    }


    public function adminIndexMatchHistories(): JsonResponse
    {
        return $this->matchService->adminDataTablesMatchHistories();
    }
    public function coachIndexMatchHistories(): JsonResponse
    {
        return $this->matchService->coachTeamsDataTablesMatchHistories($this->getLoggedCoachUser());
    }
    public function playerIndexMatchHistories(): JsonResponse
    {
        return $this->matchService->playerTeamsDataTablesMatchHistories($this->getLoggedPLayerUser());
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
        $this->matchService->storeMatch($data, $this->getLoggedUser());

        Alert::success('Match schedule successfully added!');
        return redirect()->route('match-schedules.index');
    }

    public function showMatch(MatchModel $match)
    {
        $playerSkills = null;
        $playerPerformanceReviews = null;

        $awayTeam = $this->matchService->awayTeamMatch($match);
        $homeTeam = $this->matchService->homeTeamMatch($match);
        $awayPlayers = null;
        $awayCoaches = null;
        $awayTeamMatchScorers = null;
        $awayTeamTotalParticipant = null;
        $awayTeamTotalIllness = null;
        $awayTeamTotalOthers = null;
        $awayTeamTotalInjured = null;
        $awayTeamTotalDidntAttend = null;
        $awayTeamTotalAttended = null;
        $awayTeamNotes = null;
        $userTeams = null;

        if ($match->matchType == 'Internal Match'){
            $awayPlayers = $this->matchService->awayTeamPlayers($match);
            $awayCoaches = $this->matchService->awayTeamCoaches($match);
            $awayTeamMatchScorers = $this->matchService->awayTeamMatchScorers($match);
            $awayTeamTotalParticipant =$this->matchService->totalParticipant($match, $awayTeam);
            $awayTeamTotalIllness = $this->matchService->totalIllness($match, $awayTeam);
            $awayTeamTotalOthers = $this->matchService->totalOther($match, $awayTeam);
            $awayTeamTotalInjured = $this->matchService->totalInjured($match, $awayTeam);
            $awayTeamTotalDidntAttend = $this->matchService->totalDidntAttend($match, $awayTeam);
            $awayTeamTotalAttended = $this->matchService->totalAttended($match, $awayTeam);
            $awayTeamNotes = $this->matchService->awayTeamNotes($match);

            if ($this->isCoach()) {
                $userTeams = collect($this->getLoggedCoachUser()->teams)->pluck('id')->all();
            } elseif ($this->isPlayer()) {
                $userTeams = collect($this->getLoggedPLayerUser()->teams)->pluck('id')->all();
            } else {
                $userTeams = collect($this->teamService->allTeams())->pluck('id')->all();
            }
        }

        if ($this->isPlayer()){
            $playerSkills = $this->matchService->playerSkills($match, $this->getLoggedPLayerUser());
            $playerPerformanceReviews = $this->matchService->playerPerformanceReviews($match, $this->getLoggedPLayerUser());
        }

        return view('pages.academies.schedules.matches.detail', [
            'schedule' => $match,
            'playerSkills' => $playerSkills,
            'playerPerformanceReviews' => $playerPerformanceReviews,
            'homeTeam' => $this->matchService->homeTeamMatch($match),
            'homePlayers' => $this->matchService->homeTeamPlayers($match),
            'homeCoaches' => $this->matchService->homeTeamCoaches($match),
            'homeTeamMatchScorers' => $this->matchService->homeTeamMatchScorers($match),
            'homeTeamTotalParticipant'=> $this->matchService->totalParticipant($match),
            'homeTeamTotalIllness' => $this->matchService->totalIllness($match, $homeTeam),
            'homeTeamTotalOthers' => $this->matchService->totalOther($match, $homeTeam),
            'homeTeamTotalInjured' => $this->matchService->totalInjured($match, $homeTeam),
            'homeTeamTotalDidntAttend' => $this->matchService->totalDidntAttend($match, $homeTeam),
            'homeTeamTotalAttended' => $this->matchService->totalAttended($match, $homeTeam),
            'homeTeamNotes' => $this->matchService->homeTeamNotes($match),
            'awayTeam' => $awayTeam,
            'awayCoaches' => $awayCoaches,
            'awayPlayers' => $awayPlayers,
            'awayTeamMatchScorers' => $awayTeamMatchScorers,
            'awayTeamTotalParticipant' => $awayTeamTotalParticipant,
            'awayTeamTotalIllness' => $awayTeamTotalIllness,
            'awayTeamTotalOthers' => $awayTeamTotalOthers,
            'awayTeamTotalInjured' => $awayTeamTotalInjured,
            'awayTeamTotalDidntAttend' => $awayTeamTotalDidntAttend,
            'awayTeamTotalAttended' => $awayTeamTotalAttended,
            'awayTeamNotes' => $awayTeamNotes,
            'userTeams' => $userTeams,
        ]);
    }

    public function getMatchDetail(MatchModel $match): JsonResponse
    {
        $data = $this->matchService->getMatchDetail($match);
        return ApiResponse::success($data);
    }

    public function getTeamMatchStats(Request $request, MatchModel $match): JsonResponse
    {
        $team = $request->input('team');
        $data = $this->matchService->getTeamMatchStats($match, $team);
        return ApiResponse::success($data);
    }

    public function editMatch(MatchModel $match)
    {
        return view('pages.academies.schedules.matches.edit', [
            'competitions' => $this->competitionService->getActiveCompetition(),
            'data' => $match
        ]);
    }

    public function updateMatch(CompetitionMatchRequest $request, MatchModel $match): JsonResponse
    {
        $data = $request->validated();
        $this->matchService->updateMatch($data, $match, $this->getLoggedUser());
        return ApiResponse::success(message: 'Match session successfully updated!');
    }

    public function scheduled(MatchModel $match): JsonResponse
    {
        if ($match->startDatetime < Carbon::now()) {
            return ApiResponse::error("You cannot set the match session to scheduled because the match date has passed, please change the match start date to a future date.");
        } else {
            $this->matchService->setScheduled($match, $this->getLoggedUser());
            return ApiResponse::success(message: 'Match session status successfully set to scheduled!');
        }
    }

    public function ongoing(MatchModel $match): JsonResponse
    {
        $this->matchService->setOngoing($match);
        return ApiResponse::success(message: 'Match session status successfully set to ongoing!');
    }
    public function completed(MatchModel $match): JsonResponse
    {
        $this->matchService->setCompleted($match);
        return ApiResponse::success(message: 'Match session status successfully set to completed!');
    }
    public function cancelled(MatchModel $match): JsonResponse
    {
        $this->matchService->setCanceled($match, $this->getLoggedUser());
        return ApiResponse::success(message: 'Match session status successfully set to canceled!');
    }

    public function getPlayerAttendance(MatchModel $match, Player $player): JsonResponse
    {
        try {
            $data = $this->matchService->getPlayerAttendance($match, $player);
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

    public function getCoachAttendance(MatchModel $match, Coach $coach): JsonResponse
    {
        try {
            $data = $this->matchService->getCoachAttendance($match, $coach);
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

    public function updatePlayerAttendance(AttendanceStatusRequest $request, MatchModel $match, Player $player): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->matchService->updatePlayerAttendanceStatus($data, $match, $player);
            $message = "Player ".$this->getUserFullName($player->user)."'s attendance successfully set to ".$data['attendanceStatus'].".";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating attendance for player ".$this->getUserFullName($player->user).": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updateCoachAttendance(AttendanceStatusRequest $request, MatchModel $match, Coach $coach): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->matchService->updateCoachAttendanceStatus($data, $match, $coach);
            $message = "Coach ".$this->getUserFullName($coach->user)."'s attendance successfully set to ".$data['attendanceStatus'].".";
            return ApiResponse::success(message:  $message);
        } catch (Exception $e){
            $message = "Error while updating attendance for coach ".$this->getUserFullName($coach->user).": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function createNote(ScheduleNoteRequest $request, MatchModel $match): JsonResponse
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        try {
            $this->matchService->createNote($data, $match, $loggedUser);
            $message = "Note for this ".$match->eventType." session successfully created.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while creating a note for this session: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function editNote(MatchModel $match, MatchNote $note): JsonResponse
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

    public function updateNote(ScheduleNoteRequest $request, MatchModel $match, MatchNote $note): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->matchService->updateNote($data, $match, $note, $this->getLoggedUser());
            $message = "Note successfully updated.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while updating note data: ". $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
    public function destroyNote(MatchModel $match, MatchNote $note): JsonResponse
    {
        try {
            $this->matchService->destroyNote($match, $note, $this->getLoggedUser());
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

    public function getEventPLayers(Request $request, MatchModel $match): JsonResponse
    {
        $team = $request->input('team');
        $exceptPlayerId = $request->input('exceptPlayerId');

        $data = $this->matchService->getMatchPLayers($match, $team, $exceptPlayerId);

        return ApiResponse::success($data, message:  "Successfully retrieved player data");
    }

    public function storeMatchScorer(MatchScoreRequest $request, MatchModel $match): JsonResponse
    {
        $data = $request->validated();
        try {
            if ($data['dataTeam'] == 'awayTeam') {
                $scorer = $this->matchService->storeMatchScorer($data, $match, true);
            } else {
                $scorer = $this->matchService->storeMatchScorer($data, $match);
            }

            $message = $this->getUserFullName($scorer->player->user)."'s score successfully added.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while adding match scorer ".$this->getUserFullName($scorer->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function destroyMatchScorer(MatchModel $match, MatchScore $scorer): JsonResponse
    {
        try {
            if ($scorer->teamId == $match->awayTeamId) {
                $this->matchService->destroyMatchScorer($match, $scorer, true);
            } else {
                $this->matchService->destroyMatchScorer($match, $scorer);
            }

            $message = $this->getUserFullName($scorer->player->user)."'s score successfully deleted.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while deleting match scorer ".$this->getUserFullName($scorer->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updateMatchStats(MatchStatsRequest $request, MatchModel $match): JsonResponse
    {
        $data = $request->validated();
        $this->matchService->updateMatchStats($data, $match);
        return ApiResponse::success(message:  "Team match stats successfully updated.");
    }

    public function updateExternalTeamScore(ExternalTeamScoreRequest $request, MatchModel $match): JsonResponse
    {
        $data = $request->validated();
        $this->matchService->updateExternalTeamScore($data, $match);
        return ApiResponse::success(message:  "Team ".$match->externalTeam->teamName." score successfully updated.");
    }

    public function storeOwnGoal(MatchScoreRequest $request, MatchModel $match): JsonResponse
    {
        $data = $request->validated();
        try {
            if ($data['dataTeam'] == 'awayTeam') {
                $ownGoal = $this->matchService->storeOwnGoal($data, $match, true);
            } else {
                $ownGoal = $this->matchService->storeOwnGoal($data, $match);
            }
            $message = $this->getUserFullName($ownGoal->player->user)."'s own goal successfully added.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while adding own goal ".$this->getUserFullName($ownGoal->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function destroyOwnGoal(MatchModel $match, MatchScore $scorer): JsonResponse
    {
        try {
            if ($scorer->teamId == $match->awayTeamId) {
                $this->matchService->destroyOwnGoal($match, $scorer, true);
            } else {
                $this->matchService->destroyOwnGoal($match, $scorer);
            }
            $message = $this->getUserFullName($scorer->player->user)."'s own goal successfully deleted.";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while deleting own goal ".$this->getUserFullName($scorer->player->user).":" . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function indexPlayerMatchStats(Request $request, MatchModel $match): JsonResponse
    {
        $teamId = $request->input('teamId');
        return $this->matchService->dataTablesPlayerStats($match, $teamId);
    }

    public function getPlayerStats(MatchModel $match, Player $player): JsonResponse
    {
        $player = $this->matchService->getPlayerStats($match, $player);
        return ApiResponse::success($player, message:  "Successfully retrieved player stats");
    }

    public function updatePlayerStats(PlayerMatchStatsRequest $request, MatchModel $match, Player $player): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->matchService->updatePlayerStats($data, $match, $player);
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
    public function destroy(MatchModel $match): JsonResponse
    {
        try {
            $message = "Match ".$match->homeTeam->teamName." Vs. ".$this->getAwayTeamName($match)." successfully deleted.";
            $data = $this->matchService->destroy($match, $this->getLoggedUser());
            return ApiResponse::success($data, message:  $message);

        } catch (Exception $e){
            $message = "Error while deleting match ".$match->teams[0]->teamName." Vs. ".$match->teams[1]->teamName." : " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    private function getAwayTeamName(MatchModel $match): string
    {
        ($match->matchType == 'Internal Match') ? $away = $match->awayTeam->teamName : $away = $match->externalTeam->teamName;
        return $away;
    }
}
