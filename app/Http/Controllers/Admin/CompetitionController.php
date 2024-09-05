<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompetitionRequest;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\OpponentTeam;
use App\Models\Player;
use App\Models\Team;
use App\Services\CompetitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class CompetitionController extends Controller
{
    private CompetitionService $competitionService;
    public function __construct(CompetitionService $competitionService)
    {
        $this->competitionService = $competitionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->competitionService->index();
        }
        return view('pages.admins.managements.competitions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teams = Team::all();
        $players = Player::all();
        $coaches = Coach::all();
        $opponentTeams = OpponentTeam::all();

        return view('pages.admins.managements.competitions.create', [
            'teams' => $teams,
            'players' => $players,
            'coaches' => $coaches,
            'opponentTeams' => $opponentTeams
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompetitionRequest $request)
    {
        $data = $request->validated();

        $this->competitionService->store($data);

        $text = 'Competition '.$data['name'].' successfully added!';
        Alert::success($text);
        return redirect()->route('competition-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Competition $competition)
    {
        return view('pages.admins.managements.competition.detail', [
            'competition' => $competition,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competition $competition)
    {
        $teams = Team::all();
        $opponentTeams = OpponentTeam::all();
        $teams_id = [];
        $opponentTeams_id = [];

        foreach ($competition->teams as $team){
            $teams_id[] = $team->id;
        }

        foreach ($competition->opponentTeams as $opponentTeam){
            $opponentTeams_id[] = $opponentTeam->id;
        }

        return view('pages.admins.managements.competition.edit',[
            'competition' => $competition,
            'teams' => $teams,
            'opponentTeams' => $opponentTeams,
            'teams_id' => $teams_id,
            'opponentTeams_id' => $opponentTeams_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Competition $competition)
    {
        $data = $request->validated();

        $this->competitionService->update($data, $competition);

        $text = 'Competition '.$competition->name.' successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }

    public function activate(Competition $competition)
    {
        $this->competitionService->activate($competition);

        $text = 'Competition '.$competition->name.' status successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }

    public function deactivate(Competition $competition)
    {
        $this->competitionService->deactivate($competition);

        $text = 'Competition '.$competition->name.' status successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competition $competition)
    {
        $this->competitionService->destroy($competition);

        return response()->json(['success' => true]);
    }
}
