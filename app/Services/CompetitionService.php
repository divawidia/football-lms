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
    private GroupDivisionService $groupDivisionService;
    private EventScheduleService $eventScheduleService;
    private DatatablesHelper $datatablesService;

    public function __construct(
        CompetitionRepository $competitionRepository,
        TeamRepository $teamRepository,
        PlayerRepository $playerRepository,
        CoachRepository $coachRepository,
        UserRepository $userRepository,
        GroupDivisionService $groupDivisionService,
        EventScheduleService $eventScheduleService,
        DatatablesHelper $datatablesService
    )
    {
        $this->competitionRepository = $competitionRepository;
        $this->teamRepository = $teamRepository;
        $this->playerRepository = $playerRepository;
        $this->coachRepository = $coachRepository;
        $this->userRepository = $userRepository;
        $this->groupDivisionService = $groupDivisionService;
        $this->eventScheduleService = $eventScheduleService;
        $this->datatablesService = $datatablesService;
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
                $statusButton = '';
                if ($item->status != 'Cancelled' && $item->status != 'Completed') {
                    $statusButton = '<button type="submit" class="dropdown-item cancelBtn" id="'.$item->id.'">
                                        <span class="material-icons text-danger">block</span> Cancel Competition
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
                                <a class="dropdown-item" href="' . route('competition-managements.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Competition</a>
                                <a class="dropdown-item" href="' . route('competition-managements.show', $item->hash) . '"><span class="material-icons">visibility</span> View Competition</a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                    <span class="material-icons text-danger">delete</span> Delete Competition
                                </button>
                              </div>
                            </div>';
            })
            ->editColumn('divisions', function ($item) {
                $divisions = '';
                if (count($item->groups) == 0){
                    $divisions = 'No division added in this competition';
                }else{
                    foreach ($item->groups as $group) {
                        $divisions .= '<span class="badge badge-pill badge-danger">'.$group->groupName.'</span>';
                    }
                }
                return $divisions;
            })
            ->editColumn('teams', function ($item) {
                $teams = '';
                foreach ($item->groups as $group){
                    if (count($group->teams) > 0) {
                        $team = $group->teams->where('teamSide', 'Academy Team')->first();
                        $teams .= '<span class="badge badge-pill badge-danger">' . $team->teamName . '</span>';
                    }
                }
                return $teams;
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->logo, $item->name, $item->type, route('competition-managements.show', $item->hash));
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesService->competitionStartEndDate($item);
            })
            ->editColumn('contact', function ($item) {
                if ($item->contactName != null && $item->contactPhone != null){
                    $contact = $item->contactName. ' ~ '.$item->contactPhone;
                }else{
                    $contact = 'No cantact added';
                }
                return $contact;
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->eventStatus($item->status);
            })
            ->rawColumns(['action', 'name', 'teams', 'divisions', 'date', 'contact', 'status'])
            ->make();
    }

    public function overviewStats(Competition $competition)
    {
        $groupDivisions = $competition->groups;
        $totalGroups = $groupDivisions->count();
        $totalMatch = $competition->matches()->count();
        $totalTeams = 0;
        $ourTeamsWins = 0;
        $ourTeamsDraws = 0;
        $ourTeamsLosses = 0;
        foreach ($groupDivisions as $group){
            $totalTeams += $group->teams()->count();

            $ourTeamsWins += $group->teams()->where('teamSide', 'Academy Team')->sum('competition_team.won');
            $ourTeamsDraws += $group->teams()->where('teamSide', 'Academy Team')->sum('competition_team.drawn');
            $ourTeamsLosses += $group->teams()->where('teamSide', 'Academy Team')->sum('competition_team.lost');
        }

        return compact('totalTeams', 'ourTeamsWins', 'ourTeamsDraws', 'ourTeamsLosses', 'totalGroups', 'totalMatch');

    }
    public function competitionMatches(Competition $competition)
    {
        $data = $competition->matches;
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if (isAllAdmin()){
                    $actionBtn ='
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('match-schedules.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Match</a>
                            <a class="dropdown-item" href="' . route('match-schedules.show', $item->hash) . '"><span class="material-icons">visibility</span> View Match</a>
                            <button type="button" class="dropdown-item delete-match" id="' . $item->id . '">
                                <span class="material-icons text-danger">delete</span> Delete Match
                            </button>
                          </div>
                        </div>';
                } else {
                    $actionBtn = $this->datatablesService->buttonTooltips(route('match-schedules.show', $item->hash), 'View match session', 'visibility');
                }
                return $actionBtn;
            })
            ->editColumn('team', function ($item) {
                return $this->datatablesService->name($item->teams[0]->logo, $item->teams[0]->teamName, $item->teams[0]->ageGroup, route('team-managements.show', $item->teams[0]->hash));
            })
            ->editColumn('opponentTeam', function ($item) {
                return $this->datatablesService->name($item->teams[1]->logo, $item->teams[1]->teamName, $item->teams[1]->ageGroup, route('team-managements.show', $item->teams[1]->hash));
            })
            ->editColumn('score', function ($item){
                return '<p class="mb-0"><strong class="js-lists-values-lead">' .$item->teams[0]->pivot->teamScore . ' - ' . $item->teams[1]->pivot->teamScore.'</strong></p>';
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesService->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->eventStatus($item->status);
            })
            ->rawColumns(['action','team', 'score', 'status','opponentTeam','date'])
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
        $competitionData['logo'] = $this->storeImage($competitionData, 'logo', 'assets/competition-logo', 'images/undefined-user.png');
        $competition = $this->competitionRepository->create($competitionData);

        $admins = $this->userRepository->getAllAdminUsers();
        Notification::send($admins, new CompetitionCreatedDeleted($loggedUser, $competition, 'created'));

//        $competitionData['competitionId'] = $competition->id;
//        $this->groupDivisionService->store($competitionData, $competition, $loggedUser);

        return $competition;
    }

    public  function storeMatch(array $competitionData, Competition $competition, $loggedUser)
    {
        $competitionData['matchType'] = 'Competition';
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

    public function setStatus(Competition $competition, $status): Competition
    {
        $competition->update(['status' => $status]);
        $teams = $this->teamRepository->getJoinedCompetition($competition);

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

            foreach ($teams as $team) {
                $teamParticipants = $this->userRepository->allTeamsParticipant($team);
                Notification::send($teamParticipants, new CompetitionStatus($competition, $team, $statusMessage));
            }
        }

        return $competition;
    }

    public function destroy(Competition $competition, $loggedUser): Competition
    {
        $teams = $this->teamRepository->getJoinedCompetition($competition);
        foreach ($teams as $team) {
            $teamParticipants = $this->userRepository->allTeamsParticipant($team);
            Notification::send($teamParticipants, new CompetitionCreatedDeleted($loggedUser, $competition, 'deleted'));
        }
        $this->deleteImage($competition->logo);
        $competition->delete();
        return $competition;
    }
}
