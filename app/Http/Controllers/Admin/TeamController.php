<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Services\OpponentTeamService;
use App\Services\TeamService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
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
            $teamRoutes = url()->route('coach.team-managements.coach-teams');
        } elseif (isPlayer()){
            $teamRoutes = url()->route('player.team-managements.player-teams');
        }

        return view('pages.admins.managements.teams.index', [
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

    public function teamCompetitions(Team $team): JsonResponse
    {
        return $this->teamService->teamCompetition($team);
    }

    public function teamTrainingHistories(Team $team): JsonResponse
    {
        return $this->teamService->teamTrainingHistories($team);
    }

    public function teamMatchHistories(Team $team): JsonResponse
    {
        return $this->teamService->teamMatchHistories($team);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $players = Player::all();
        $coaches = Coach::all();

        return view('pages.admins.managements.teams.create', [
            'players' => $players,
            'coaches' => $coaches
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamRequest $request)
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();

        $this->teamService->store($data, $this->getAcademyId(), $loggedUser);

        $text = 'Team '.$data['teamName'].' successfully added!';
        Alert::success($text);
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
        return view('pages.admins.managements.teams.detail', [
            'team' => $team,
            'overview' => $this->teamService->teamOverviewStats($team),
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
        return view('pages.admins.managements.teams.edit',[
            'team' => $team
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamRequest $request, Team $team)
    {
        $data = $request->validated();

        $loggedUser = $this->getLoggedUser();
        $this->teamService->update($data, $team, $loggedUser);

        $text = 'Team '.$team->teamName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('team-managements.show', $team->id);
    }

    public function deactivate(Team $team)
    {
        $loggedUser = $this->getLoggedUser();
        try {
            $data = $this->teamService->setStatus($team, '0', $loggedUser);
            $message = "Team ".$team->teamName."'s status successfully set to deactivated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating team ".$team->teamName."'s status to deactivate: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function activate(Team $team)
    {
        $loggedUser = $this->getLoggedUser();
        try {
            $data = $this->teamService->setStatus($team, '1', $loggedUser);
            $message = "Team ".$team->teamName."'s status successfully set to activated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating team ".$team->teamName."'s status to activated: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function updatePlayerTeam(Request $request, Team $team)
    {
        $data = $request->validate([
            'players' => ['required', Rule::exists('players', 'id')],
        ]);
        $result = $this->teamService->updatePlayerTeam($data, $team);
        $text = "Team ".$team->teamName." successfully added new players!";
        return ApiResponse::success($result, $text);
    }

    public function updateCoachTeam(Request $request, Team $team)
    {
        $data = $request->validate([
            'coaches' => ['required', Rule::exists('coaches', 'id')]
        ]);
        $result = $this->teamService->updateCoachTeam($data, $team);
        $text = "Team ".$team->teamName." successfully added new coaches!";
        return ApiResponse::success($result, $text);
    }

    public function removePlayer(Team $team, Player $player)
    {
        try {
            $this->teamService->removePlayer($team, $player);
            $message = "Player ".$this->getUserFullName($player->user)." successfully removed from team ".$team->teamName.".";
            return ApiResponse::success(message:  $message);

        } catch (Exception $e){
            $message = "Error while removing player ".$this->getUserFullName($player->user)." from team ".$team->teamName.": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function removeCoach(Team $team, Coach $coach)
    {
        try {
            $this->teamService->removeCoach($team, $coach);
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
    public function destroy(Team $team)
    {
        $loggedUser = $this->getLoggedUser();
        try {
            $data = $this->teamService->destroy($team, $loggedUser);
            $message = "Team ".$team->teamName."'s successfully deleted.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while deleting team ".$team->teamName."'s: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

}
