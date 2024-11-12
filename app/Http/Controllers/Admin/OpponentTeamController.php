<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Services\OpponentTeamService;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use RealRashid\SweetAlert\Facades\Alert;

class OpponentTeamController extends Controller
{
    private OpponentTeamService $opponentTeamService;
    private TeamService $teamService;

    public function __construct(OpponentTeamService $opponentTeamService, TeamService $teamService)
    {
        $this->opponentTeamService = $opponentTeamService;
        $this->teamService = $teamService;
    }

    public function index()
    {
        return $this->opponentTeamService->index();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admins.managements.opponentTeams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamRequest $request)
    {
        $data = $request->validated();

        $loggedUser = $this->getLoggedUser();
        $this->opponentTeamService->store($data, $loggedUser);

        $text = 'Team '.$data['teamName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('team-managements.index');
    }

    public function apiStore(TeamRequest $request): JsonResponse
    {
        $data = $request->validated();

        $loggedUser = $this->getLoggedUser();
        $team = $this->opponentTeamService->store($data, $loggedUser);

        return response()->json($team, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        return view('pages.admins.managements.opponentTeams.detail', [
            'team' => $team,
            'overview' => $this->opponentTeamService->teamOverviewStats($team),
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
        return view('pages.admins.managements.opponentTeams.edit',[
            'team' => $team,

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamRequest $request, Team $team)
    {
        $data = $request->validated();

        $loggedUser = $this->getLoggedUser();
        $this->opponentTeamService->update($data, $team, $loggedUser);

        $text = 'Team '.$team->teamName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('team-managements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $loggedUser = $this->getLoggedUser();
        $this->opponentTeamService->destroy($team, $loggedUser);

        return response()->json(['success' => true]);
    }
}
