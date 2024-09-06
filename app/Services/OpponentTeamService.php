<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class OpponentTeamService extends Service
{
    public function index(): JsonResponse
    {
        $query = Team::where('teamSide','=','Opponent Team')->get();
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
                                <button type="button" class="dropdown-item delete-user" id="' . $item->id . '">
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
                $status = '';
                if ($item->status == '1') {
                    $status = '<span class="badge badge-pill badge-success">Aktif</span>';
                } elseif ($item->status == '0') {
                    $status = '<span class="badge badge-pill badge-danger">Non Aktif</span>';
                }
                return $status;
            })
            ->rawColumns(['action', 'name', 'status'])
            ->make();
    }
    public  function store(array $data){

        if (array_key_exists('logo', $data)){
            $data['logo'] =$data['logo']->store('assets/team-logo', 'public');
        }else{
            $data['logo'] = 'images/undefined-user.png';
        }
        $data['status'] = '1';
        $data['teamSide'] = 'Opponent Team';
        return Team::create($data);
    }

    public function update(array $data, Team $team): Team
    {
        if (array_key_exists('logo', $data)){
            $this->deleteImage($team->logo);
            $data['logo'] = $data['logo']->store('assets/team-logo', 'public');
        }else{
            $data['logo'] = $team->logo;
        }

        $team->update($data);

        return $team;
    }

    public function destroy(Team $team): Team
    {
        $this->deleteImage($team->logo);
        $team->delete();
        return $team;
    }
}
