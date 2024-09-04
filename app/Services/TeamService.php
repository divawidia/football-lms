<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\OpponentTeam;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TeamService
{
    private function deleteLogo($logo): void
    {
        if (Storage::disk('public')->exists($logo) && $logo != 'images/undefined-user.png'){
            Storage::disk('public')->delete($logo);
        }
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
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
    public  function store(array $teamData, $academyId){

        if (array_key_exists('logo', $teamData)){
            $teamData['logo'] =$teamData['logo']->store('assets/team-logo', 'public');
        }else{
            $teamData['logo'] = 'images/undefined-user.png';
        }
        $teamData['status'] = '1';
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
            $this->deleteLogo($team->logo);
            $teamData['logo'] = $teamData['logo']->store('assets/team-logo', 'public');
        }else{
            $teamData['logo'] = $team->logo;
        }

        $team->update($teamData);
        $team->players()->sync($teamData['players']);
        $team->coaches()->sync($teamData['coaches']);

        return $team;
    }

    public function updatePlayerTeam(array $teamData, Team $team): Team
    {
        $team->players()->sync($teamData['players']);
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
        $this->deleteLogo($team->logo);
        $team->coaches()->detach();
        $team->players()->detach();
        $team->delete();
        return $team;
    }
}