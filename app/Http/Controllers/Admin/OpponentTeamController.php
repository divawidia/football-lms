<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Models\OpponentTeam;
use App\Models\Team;
use App\Services\OpponentTeamService;
use Illuminate\Http\JsonResponse;
use RealRashid\SweetAlert\Facades\Alert;

class OpponentTeamController extends Controller
{
    private OpponentTeamService $opponentTeamService;

    public function __construct(OpponentTeamService $opponentTeamService)
    {
        $this->opponentTeamService = $opponentTeamService;
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->opponentTeamService->index();
        }
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

        $this->opponentTeamService->store($data);

        $text = 'Team '.$data['teamName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('team-managements.index');
    }

    public function apiStore(TeamRequest $request): JsonResponse
    {
        $data = $request->validated();

        $team = $this->opponentTeamService->store($data);

        return response()->json($team, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OpponentTeam $team)
    {
        return view('pages.admins.managements.opponentTeams.detail', [
            'team' => $team,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OpponentTeam $team)
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

        $this->opponentTeamService->update($data, $team);

        $text = 'Team '.$team->teamName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('opponentTeam-managements.show', $team->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $this->opponentTeamService->destroy($team);

        return response()->json(['success' => true]);
    }
}
