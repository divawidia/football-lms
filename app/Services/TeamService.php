<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\OpponentTeam;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TeamService extends Service
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $query = Team::with('coaches', 'players')->where('teamSide', 'Academy Team')->get();
        return Datatables::of($query)->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-team', $item->id) . '" method="POST">
                                                    ' . method_field("PATCH") . '
                                                    ' . csrf_field() . '
                                                    <button type="submit" class="dropdown-item">
                                                        <span class="material-icons">block</span> Deactivate Team
                                                    </button>
                                                </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-team', $item->id) . '" method="POST">
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
                                <button type="button" class="dropdown-item delete-team" id="' . $item->id . '">
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
                                            <small class="js-lists-values-email text-50">'.$item->ageGroup.'</small>
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

    public function teamPlayers(Team $team){
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
                                    <button type="button" class="dropdown-item remove-player" id="' . $item->id . '">
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

    public function teamCoaches(Team $team){
        $query = $team->coaches()->get();
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
                                    <a class="dropdown-item" href="' . route('coach-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Coach</a>
                                    <a class="dropdown-item" href="' . route('coach-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Coach</a>
                                    <button type="button" class="dropdown-item remove-coach" id="' . $item->id . '">
                                        <span class="material-icons">delete</span> Remove Coach From Team
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
                                            <small class="js-lists-values-email text-50">' . $item->specializations->name . ' - '.$item->certification->name.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('joinedDate', function ($item) {
                return date('l, M d, Y. h:i A', strtotime($item->pivot->created_at));
            })
            ->editColumn('gender', function ($item) {
                return $item->user->gender;
            })
            ->rawColumns(['action', 'name', 'age', 'gender','joinedDate'])
            ->make();
    }

    public  function store(array $teamData, $academyId){

        if (array_key_exists('logo', $teamData)){
            $teamData['logo'] =$teamData['logo']->store('assets/team-logo', 'public');
        }else{
            $teamData['logo'] = 'images/undefined-user.png';
        }
        $teamData['status'] = '1';
        $teamData['teamSide'] = 'Academy Team';
        $teamData['academyId'] = $academyId;

        $team = Team::create($teamData);

        if (array_key_exists('players', $teamData)){
            $team->players()->attach($teamData['players']);
        }
        if (array_key_exists('coaches', $teamData)){
            $team->coaches()->attach($teamData['coaches']);
        }
        return $team;
    }

    public function update(array $teamData, Team $team): Team
    {
        if (array_key_exists('logo', $teamData)){
            $this->deleteImage($team->logo);
            $teamData['logo'] = $teamData['logo']->store('assets/team-logo', 'public');
        }else{
            $teamData['logo'] = $team->logo;
        }

        $team->update($teamData);

        return $team;
    }

    public function updatePlayerTeam(array $teamData, Team $team): Team
    {
        $team->players()->attach($teamData['players']);
        return $team;
    }

    public function updateCoachTeam(array $teamData, Team $team): Team
    {
        $team->coaches()->sync($teamData['coaches']);
        return $team;
    }

    public function removePlayer(Team $team, Player $player): Team
    {
        $team->players()->detach($player);
        return $team;
    }

    public function removeCoach(Team $team, Coach $coach): Team
    {
        $team->coaches()->detach($coach);
        return $team;
    }

    public function activate(Team $team): Team
    {
        $team->update(['status' => '1']);
        return $team;
    }

    public function deactivate(Team $team): Team
    {
        $team->update(['status' => '0']);
        return $team;
    }

    public function destroy(Team $team): Team
    {
        $this->deleteImage($team->logo);
        $team->coaches()->detach();
        $team->players()->detach();
        $team->delete();
        return $team;
    }
}
