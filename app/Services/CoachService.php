<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\Team;
use App\Notifications\CoachManagements\Admin\AddTeamForAdminNotification;
use App\Notifications\CoachManagements\Admin\CoachActivatedForAdminNotification;
use App\Notifications\CoachManagements\Admin\CoachChangePasswordForAdminNotification;
use App\Notifications\CoachManagements\Admin\CoachCreatedForAdminNotification;
use App\Notifications\CoachManagements\Admin\CoachDeactivatedForAdminNotification;
use App\Notifications\CoachManagements\Admin\CoachDeletedForAdminNotification;
use App\Notifications\CoachManagements\Admin\CoachUpdatedForAdminNotification;
use App\Notifications\CoachManagements\Admin\RemoveTeamForAdminNotification;
use App\Notifications\CoachManagements\Coach\AddTeamForCoachNotification;
use App\Notifications\CoachManagements\Coach\CoachActivatedForCoachNotification;
use App\Notifications\CoachManagements\Coach\CoachChangePasswordForCoachNotification;
use App\Notifications\CoachManagements\Coach\CoachCreatedForCoachNotification;
use App\Notifications\CoachManagements\Coach\CoachDeactivatedForCoachNotification;
use App\Notifications\CoachManagements\Coach\CoachUpdatedForCoachNotification;
use App\Notifications\CoachManagements\Coach\RemoveTeamForCoachNotification;
use App\Repository\Interface\CoachMatchStatsRepositoryInterface;
use App\Repository\Interface\CoachRepositoryInterface;
use App\Repository\Interface\TeamRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class CoachService extends Service
{
    private CoachRepositoryInterface $coachRepository;
    private TeamRepositoryInterface $teamRepository;
    private UserRepositoryInterface $userRepository;
    private CoachMatchStatsRepositoryInterface $coachMatchStatsRepository;
    private DatatablesHelper $datatablesHelper;

    public function __construct(
        CoachRepositoryInterface $coachRepository,
        TeamRepositoryInterface $teamRepository,
        UserRepositoryInterface $userRepository,
        CoachMatchStatsRepositoryInterface $coachMatchStatsRepository,
        DatatablesHelper $datatablesHelper
    )
    {
        $this->coachRepository = $coachRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->coachMatchStatsRepository = $coachMatchStatsRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function index($certification, $specializations, $team, $status)
    {
        $query = $this->coachRepository->getAll(['user', 'teams', 'specialization', 'certification'], $certification, $specializations, $team, $status);

        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                return $this->indexActionButton($item);
            })
            ->addColumn('teams', function ($item) {
                return $this->datatablesHelper->usersTeams($item);
            })
            ->addColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), "{$item->specialization->name} - {$item->certification->name}", route('coach-managements.show', $item->hash));
            })
            ->editColumn('status', function ($item){
                return $this->datatablesHelper->activeNonactiveStatus($item->user->status);
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->rawColumns(['action', 'name','status', 'teams'])
            ->addIndexColumn()
            ->make();
    }

    public function indexActionButton(Coach $coach)
    {
        $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('coach-managements.show', $coach->hash), icon: 'visibility', btnText: 'View coach Profile');
        $dropdownItem .= $this->datatablesHelper->linkDropdownItem(route: route('coach-managements.edit', $coach->hash), icon: 'edit', btnText: 'edit coach Profile');
        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('changePassword', $coach->hash, icon: 'lock', btnText: 'Change coach Account Password');
        ($coach->user->status == '1')
            ? $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setDeactivate', $coach->hash, 'danger', icon: 'check_circle', btnText: 'Deactivate coach Account')
            : $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setActivate', $coach->hash, 'success', icon: 'check_circle', btnText: 'Activate coach Account');
        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete-user', $coach->hash, 'danger', icon: 'delete', btnText: 'delete coach Account');

        return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
            return $dropdownItem;
        });
    }

    public function coachTeams(Coach $coach): JsonResponse
    {
        return Datatables::of($coach->teams)
            ->addColumn('action', function ($item) {
                return $this->coachTeamsActionButton($item);
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->logo, $item->teamName, $item->division, route('team-managements.show', $item->hash));
            })
            ->editColumn('date', function ($item){
                return $this->convertToDate($item->pivot->created_at);
            })
            ->rawColumns(['action', 'name'])
            ->make();
    }

    public function coachTeamsActionButton(Team $team)
    {
        $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('team-managements.show', $team->hash), icon: 'visibility', btnText: 'View team Profile');
        $dropdownItem .= $this->datatablesHelper->linkDropdownItem(route: route('team-managements.edit', $team->hash), icon: 'edit', btnText: 'edit team Profile');
        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete-team', $team->hash, 'danger', icon: 'delete', btnText: 'delete team');
        return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
            return $dropdownItem;
        });
    }


    public function removeTeam(Coach $coach, Team $team, $loggedUser)
    {
        $coach->user->notify(new RemoveTeamForCoachNotification($team));
        Notification::send($this->userRepository->getAllAdminUsers(),new RemoveTeamForAdminNotification($loggedUser, $team, $coach));
        return $coach->teams()->detach($team->id);
    }

    public function updateTeams($teamData, Coach $coach, $loggedUser)
    {
        $coach->teams()->attach($teamData);
        $team =$this->teamRepository->find($teamData)->first();
        $coach->user->notify(new AddTeamForCoachNotification($team));
        Notification::send($this->userRepository->getAllAdminUsers(),new AddTeamForAdminNotification($loggedUser, $team, $coach));
        return $coach;
    }

    public function getCoachCert()
    {
        return $this->coachRepository->getAllCoachCertification();
    }
    public function getCoachSpecializations()
    {
        return $this->coachRepository->getAllCoachSpecialization();
    }

    public  function store(array $data, $academyId, $loggedUser){

        $data['password'] = bcrypt($data['password']);
        $data['foto'] = $this->storeImage($data, 'foto', 'assets/user-profile', 'images/undefined-user.png');
        $data['status'] = '1';
        $data['academyId'] = $academyId;

        $user = $this->userRepository->createUserWithRole($data, 'coach');
        $data['userId'] = $user->id;
        $coach = $this->coachRepository->create($data);

        try {
            Notification::send($this->userRepository->getAllAdminUsers(),new CoachCreatedForAdminNotification($loggedUser, $coach));
            $coach->user->notify(new CoachCreatedForCoachNotification());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        return $coach;
    }

    public function getTeamsHaventJoinedByCoach(Coach $coach)
    {
        return $this->teamRepository->getAll(exceptCoach: $coach);
    }

    public function totalMatchPlayed(Coach $coach, $startDate = null, $endDate = null)
    {
        return $this->coachMatchStatsRepository->getAll($coach, $startDate, $endDate, matchPlayed: true);
    }

    public function totalGoals(Coach $coach, $startDate = null, $endDate = null)
    {
        return $this->coachMatchStatsRepository->getAll($coach, $startDate, $endDate, retrievalMethod: 'sum', column: 'goalScored');
    }

    public function goalConceded(Coach $coach, $startDate = null, $endDate = null)
    {
        return $this->coachMatchStatsRepository->getAll($coach, $startDate, $endDate, retrievalMethod: 'sum', column: 'goalConceded');
    }

    public function winRate(Coach $coach, $startDate = null, $endDate = null)
    {
        $totalMatch = $this->totalMatchPlayed($coach, $startDate, $endDate);
        $wins = $this->wins($coach, $startDate, $endDate);

        ($totalMatch > 0) ? $winRate = ( $wins /$totalMatch) * 100 : $winRate = 0; // check if totalMatch is 0 then set win rate to 0
        return round($winRate, 2);
    }

    public function cleanSheets(Coach $coach, $startDate = null, $endDate = null)
    {
        return $this->coachMatchStatsRepository->getAll($coach, $startDate, $endDate, retrievalMethod: 'sum', column: 'cleanSheets');
    }

    public function wins(Coach $coach, $startDate = null, $endDate = null)
    {
        return $this->coachMatchStatsRepository->getAll($coach, $startDate, $endDate, 'Win');
    }

    public function lose(Coach $coach, $startDate = null, $endDate = null)
    {
        return $this->coachMatchStatsRepository->getAll($coach, $startDate, $endDate, 'Lose');
    }

    public function draw(Coach $coach, $startDate = null, $endDate = null)
    {
        return $this->coachMatchStatsRepository->getAll($coach, $startDate, $endDate, 'Draw');
    }

    public function goalsDifference(Coach $coach, $startDate = null, $endDate = null)
    {
        $goalScored = $this->totalGoals($coach, $startDate, $endDate);
        $goalConceded = $this->goalConceded($coach, $startDate, $endDate);
        return $goalScored - $goalConceded;
    }

    public function update(array $data, Coach $coach, $loggedUser): bool
    {
        $data['foto'] = $this->updateImage($data, 'foto', 'user-profile', $coach->user->foto);

        Notification::send($this->userRepository->getAllAdminUsers(),new CoachUpdatedForAdminNotification($loggedUser, $coach));
        $coach->user->notify(new CoachUpdatedForCoachNotification());

        return $coach->update($data);
    }

    public function setStatus(Coach $coach, $status, $loggedUser)
    {
        $this->userRepository->updateUserStatus($coach, $status);

        if ($status == '1') {
            Notification::send($this->userRepository->getAllAdminUsers(),new CoachActivatedForAdminNotification($loggedUser, $coach));
            $coach->user->notify(new CoachActivatedForCoachNotification());
        } else {
            Notification::send($this->userRepository->getAllAdminUsers(),new CoachDeactivatedForAdminNotification($loggedUser, $coach));
            $coach->user->notify(new CoachDeactivatedForCoachNotification());
        }
        return $coach;
    }

    public function changePassword($data, Coach $coach, $loggedUser){
        Notification::send($this->userRepository->getAllAdminUsers(),new CoachChangePasswordForAdminNotification($loggedUser, $coach));
        $coach->user->notify(new CoachChangePasswordForCoachNotification());

        return $this->userRepository->changePassword($data, $coach);
    }

    public function destroy(Coach $coach, $loggedUser)
    {
        $this->deleteImage($coach->user->foto);
        Notification::send($this->userRepository->getAllAdminUsers(),new CoachDeletedForAdminNotification($loggedUser, $coach));
        return $coach->delete();
    }
}
