<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpponentTeamRequest;
use App\Models\OpponentTeam;
use App\Services\OpponentTeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

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
            $this->opponentTeamService->index();
        }
        return view('pages.admins.managements.opponentTeams.index');
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
    public function store(OpponentTeamRequest $request)
    {
        $data = $request->validated();

        $this->opponentTeamService->store($data);

        $text = 'Team '.$data['teamName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('opponentTeam-managements.index');
    }

    public function apiStore(OpponentTeamRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $team = $this->opponentTeamService->store($data);

            return response()->json($team, 201);
        }catch (\Illuminate\Validation\ValidationException $e){
            return response()->json($e->errors(), 422);
        }
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
    public function update(OpponentTeamRequest $request, OpponentTeam $team, OpponentTeamService $opponentTeamService)
    {
        $data = $request->validated();

        $this->opponentTeamService->update($data, $team);

        $text = 'Team '.$team->teamName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('opponentTeam-managements.show', $team->id);
    }

    public function deactivate(OpponentTeam $team){
        $this->opponentTeamService->deactivate($team);

        Alert::success('Team '.$team->teamName.' status successfully deactivated!');
        return redirect()->route('opponentTeam-managements.index');
    }

    public function activate(OpponentTeam $team){
        $this->opponentTeamService->activate($team);

        Alert::success('Team '.$team->teamName.' status successfully activated!');
        return redirect()->route('opponentTeam-managements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OpponentTeam $team)
    {
        $this->opponentTeamService->destroy($team);

        return response()->json(['success' => true]);
    }
}
