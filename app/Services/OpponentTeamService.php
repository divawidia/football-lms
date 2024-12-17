<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Team;
use App\Notifications\OpponentTeamsManagements\OpponentTeamCreatedDeleted;
use App\Notifications\OpponentTeamsManagements\OpponentTeamUpdated;
use App\Repository\EventScheduleRepository;
use App\Repository\TeamMatchRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class OpponentTeamService extends Service
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private EventScheduleRepository $eventScheduleRepository;
    private TeamMatchRepository $teamMatchRepository;
    private DatatablesHelper $datatablesService;

    public function __construct(
        TeamRepository          $teamRepository,
        UserRepository          $userRepository,
        EventScheduleRepository $eventScheduleRepository,
        TeamMatchRepository     $teamMatchRepository,
        DatatablesHelper        $datatablesService)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->teamMatchRepository = $teamMatchRepository;
        $this->datatablesService = $datatablesService;
    }
    public function index(): JsonResponse
    {
        $query = $this->teamRepository->getByTeamside('Opponent Team');
        return Datatables::of($query)->addColumn('action', function ($item) {
            if ($item->status == '1') {
                $statusButton = '<button type="submit" class="dropdown-item setDeactivate" id="'.$item->id.'">
                                        <span class="material-icons text-danger">check_circle</span>
                                        Deactivate Admin
                                </button>';
            } else {
                $statusButton = '<button type="submit" class="dropdown-item setActivate" id="'.$item->id.'">
                                        <span class="material-icons text-success">check_circle</span>
                                        Activate Admin
                                </button>';
            }
            return '
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . route('opponentTeam-managements.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Team</a>
                                <a class="dropdown-item" href="' . route('opponentTeam-managements.show', $item->hash) . '"><span class="material-icons">visibility</span> View Team</a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item delete-opponentTeam" id="' . $item->id . '">
                                    <span class="material-icons text-danger">delete</span> Delete Team
                                </button>
                              </div>
                            </div>';
        })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->logo, $item->teamName, $item->ageGroup, route('opponentTeam-managements.show', $item->hash));
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->activeNonactiveStatus($item->status);
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

    public function teamOverviewStats(Team $team)
    {
        $stats = [
            'teamScore',
            'cleanSheets',
            'teamOwnGoal',
        ];
        $results = ['Win', 'Lose', 'Draw'];

        $statsData['matchPlayed'] = $this->eventScheduleRepository->getTeamsMatchPlayed($team, 'Opponent Team');
        $statsData['matchPlayedThisMonth'] = $this->eventScheduleRepository->getTeamsMatchPlayed($team, 'Opponent Team', startDate: Carbon::now()->startOfMonth(), endDate: Carbon::now());

        foreach ($stats as $stat){
            $statsData[$stat] = $this->teamMatchRepository->getTeamsStats($team, 'Opponent Team', stats: $stat);
            $statsData[$stat.'ThisMonth'] = $this->teamMatchRepository->getTeamsStats($team, 'Opponent Team', startDate: Carbon::now()->startOfMonth(), endDate: Carbon::now(), stats: $stat);
        }
        foreach ($results as $result){
            $statsData[$result] = $this->teamMatchRepository->getTeamsStats($team, 'Opponent Team', results: $result);
            $statsData[$result.'ThisMonth'] = $this->teamMatchRepository->getTeamsStats($team, 'Opponent Team', startDate: Carbon::now()->startOfMonth(), endDate: Carbon::now(), results: $result);
        }

        $statsData['goalsConceded'] = $this->teamMatchRepository->getTeamsStats($team, stats: 'teamScore');
        $statsData['goalsConcededThisMonth'] = $this->teamMatchRepository->getTeamsStats($team, startDate: Carbon::now()->startOfMonth(), endDate: Carbon::now(), stats: 'teamScore');

        $statsData['goalsDifference'] = $statsData['teamScore'] - $statsData['goalsConceded'];
        $statsData['goalDifferenceThisMonth'] = $statsData['teamScoreThisMonth'] - $statsData['goalsConcededThisMonth'];

        return $statsData;
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

        $admins = $this->userRepository->getAllAdminUsers();
        $loggedAdminName = $this->getUserFullName($loggedUser);
        Notification::send($admins,new OpponentTeamCreatedDeleted($loggedAdminName, $team, 'deleted'));
        $team->delete();
        return $team;
    }
}
