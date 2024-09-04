<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompetitionRequest;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\OpponentTeam;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Competition::with('teams', 'opponentTeams')->get();
            return Datatables::of($query)
                ->addColumn('action', function ($item) {
                    return '
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . route('team-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Competition</a>
                                <a class="dropdown-item" href="' . route('team-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Competition</a>
                                <button type="button" class="dropdown-item delete-user" id="' . $item->id . '">
                                    <span class="material-icons">delete</span> Delete Competition
                                </button>
                              </div>
                            </div>';
                })
                ->editColumn('teams', function ($item) {
                    $academyTeam = '';
                    if (count($item->teams) == 0){
                        $academyTeam = 'No teams in this competition at this moment';
                    }else{
                        foreach ($item->teams as $team) {
                            $academyTeam .= '<span class="badge badge-pill badge-danger">'.$team->teamName.'</span>';
                        }
                    }
                    return $academyTeam;
                })
                ->editColumn('opponentTeams', function ($item) {
                    $opponentTeam = '';
                    if (count($item->opponentTeams) == 0){
                        $opponentTeam = 'No teams in this competition at this moment';
                    }else{
                        $opponentTeam = count($item->opponentTeams).' Opponent teams';
                    }
                    return $opponentTeam;
                })
                ->editColumn('name', function ($item) {
                    return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->name . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->type . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                })
                ->editColumn('date', function ($item) {
                    $startDate = date('M d, Y', strtotime($item->startDate));
                    $endDate = date('M d, Y', strtotime($item->endDate));
                    return $startDate.' '.$endDate;
                })
                ->editColumn('contact', function ($item) {
                    return $item->contactName. ' ('.$item->contactPhone.')';
                })
                ->rawColumns(['action', 'name', 'teams', 'opponentTeams', 'date', 'contact'])
                ->make();
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

        if ($request->hasFile('logo')){
            $data['logo'] = $request->file('logo')->store('assets/competition-logo', 'public');
        }else{
            $data['logo'] = 'images/undefined-user.png';
        }

        $competition = Competition::create($data);

        if ($request->has('teams')){
            $competition->teams()->attach($request->teams);
        }
        if ($request->has('opponentTeams')){
            $competition->opponentTeams()->attach($request->opponentTeams);
        }

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

        if ($request->hasFile('logo')){
            $data['logo'] = $request->file('logo')->store('assets/competition-logo', 'public');
        }else{
            $data['logo'] = $team->logo;
        }

        $competition->update($data);
        $competition->teams()->sync($request->teams);
        $competition->opponentTeams()->sync($request->opponentTeams);

        $text = 'Competition '.$competition->name.' successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competition $competition)
    {
        if (File::exists($competition->logo) && $competition->logo != 'images/undefined-user.png'){
            File::delete($competition->logo);
        }

        $competition->teams()->detach();
        $competition->opponentTeams()->detach();
        $competition->delete();

        return response()->json(['success' => true]);
    }
}
