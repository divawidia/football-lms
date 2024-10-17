<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Services\OpponentTeamService;
use App\Services\TeamService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
        return view('pages.admins.managements.teams.index');
    }

    public function adminTeamsData(): JsonResponse
    {
        return $this->teamService->index();
    }

    public function coachTeamsData(): JsonResponse
    {
        return $this->teamService->coachTeamsIndex($this->getLoggedCoachUser());
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

        $this->teamService->store($data, Auth::user()->academyId);

        $text = 'Team '.$data['teamName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('team-managements.index');
    }

    public function apiStore(TeamRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $team = $this->teamService->store($data, Auth::user()->academyId);

            return response()->json($team, 201);
        }catch (\Illuminate\Validation\ValidationException $e){
            return response()->json($e->errors(), 422);
        }
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

        $this->teamService->update($data, $team);

        $text = 'Team '.$team->teamName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('team-managements.show', $team->id);
    }

    public function deactivate(Team $team){
        $this->teamService->deactivate($team);

        Alert::success('Team '.$team->teamName.' status successfully deactivated!');
        return redirect()->route('team-managements.index');
    }

    public function activate(Team $team){
        $this->teamService->activate($team);

        Alert::success('Team '.$team->teamName.' status successfully activated!');
        return redirect()->route('team-managements.index');
    }

    public function addPlayerTeam(Team $team)
    {
        $players = Player::with('user')->whereDoesntHave('teams', function (Builder $query) use ($team){
            $query->where('teamId', $team->id);
        })->get();

        return view('pages.admins.managements.teams.editPlayer',[
            'team' => $team,
            'players' => $players,
        ]);
    }

    public function updatePlayerTeam(Request $request, Team $team)
    {
        $data = $request->validate([
            'players' => ['required', Rule::exists('players', 'id')],
        ]);

        $this->teamService->updatePlayerTeam($data, $team);

        $text = 'Team '.$team->teamName.' Players successfully updated!';
        Alert::success($text);
        return redirect()->route('team-managements.show', $team->id);
    }

    public function addCoachesTeam(Team $team)
    {
        $coaches = Coach::with('user')->whereDoesntHave('teams', function (Builder $query) use ($team){
            $query->where('teamId', $team->id);
        })->get();

        return view('pages.admins.managements.teams.editCoach',[
            'team' => $team,
            'coaches' => $coaches,
        ]);
    }

    public function updateCoachTeam(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'coaches' => ['required', Rule::exists('coaches', 'id')]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $this->teamService->updateCoachTeam((array)$validator, $team);

        $text = 'Team '.$team->teamName.' Coaches successfully updated!';
        Alert::success($text);
        return redirect()->route('team-managements.show', $team->id);
    }

    public function removePlayer(Team $team, Player $player)
    {
        $this->teamService->removePlayer($team, $player);

        return response()->json(['success' => true]);
    }

    public function removeCoach(Team $team, Coach $coach)
    {
        $this->teamService->removeCoach($team, $coach);
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
       $this->teamService->destroy($team);

        return response()->json(['success' => true]);
    }
}
