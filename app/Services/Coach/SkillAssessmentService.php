<?php

namespace App\Services\Coach;

use App\Models\Player;
use App\Services\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SkillAssessmentService extends Service
{
    // retrieve player data based on coach managed teams
    public function index($coach): JsonResponse
    {
        $teams = $this->coachManagedTeams($coach);

        // query player data that included in teams that managed by logged in coach
        $query = Player::with('user', 'teams', 'position', 'playerSkillStats')
            ->whereHas('teams', function ($q) use ($teams) {
                $q->where('teamId', $teams[0]->id);

                // if teams are more than 1 then iterate more
                if (count($teams) > 1) {
                    for ($i = 1; $i < count($teams); $i++) {
                        $q->orWhere('teamId', $teams[$i]->id);
                    }
                }
            })->get();

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
                            <a class="dropdown-item" href="' . route('coach.skill-assessments.skill-stats', $item->id) . '"><span class="material-icons">edit</span> View Skill Player</a>
                            <a class="dropdown-item" href="' . route('player-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> Update Player Skill</a>
                            <button type="button" class="dropdown-item add-performance-review" id="' . $item->userId . '">
                                <span class="material-icons">add</span>
                                Add Performance Review
                            </button>
                          </div>
                        </div>';
            })
            ->editColumn('teams.name', function ($item) {
                $playerTeam = '';
                if (count($item->teams) === 0) {
                    $playerTeam = 'No Team';
                } else {
                    foreach ($item->teams as $team) {
                        $playerTeam .= '<span class="badge badge-pill badge-danger">' . $team->teamName . '</span>';
                    }
                }
                return $playerTeam;
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
                                        <a href="' . route('coach.skill-assessments.skill-stats', $item->id) . '">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' ' . $item->user->lastName . '</strong></p>
                                        </a>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->editColumn('age', function ($item) {
                return $this->getAge($item->user->dob);
            })
            ->editColumn('lastUpdated', function ($item) {
                $data = $item->playerSkillStats()->latest()->first();
                if ($data){
                    $date = $this->convertToDatetime($data->created_at);
                } else{
                    $date = "Haven't assessed yet";
                }
                return $date;
            })
            ->rawColumns(['action', 'name', 'lastUpdated', 'age', 'teams.name'])
            ->addIndexColumn()
            ->make();
    }
}
