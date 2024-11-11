<?php

namespace App\Services;

use App\Models\Team;
use App\Notifications\TeamsManagements\OpponentTeamCreatedDeleted;
use App\Notifications\TeamsManagements\OpponentTeamUpdated;
use App\Notifications\TeamsManagements\TeamCreatedDeleted;
use App\Notifications\TeamsManagements\TeamUpdated;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class OpponentTeamService extends Service
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;

    public function __construct(
        TeamRepository $teamRepository,
        UserRepository $userRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
    }
    public function index(): JsonResponse
    {
        $query = $this->teamRepository->getByTeamside('Opponent Team');
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
                                <a class="dropdown-item" href="' . route('opponentTeam-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Team</a>
                                <a class="dropdown-item" href="' . route('opponentTeam-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Team</a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item delete-opponentTeam" id="' . $item->id . '">
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
    public  function store(array $data, $loggedUser){
        $data['logo'] = $this->storeImage($data, 'logo', 'assets/team-logo', 'images/undefined-user.png');
        $data['status'] = '1';
        $data['teamSide'] = 'Opponent Team';

        $team = $this->teamRepository->create($data);

        $superAdminName = $this->getUserFullName($loggedUser);
        Notification::send($this->userRepository->getAllAdminUsers(),new OpponentTeamCreatedDeleted($superAdminName, $team, 'created'));
        return $team;
    }

    public function update(array $data, Team $team, $loggedUser): Team
    {
        $data['logo'] = $this->updateImage($data, 'logo', 'team-logo', $team->logo);
        $team->update($data);

        $admins = $this->userRepository->getAllAdminUsers();
        $loggedAdminName = $this->getUserFullName($loggedUser);
        Notification::send($admins,new OpponentTeamUpdated($loggedAdminName, $team, 'updated'));

        return $team;
    }

    public function destroy(Team $team, $loggedUser): Team
    {
        $this->deleteImage($team->logo);
        $team->delete();

        $admins = $this->userRepository->getAllAdminUsers();
        $loggedAdminName = $this->getUserFullName($loggedUser);
        Notification::send($admins,new OpponentTeamCreatedDeleted($loggedAdminName, $team, 'deleted'));

        return $team;
    }
}
