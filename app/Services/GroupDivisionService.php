<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\GroupDivision;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class GroupDivisionService extends Service
{
    public function index(GroupDivision $groupDivision): JsonResponse
    {
        $query = GroupDivision::with('teams')->find($groupDivision->id);
        return Datatables::of($query->teams)
            ->addColumn('action', function ($item) {
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
                                <button type="button" class="dropdown-item delete-team" id="' . $item->id . '">
                                    <span class="material-icons">delete</span> Remove Team
                                </button>
                              </div>
                            </div>';
            })
            ->editColumn('teams', function ($item) {
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
            ->rawColumns(['action', 'teams'])
            ->make();
    }
    public  function store(array $data, Competition $competition){
        $division = GroupDivision::create([
            'groupName' => $data['groupName'],
            'competitionId' => $competition->id
        ]);

        if (array_key_exists('opponentTeams', $data)){
            $division->teams()->attach($data['opponentTeams']);
        }
        if (array_key_exists('teams', $data)){
            $division->teams()->attach($data['teams']);
        }
        return $division;
    }

    public  function storeTeam(array $data, GroupDivision $groupDivision){
        if (array_key_exists('opponentTeams', $data)){
            $groupDivision->teams()->attach($data['opponentTeams']);
        }
        if (array_key_exists('teams', $data)){
            $groupDivision->teams()->attach($data['teams']);
        }
        return $groupDivision;
    }

    public function removeTeam(GroupDivision $group, Team $team)
    {
        return $group->teams()->detach($team);
    }

    public function update(array $data, GroupDivision $groupDivision): GroupDivision
    {
        $groupDivision->update($data);
        $groupDivision->teams()->sync($data);
        return $groupDivision;
    }

    public function destroy(GroupDivision $groupDivision): GroupDivision
    {
        $groupDivision->teams()->detach();
        $groupDivision->delete();
        return $groupDivision;
    }
}
