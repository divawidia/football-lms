<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Competition;
use App\Notifications\CompetitionManagements\CompetitionCreatedDeleted;
use App\Notifications\CompetitionManagements\CompetitionStatus;
use App\Notifications\CompetitionManagements\CompetitionUpdated;
use App\Repository\CoachRepository;
use App\Repository\CompetitionRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class CompetitionService extends Service
{
    private CompetitionRepository $competitionRepository;
    private TeamRepository $teamRepository;
    private PlayerRepository $playerRepository;
    private CoachRepository $coachRepository;
    private UserRepository $userRepository;
    private MatchService $eventScheduleService;
    private DatatablesHelper $datatablesHelper;

    public function __construct(
        CompetitionRepository $competitionRepository,
        TeamRepository        $teamRepository,
        PlayerRepository      $playerRepository,
        CoachRepository       $coachRepository,
        UserRepository        $userRepository,
        MatchService          $eventScheduleService,
        DatatablesHelper      $datatablesHelper
    )
    {
        $this->competitionRepository = $competitionRepository;
        $this->teamRepository = $teamRepository;
        $this->playerRepository = $playerRepository;
        $this->coachRepository = $coachRepository;
        $this->userRepository = $userRepository;
        $this->eventScheduleService = $eventScheduleService;
        $this->datatablesHelper = $datatablesHelper;
    }
    public function index(){
        return $this->competitionRepository->getAll();
    }
    public function getActiveCompetition()
    {
        return $this->competitionRepository->getAll(status: 'Ongoing');
    }

    public function modelTeamsCompetition($model, $status = null){
        $teams = $model->teams;
        if ($status){
            $data = $this->competitionRepository->getAll($teams, $status);
        }else{
            $data = $this->competitionRepository->getAll($teams);
        }
        return $data;
    }
    public function datatables(): JsonResponse
    {
        $query = $this->index();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('competition-managements.show', $item->hash), icon: 'visibility', btnText: 'View competition Profile');
                $dropdownItem .= $this->datatablesHelper->linkDropdownItem(route: route('competition-managements.edit', $item->hash), icon: 'edit', btnText: 'edit competition Profile');
                if ($item->status != 'Cancelled' && $item->status != 'Completed') {
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('cancelBtn', $item->hash, 'danger', icon: 'block', btnText: 'Cancel Competition');
                } elseif ($item->status == 'Cancelled') {
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('scheduled-btn', $item->hash, 'warning', icon: 'block', btnText: 'Set Competition to scheduled');
                }
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete', $item->hash, 'danger', icon: 'delete', btnText: 'delete Competition');

                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->logo, $item->name, $item->type, route('competition-managements.show', $item->hash));
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesHelper->competitionStartEndDate($item);
            })
            ->editColumn('isInternal', function ($item) {
                if ($item->isInternal == 1){
                    return '<span class="badge badge-pill badge-primary">Internal</span>';
                }else{
                    return '<span class="badge badge-pill badge-warning">External</span>';
                }
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->rawColumns(['action', 'name', 'isInternal', 'date', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function competitionMatches(Competition $competition)
    {
        $data = $competition->matches()->with('teams', 'externalTeam')->get();
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('match-schedules.show', $item->hash), icon: 'visibility', btnText: 'View match session');
                if (isAllAdmin()){
                    if ($item->status == 'Scheduled'){
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('edit-match-btn', $item->id, icon: 'edit', btnText: 'Edit Match');
                    }
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete-match', $item->hash, 'danger', icon: 'delete', btnText: 'delete Match');
                }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('homeTeam', function ($item) {
                return $this->datatablesHelper->name($item->homeTeam->logo, $item->homeTeam->teamName, $item->homeTeam->ageGroup, route('team-managements.show', $item->homeTeam->hash));
            })
            ->editColumn('awayTeam', function ($item) use ($competition) {
                return ($competition->isInternal == 1)
                    ? $this->datatablesHelper->name($item->awayTeam->logo, $item->awayTeam->teamName, $item->awayTeam->ageGroup, route('team-managements.show', $item->awayTeam->hash))
                    : $item->externalTeam->teamName;
            })
            ->editColumn('score', function ($item) use ($competition) {
                $homeTeam = $item->teams()->where('teamId', $item->homeTeamId)->first();
                if ($competition->isInternal == 1) {
                    $awayTeam = $item->teams()->where('teamId', $item->awayTeamId)->first();
                    return '<p class="mb-0"><strong class="js-lists-values-lead">' .$homeTeam->pivot->teamScore . ' - ' . $awayTeam->pivot->teamScore.'</strong></p>';
                } else {
                    return '<p class="mb-0"><strong class="js-lists-values-lead">' .$homeTeam->pivot->teamScore . ' - ' . $item->externalTeam->teamScore.'</strong></p>';
                }
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesHelper->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->rawColumns(['action','homeTeam', 'awayTeam', 'score', 'status','date'])
            ->addIndexColumn()
            ->make();
    }

    public function create()
    {
        $teams = $this->teamRepository->getByTeamside('Academy Team');
        $opponentTeams = $this->teamRepository->getByTeamside('Opponent Team');
        $players = $this->playerRepository->getAll();
        $coaches = $this->coachRepository->getAll();
        return compact('teams', 'opponentTeams', 'players', 'coaches');
    }
    public  function store(array $competitionData, $loggedUser)
    {
        $competitionData['userId'] = $loggedUser->id;
        $competitionData['logo'] = $this->storeImage($competitionData, 'logo', 'assets/competition-logo', 'images/undefined-user.png');
        $competition = $this->competitionRepository->create($competitionData);

        $admins = $this->userRepository->getAllAdminUsers();
        Notification::send($admins, new CompetitionCreatedDeleted($loggedUser, $competition, 'created'));

        return $competition;
    }

    public  function storeMatch(array $competitionData, Competition $competition, $loggedUser)
    {
        ($competition->isInternal == 1) ? $competitionData['matchType'] = 'Internal Match' : $competitionData['matchType'] = 'External Match';

        $competitionData['competitionId'] = $competition->id;
        $this->eventScheduleService->storeMatch($competitionData, $loggedUser->id);
        return $competition;
    }

    public function update(array $competitionData, Competition $competition, $loggedUser): Competition
    {
        $competitionData['logo'] = $this->updateImage($competitionData, 'logo', 'competition-logo', $competition->logo);

        if ($competitionData['startDate'] > $this->getNowDate()){
            $competitionData['status'] = 'Scheduled';
        }

        $competition->update($competitionData);
        Notification::send($this->userRepository->getAllAdminUsers(), new CompetitionUpdated($loggedUser, $competition, 'updated'));
        return $competition;
    }

    public function setStatus(Competition $competition, $status)
    {
        $competition->update(['status' => $status]);

        // Define status messages mapping
        $statusMessages = [
            'Ongoing' => 'is now competing',
            'Completed' => 'have been completed',
            'Cancelled' => 'have been cancelled',
            'Scheduled' => 'have been set to scheduled',
        ];

        // Check if the status exists in the defined mapping
        if (array_key_exists($status, $statusMessages)) {
            $statusMessage = $statusMessages[$status];
            Notification::send($this->userRepository->getAllAdminUsers(), new CompetitionStatus($competition, $statusMessage));
        }

        return $competition;
    }

    public function destroy(Competition $competition, $loggedUser): Competition
    {
        Notification::send($this->userRepository->getAllAdminUsers(), new CompetitionCreatedDeleted($loggedUser, $competition, 'deleted'));
        $this->deleteImage($competition->logo);
        $competition->delete();
        return $competition;
    }
}
