<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\Team;
use App\Notifications\CoachManagements\CoachAccountCreatedDeleted;
use App\Notifications\CoachManagements\CoachAccountUpdated;
use App\Notifications\PlayerCoachAddToTeam;
use App\Notifications\PlayerCoachRemoveToTeam;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\CoachRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CoachService extends Service
{
    private CoachRepository $coachRepository;
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private CoachMatchStatsRepository $coachMatchStatsRepository;
    private DatatablesService $datatablesService;

    public function __construct(CoachRepository $coachRepository, TeamRepository $teamRepository, UserRepository $userRepository, CoachMatchStatsRepository $coachMatchStatsRepository, DatatablesService $datatablesService)
    {
        $this->coachRepository = $coachRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->coachMatchStatsRepository = $coachMatchStatsRepository;
        $this->datatablesService = $datatablesService;
    }

    public function index(): JsonResponse
    {
        $query = $this->coachRepository->getAll();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if ($item->user->status == '1') {
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
                                <a class="dropdown-item" href="' . route('coach-managements.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Coach Profile</a>
                                <a class="dropdown-item" href="' . route('coach-managements.show', $item->hash) . '"><span class="material-icons">visibility</span> View Coach</a>
                                ' . $statusButton . '
                                <a class="dropdown-item changePassword" id="'.$item->id.'"><span class="material-icons">lock</span> Change Coach Password</a>
                                <button type="button" class="dropdown-item delete-user" id="' . $item->id . '">
                                    <span class="material-icons text-danger">delete</span> Delete Coach
                                </button>
                              </div>
                            </div>';
            })
            ->editColumn('teams', function ($item) {
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
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->specializations->name. ' - '.$item->certification->name, route('coach-managements.show', $item->hash));
            })
            ->editColumn('status', function ($item){
                return $this->datatablesService->activeNonactiveStatus($item->user->status);
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->rawColumns(['action', 'name','status', 'age', 'teams'])
            ->make();
    }

    public function coachTeams(Coach $coach): JsonResponse
    {
        return Datatables::of($coach->teams)
            ->addColumn('action', function ($item) {
                return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="'.route('team-managements.edit', $item->hash).'"><span class="material-icons">edit</span> Edit Team</a>
                            <a class="dropdown-item" href="'.route('team-managements.show', $item->hash).'"><span class="material-icons">visibility</span> View Team</a>
                            <button type="button" class="dropdown-item delete-team" id="' . $item->id . '">
                                <span class="material-icons text-danger">delete</span> Remove coach from Team
                            </button>
                          </div>
                        </div>';
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->logo, $item->teamName, $item->division, route('team-managements.show', $item->hash));
            })
            ->editColumn('date', function ($item){
                return $this->convertToDate($item->pivot->created_at);
            })
            ->rawColumns(['action', 'name','date'])
            ->make();
    }

    public function removeTeam(Coach $coach, Team $team)
    {
        $coach->teams()->detach($team->id);
        $coach->user->notify(new PlayerCoachRemoveToTeam($team));
        return $coach;
    }

    public function updateTeams($teamData, Coach $coach)
    {
        $coach->teams()->attach($teamData);
        $team =$this->teamRepository->find($teamData)->first();
        $coach->user->notify(new PlayerCoachAddToTeam($team));
        return $coach;
    }

    public function create()
    {
        $certifications = $this->coachRepository->getAllCoachCertification();
        $specializations = $this->coachRepository->getAllCoachSpecialization();
        $teams = $this->teamRepository->getByTeamside('Academy Team');

        return compact('certifications', 'specializations', 'teams');
    }

    public  function store(array $data, $academyId, $loggedUser){

        $data['password'] = bcrypt($data['password']);
        $data['foto'] = $this->storeImage($data, 'foto', 'assets/user-profile', 'images/undefined-user.png');
        $data['status'] = '1';
        $data['academyId'] = $academyId;

        $user = $this->userRepository->createUserWithRole($data, 'coach');
        $data['userId'] = $user->id;
        $coach = $this->coachRepository->create($data);

        $superAdminName = $this->getUserFullName($loggedUser);
        Notification::send($this->userRepository->getAllAdminUsers(),new CoachAccountCreatedDeleted($superAdminName, $coach, 'created'));
        return $coach;
    }
    public function show(Coach $coach)
    {
        $fullName = $this->getUserFullName($coach->user);
        $age = $this->getAge($coach->user->dob);
        $teams = $this->teamRepository->getTeamsHaventJoinedByCoach($coach);

        $totalMatchPlayed = $this->coachMatchStatsRepository->totalMatchPlayed($coach);
        $thisMonthTotalMatchPlayed = $this->coachMatchStatsRepository->thisMonthTotalMatchPlayed($coach);

        $totalGoals =  $this->coachMatchStatsRepository->totalGoals($coach);
        $thisMonthTotalGoals = $this->coachMatchStatsRepository->thisMonthTotalGoals($coach);

        $totalGoalsConceded = $this->coachMatchStatsRepository->totalGoalsConceded($coach);
        $thisMonthTotalGoalsConceded = $this->coachMatchStatsRepository->thisMonthTotalGoalsConceded($coach);

        $totalCleanSheets = $this->coachMatchStatsRepository->totalCleanSheets($coach);
        $thisMonthTotalCleanSheets = $this->coachMatchStatsRepository->thisMonthTotalCleanSheets($coach);

        $totalOwnGoals = $this->coachMatchStatsRepository->totalOwnGoals($coach);
        $thisMonthTotalOwnGoals = $this->coachMatchStatsRepository->thisMonthTotalOwnGoals($coach);

        $totalWins = $this->coachMatchStatsRepository->totalWins($coach);
        $thisMonthTotalWins = $this->coachMatchStatsRepository->thisMonthTotalWins($coach);

        $totalLosses = $this->coachMatchStatsRepository->totalLosses($coach);
        $thisMonthTotalLosses = $this->coachMatchStatsRepository->thisMonthTotalLosses($coach);

        $totalDraws = $this->coachMatchStatsRepository->totalDraws($coach);
        $thisMonthTotalDraws = $this->coachMatchStatsRepository->thisMonthTotalDraws($coach);

        $goalsDifference = $totalGoals - $totalGoalsConceded;
        $thisMonthGoalsDifference = $thisMonthTotalGoals - $thisMonthTotalGoalsConceded;

        return compact(
            'fullName',
            'age',
            'teams',
            'totalMatchPlayed',
            'totalGoals',
            'totalGoalsConceded',
            'goalsDifference',
            'totalCleanSheets',
            'totalOwnGoals',
            'totalWins',
            'totalLosses',
            'totalDraws',
            'thisMonthTotalMatchPlayed',
            'thisMonthTotalGoals',
            'thisMonthTotalGoalsConceded',
            'thisMonthGoalsDifference',
            'thisMonthTotalCleanSheets',
            'thisMonthTotalOwnGoals',
            'thisMonthTotalWins',
            'thisMonthTotalLosses',
            'thisMonthTotalDraws',
        );
    }

    public function edit(){
        $certifications = $this->coachRepository->getAllCoachCertification();
        $specializations = $this->coachRepository->getAllCoachSpecialization();
        return compact('certifications', 'specializations');
    }

    public function update(array $data, Coach $coach)
    {
        $data['foto'] = $this->updateImage($data, 'foto', 'user-profile', $coach->user->foto);
        $this->coachRepository->update($coach, $data);
        $coach->user->notify(new CoachAccountUpdated($coach, 'updated'));
        return $coach;
    }

    public function setStatus(Coach $coach, $status)
    {
        $this->userRepository->updateUserStatus($coach, $status);

        if ($status == '1') {
            $message = 'activated';
        } elseif ($status == '0') {
            $message = 'deactivated';
        }

        $coach->user->notify(new CoachAccountUpdated($coach, $message));
        return $coach;
    }

    public function changePassword($data, Coach $coach){
        $this->coachRepository->changePassword($data, $coach);
        $coach->user->notify(new CoachAccountUpdated($coach, 'updated the password'));
        return $coach;
    }

    public function destroy(Coach $coach, $loggedUser)
    {
        $this->deleteImage($coach->user->foto);

        $superAdminName = $this->getUserFullName($loggedUser);
        Notification::send($this->userRepository->getAllAdminUsers(),new CoachAccountCreatedDeleted($superAdminName, $coach, 'deleted'));

        $this->coachRepository->delete($coach);
        return $coach;
    }
}
