<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Http\Requests\UpdateCoachTeamRequest;
use App\Http\Requests\UpdatePlayerTeamRequest;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Services\OpponentTeamService;
use App\Services\TeamService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nnjeim\World\World;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private TeamService $teamService;
    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        if (isAllAdmin()){
            $teamRoutes = url()->route('team-managements.admin-teams');
        } elseif (isCoach()){
            $teamRoutes = url()->route('team-managements.coach-teams');
        } else {
            $teamRoutes = url()->route('team-managements.player-teams');
        }

        return view('pages.managements.teams.index', [
            'teamRoutes' => $teamRoutes
        ]);
    }

    public function adminTeamsData(): JsonResponse
    {
        return $this->teamService->index();
    }

    public function coachTeamsData(): JsonResponse
    {
        return $this->teamService->coachTeamsIndex($this->getLoggedCoachUser());
    }

    public function playerTeamsData(): JsonResponse
    {
        return $this->teamService->playerTeamsIndex($this->getLoggedPLayerUser());
    }

    public function teamPlayers(Team $team): JsonResponse
    {
        return $this->teamService->teamPlayers($team);
    }

    public function teamCoaches(Team $team): JsonResponse
    {
        return $this->teamService->teamCoaches($team);
    }

    public function teamTrainingHistories(Team $team): JsonResponse
    {
        return $this->teamService->teamTrainingHistories($team);
    }

    public function teamMatchHistories(Team $team): JsonResponse
    {
        return $this->teamService->teamMatchHistories($team);
    }

    public function allTeams(Request $request)
    {
        $exceptTeamId = $request->input('exceptTeamId');
        $data = $this->teamService->allTeams($exceptTeamId);
        return ApiResponse::success($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.managements.teams.create', [
            'players' => Player::all(),
            'coaches' => Coach::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamRequest $request)
    {
        $data = $request->validated();

        $this->teamService->store($data, $this->getAcademyId(), $this->getLoggedUser());

        Alert::success('Team '.$data['teamName'].' successfully added!');
        return redirect()->route('team-managements.index');
    }

    public function apiStore(TeamRequest $request): JsonResponse
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();

        $team = $this->teamService->store($data, $this->getAcademyId(), $loggedUser);

        return response()->json($team, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();
        return view('pages.managements.teams.detail', [
            'team' => $team,
            'teamScore' => $this->teamService->teamScore($team),
            'teamScoreThisMonth' => $this->teamService->teamScore($team, $startDate, $endDate),
            'cleanSheets' => $this->teamService->cleanSheets($team),
            'cleanSheetsThisMonth' => $this->teamService->cleanSheets($team, $startDate, $endDate),
            'teamOwnGoal' => $this->teamService->teamOwnGoal($team),
            'teamOwnGoalThisMonth' => $this->teamService->teamOwnGoal($team, $startDate, $endDate),
            'goalsConceded' => $this->teamService->goalsConceded($team),
            'goalsConcededThisMonth' => $this->teamService->goalsConceded($team, $startDate, $endDate),
            'goalsDifference' => $this->teamService->goalsDifference($team),
            'goalsDifferenceThisMonth' => $this->teamService->goalsDifference($team, $startDate, $endDate),
            'matchPlayed' => $this->teamService->matchPlayed($team),
            'matchPlayedThisMonth' => $this->teamService->matchPlayed($team, $startDate, $endDate),
            'wins' => $this->teamService->wins($team),
            'winsThisMonth' => $this->teamService->wins($team, $startDate, $endDate),
            'draws' => $this->teamService->draws($team),
            'drawsThisMonth' => $this->teamService->draws($team, $startDate, $endDate),
            'losses' => $this->teamService->losses($team),
            'lossesThisMonth' => $this->teamService->losses($team, $startDate, $endDate),
            'winRate' => $this->teamService->winRate($team),
            'winRateThisMonth' => $this->teamService->winRate($team, $startDate, $endDate),
            'latestMatches' => $this->teamService->teamLatestMatch($team),
            'upcomingMatches' => $this->teamService->teamUpcomingMatch($team),
            'upcomingTrainings' => $this->teamService->teamUpcomingTraining($team),
            'players' => $this->teamService->playersNotJoinTheTeam($team),
            'coaches' => $this->teamService->coachesNotJoinTheTeam($team),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        return view('pages.managements.teams.edit',[
            'team' => $team
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamRequest $request, Team $team): RedirectResponse
    {
        $data = $request->validated();

        $this->teamService->update($data, $team, $this->getLoggedUser());

        Alert::success('Team '.$team->teamName.' successfully updated!');
        return redirect()->route('team-managements.show', $team->id);
    }

    public function deactivate(Team $team): JsonResponse
    {
        try {
            $message = "Team {$team->teamName}'s status successfully set to deactivated.";
            $data = $this->teamService->setStatus($team, '0', $this->getLoggedUser());
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating team {$team->teamName}'s status to deactivate: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function activate(Team $team): JsonResponse
    {
        try {
            $message = "Team ".$team->teamName."'s status successfully set to activated.";
            $data = $this->teamService->setStatus($team, '1', $this->getLoggedUser());
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating team ".$team->teamName."'s status to activated: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updatePlayerTeam(UpdatePlayerTeamRequest $request, Team $team): JsonResponse
    {
        $data = $request->validated();
        $result = $this->teamService->updatePlayerTeam($data, $team, $this->getLoggedUser());
        $text = "Team ".$team->teamName." successfully added new players!";
        return ApiResponse::success($result, $text);
    }

    public function updateCoachTeam(UpdateCoachTeamRequest $request, Team $team): JsonResponse
    {
        $data = $request->validated();
        $result = $this->teamService->updateCoachTeam($data, $team, $this->getLoggedUser());
        $text = "Team ".$team->teamName." successfully added new coaches!";
        return ApiResponse::success($result, $text);
    }

    public function removePlayer(Team $team, Player $player): JsonResponse
    {
        try {
            $this->teamService->removePlayer($team, $player, $this->getLoggedUser());
            $message = "Player ".$this->getUserFullName($player->user)." successfully removed from team ".$team->teamName.".";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while removing player ".$this->getUserFullName($player->user)." from team ".$team->teamName.": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function removeCoach(Team $team, Coach $coach): JsonResponse
    {
        try {
            $this->teamService->removeCoach($team, $coach, $this->getLoggedUser());
            $message = "Coach ".$this->getUserFullName($coach->user)." successfully removed from team ".$team->teamName.".";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while removing coach ".$this->getUserFullName($coach->user)." from team ".$team->teamName.": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team): JsonResponse
    {
        try {
            $message = "Team ".$team->teamName."'s successfully deleted.";
            $data = $this->teamService->destroy($team, $this->getLoggedUser());
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while deleting team ".$team->teamName."'s: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

}
