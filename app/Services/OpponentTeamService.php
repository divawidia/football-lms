<?php

namespace App\Services;

use App\Models\OpponentTeam;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class OpponentTeamService
{
    private function deleteLogo($logo): void
    {
        if (Storage::disk('public')->exists($logo) && $logo != 'images/undefined-user.png'){
            Storage::disk('public')->delete($logo);
        }
    }
    public function index(): \Illuminate\Http\JsonResponse
    {
            $query = OpponentTeam::all();
            return Datatables::of($query)->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-opponentTeam', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="dropdown-item">
                                            <span class="material-icons">block</span> Deactivate Team
                                        </button>
                                    </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-opponentTeam', $item->id) . '" method="POST">
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
                                <a class="dropdown-item" href="' . route('opponentTeam-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Team</a>
                                <a class="dropdown-item" href="' . route('opponentTeam-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Team</a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item delete-team" id="' . $item->id . '">
                                    <span class="material-icons">delete</span> Delete Team
                                </button>
                              </div>
                            </div>';
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
                ->editColumn('players', function ($item) {
                    return $item->totalPlayers . ' Player(s)';
                })
                ->rawColumns(['action', 'name', 'status', 'players'])
                ->make();
    }
    public  function store(array $opponentTeamData){

        if (array_key_exists('logo', $opponentTeamData)){
            $opponentTeamData['logo'] =$opponentTeamData['logo']->store('assets/team-logo', 'public');
        }else{
            $opponentTeamData['logo'] = 'images/undefined-user.png';
        }
        $opponentTeamData['status'] = '1';
        return OpponentTeam::create($opponentTeamData);
    }

    public function update(array $opponentTeamData, OpponentTeam $opponentTeam): OpponentTeam
    {
        if (array_key_exists('logo', $opponentTeamData)){
            $this->deleteLogo($opponentTeam->logo);
            $opponentTeamData['logo'] = $opponentTeamData['logo']->store('assets/team-logo', 'public');
        }else{
            $opponentTeamData['logo'] = $opponentTeam->logo;
        }

        $opponentTeam->update($opponentTeamData);

        return $opponentTeam;
    }

    public function activate(OpponentTeam $opponentTeam): OpponentTeam
    {
        $opponentTeam->update(['status' => '1']);
        return $opponentTeam;
    }

    public function deactivate(OpponentTeam $opponentTeam): OpponentTeam
    {
        $opponentTeam->update(['status' => '0']);
        return $opponentTeam;
    }

    public function destroy(OpponentTeam $opponentTeam): OpponentTeam
    {
        $this->deleteLogo($opponentTeam->logo);
        $opponentTeam->delete();
        return $opponentTeam;
    }
}
