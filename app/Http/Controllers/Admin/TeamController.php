<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Team::with('coaches', 'players')->get();
            return Datatables::of($query)->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-coach', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">block</span> Deactivate Team
                                                </button>
                                            </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-coach', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">check_circle</span> Activate Team
                                                </button>
                                            </form>';
                }
                return '
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . route('team-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Team</a>
                                <a class="dropdown-item" href="' . route('team-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Team</a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item delete-user" id="' . $item->id . '">
                                    <span class="material-icons">delete</span> Delete Team
                                </button>
                              </div>
                            </div>';
            })
                ->editColumn('players', function ($item) {
                    return count($item->players).' Player(s)';
                })
                ->editColumn('coaches', function ($item) {
                    return count($item->coaches).' Coach(es)';
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
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->division . ' - '.$item->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                })
                ->editColumn('status', function ($item) {
                    if ($item->status == '1') {
                        return '<span class="badge badge-pill badge-success">Aktif</span>';
                    } elseif ($item->status == '0') {
                        return '<span class="badge badge-pill badge-danger">Non Aktif</span>';
                    }
                })
                ->rawColumns(['action', 'name', 'status', 'players', 'coaches'])
                ->make();
        }
        return view('pages.admins.managements.teams.index');
    }

    public function teamPlayers(Team $team){
        if (request()->ajax()) {
            $query = $team->players()->get();
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
                                    <a class="dropdown-item" href="' . route('player-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Player</a>
                                    <a class="dropdown-item" href="' . route('player-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Player</a>
                                    <button type="button" class="dropdown-item delete-user" id="' . $item->userId . '">
                                        <span class="material-icons">delete</span> Remove Player From Team
                                    </button>
                                  </div>
                                </div>';
                })
                ->editColumn('age', function ($item){
                    return $this->getAge($item->user->dob);
                })
                ->editColumn('name', function ($item) {
                    return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' '.$item->user->lastName.'</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                })
                ->editColumn('appearance', function ($item) {
                    return 0;
                })
                ->editColumn('goals', function ($item) {
                    return 0;
                })
                ->editColumn('assists', function ($item) {
                    return 0;
                })
                ->editColumn('cleanSheets', function ($item) {
                    return 0;
                })
                ->rawColumns(['action', 'name', 'age', 'appearance', 'goals', 'assists','cleanSheets'])
                ->make();
        }
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

        if ($request->hasFile('logo')){
            $data['logo'] = $request->file('logo')->store('assets/team-logo', 'public');
        }else{
            $data['logo'] = 'images/undefined-user.png';
        }

        $data['status'] = '1';
        $data['academyId'] = Auth::user()->academyId;

        $team = Team::create($data);

        if ($request->has('players')){
            $team->players()->attach($request->players);
        }
        if ($request->has('coaches')){
            $team->coaches()->attach($request->coaches);
        }

        $text = 'Team '.$data['teamName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('team-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        return view('pages.admins.managements.teams.detail', [
            'team' => $team,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $players = Player::all();
        $coaches = Coach::all();
        $player_id = [];
        $coach_id = [];

        foreach ($team->players as $player){
            $player_id[] = $player->id;
        }

        foreach ($team->coaches as $coach){
            $coach_id[] = $coach->id;
        }

        return view('pages.admins.managements.teams.edit',[
            'team' => $team,
            'players' => $players,
            'coaches' => $coaches,
            'player_id' => $player_id,
            'coach_id' => $coach_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')){
            $data['logo'] = $request->file('logo')->store('assets/team-logo', 'public');
        }else{
            $data['logo'] = $team->logo;
        }

        $team->update($data);
        $team->players()->sync($request->players);
        $team->coaches()->sync($request->coaches);

        $text = 'Team '.$team->teamName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('team-managements.show', $team->id);
    }

    public function deactivate(Team $team){
        $team->update([
            'status' => '0'
        ]);
        Alert::success('Team '.$team->teamName.' status successfully deactivated!');
        return redirect()->route('team-managements.index');
    }

    public function activate(Team $team){
        $team->update([
            'status' => '1'
        ]);
        Alert::success('Team '.$team->teamName.' status successfully activated!');
        return redirect()->route('team-managements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        if (File::exists($team->logo) && $team->logo != 'images/undefined-user.png'){
            File::delete($team->logo);
        }

        $team->coaches()->detach();
        $team->players()->detach();
        $team->delete();

        return response()->json(['success' => true]);
    }
}
