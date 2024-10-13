<?php

namespace App\Services\Coach;

use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use App\Services\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use Yajra\DataTables\Facades\DataTables;

class PlayerService extends CoachService
{
    private $coach;
    public function __construct($coach){
        $this->coach = $coach;
    }

    public function index(): JsonResponse
    {
        $query = Player::with('user', 'teams', 'position')
            ->whereHas('teams', function($q) {
                foreach ($this->managedTeams($this->coach) as $team){
                    $q->orWhere('teamId', $team->id);
                }
            })->get();

        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                return '
                      <a class="btn btn-sm btn-outline-secondary" href="' . route('player-managements.show', $item->userId) . '" data-toggle="tooltips" data-placement="bottom" title="View Player">
                        <span class="material-icons">
                            visibility
                        </span>
                      </a>';
            })
            ->editColumn('teams.name', function ($item) {
                $playerTeam = '';
                if(count($item->teams) === 0){
                    $playerTeam = 'No Team';
                }else{
                    foreach ($item->teams as $team){
                        $playerTeam .= '<span class="badge badge-pill badge-danger">'.$team->teamName.'</span>';
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
                                        <a href="' . route('player-managements.show', $item->userId) . '">
                                            <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        </a>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->editColumn('status', function ($item){
                if ($item->user->status == '1') {
                    return '<span class="badge badge-pill badge-success">Aktif</span>';
                }elseif ($item->user->status == '0'){
                    return '<span class="badge badge-pill badge-danger">Non Aktif</span>';
                }
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->rawColumns(['action', 'name','status', 'age', 'teams.name'])
            ->make();
    }
}
