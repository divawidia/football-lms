<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Competition;
use App\Models\GroupDivision;
use App\Models\Team;
use App\Notifications\CompetitionManagements\GroupDivisions\GroupDivisionCreatedDeleted;
use App\Notifications\CompetitionManagements\GroupDivisions\GroupDivisionUpdated;
use App\Notifications\CompetitionManagements\TeamJoinedRemovedCompetition;
use App\Repository\CoachRepository;
use App\Repository\GroupDivisionRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class GroupDivisionService extends Service
{
    private GroupDivisionRepository $groupDivisionRepository;
    private UserRepository $userRepository;
    private TeamRepository $teamRepository;
    private PlayerRepository $playerRepository;
    private CoachRepository $coachRepository;
    private DatatablesHelper $datatablesService;

    public function __construct(
        GroupDivisionRepository $groupDivisionRepository,
        UserRepository $userRepository,
        TeamRepository $teamRepository,
        PlayerRepository $playerRepository,
        CoachRepository $coachRepository,
        DatatablesHelper $datatablesService
    )
    {
        $this->groupDivisionRepository = $groupDivisionRepository;
        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;
        $this->playerRepository = $playerRepository;
        $this->coachRepository = $coachRepository;
        $this->datatablesService = $datatablesService;
    }
    public function index(Competition $competition, GroupDivision $groupDivision): JsonResponse
    {
        $query = $groupDivision->teams;
        return Datatables::of($query)
            ->addColumn('action', function ($item) use ($competition, $groupDivision){
                if ($item->teamSide == 'Opponent Team'){
                    $editTeam = '<a class="dropdown-item" href="' . route('opponentTeam-managements.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Team</a>';
                    $showTeam = '<a class="dropdown-item" href="' . route('opponentTeam-managements.show', $item->hash) . '"><span class="material-icons">visibility</span> View Team</a>';
                }else{
                    $editTeam = '<a class="dropdown-item" href="' . route('team-managements.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Team</a>';
                    $showTeam = '<a class="dropdown-item" href="' . route('team-managements.show', $item->hash) . '"><span class="material-icons">visibility</span> View Team</a>';
                }

                if (isAllAdmin()){
                    $actionBtn = '
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                '.$editTeam.'
                                '.$showTeam.'
                                <button type="submit" class="dropdown-item delete-group'.$groupDivision->id.'-team" id="' . $item->id . '">
                                    <span class="material-icons text-danger">delete</span> Remove Team
                                </button>
                              </div>
                            </div>';
                } else{
                    $actionBtn = $this->datatablesService->buttonTooltips(route('team-managements.show', $item->hash), 'View team detail', 'visibility');
                }
                return $actionBtn;
            })
            ->editColumn('teams', function ($item) {
                return $this->datatablesService->name($item->logo, $item->teamName, $item->ageGroup, route('team-managements.show', $item->hash));
            })
            ->rawColumns(['action', 'teams'])
            ->make();
    }

    public function getAll(Competition $competition)
    {
        return $competition->groups;
    }

    public function getTeams(GroupDivision $group, $exceptTeamId = null)
    {
        $ourTeams = $this->teamRepository->getTeamsJoinedGroupDivision($group, 'Academy Team', $exceptTeamId);
        $opponentTeams = $this->teamRepository->getTeamsJoinedGroupDivision($group, 'Opponent Team', $exceptTeamId);
        return compact('ourTeams', 'opponentTeams');
    }

    public function create(Competition $competition)
    {
        $teams = $this->teamRepository->getTeamsHaventJoinedCompetition($competition, 'Academy Team');
        $opponentTeams = $this->teamRepository->getTeamsHaventJoinedCompetition($competition, 'Opponent Team');
        $players = $this->playerRepository->getAll();
        $coaches = $this->coachRepository->getAll();
        return compact('teams', 'opponentTeams', 'players', 'coaches');
    }
    public  function store(array $data, Competition $competition, $loggedUser){
        $data['competitionId'] = $competition->id;
        $division = $this->groupDivisionRepository->create($data);

        Notification::send($this->userRepository->getAllAdminUsers(), new GroupDivisionCreatedDeleted($loggedUser, $division ,$competition, 'created'));

        $this->storeTeam($data, $division, $competition);

        return $division;
    }

    public  function storeTeam(array $data, GroupDivision $groupDivision, Competition $competition){
        if (array_key_exists('opponentTeams', $data)){
            $groupDivision->teams()->attach($data['opponentTeams']);
        }
        if (array_key_exists('teams', $data)){
            $groupDivision->teams()->attach($data['teams']);
            $teams = $this->teamRepository->getInArray($data['teams']);

            foreach ($teams as $team){
                $teamParticipants = $this->userRepository->allTeamsParticipant($team);
                Notification::send($teamParticipants,new TeamJoinedRemovedCompetition($team, $competition, 'joined'));
            }
        }
        return $groupDivision;
    }

    public function addTeam(Competition $competition, GroupDivision $group)
    {
        $availableAcademyTeams = $this->teamRepository->getTeamsHaventJoinedCompetition($competition, 'Academy Team');
        $opponentTeams = $this->teamRepository->getTeamsHaventJoinedCompetition($competition, 'Opponent Team');
        $teams = $this->teamRepository->getTeamsJoinedGroupDivision($group, 'Academy Team');
        $players = $this->playerRepository->getAll();
        $coaches = $this->coachRepository->getAll();
        return compact('teams', 'opponentTeams', 'availableAcademyTeams','players', 'coaches');
    }

    public function removeTeam(GroupDivision $group, Team $team)
    {
        $competition = $group->competition;
        $group->teams()->detach($team);
        $teamParticipants = $this->userRepository->allTeamsParticipant($team);
        Notification::send($teamParticipants,new TeamJoinedRemovedCompetition($team, $competition, 'removed from'));
        return $team;
    }

    public function update(array $data, GroupDivision $groupDivision, $loggedUser): GroupDivision
    {
        $competition = $groupDivision->competition;
        $groupDivision->update($data);
        $admin = $this->userRepository->getAllAdminUsers();
        Notification::send($admin, new GroupDivisionUpdated($loggedUser, $competition, $groupDivision,'updated'));
        return $groupDivision;
    }

    public function destroy(GroupDivision $groupDivision, $loggedUser): GroupDivision
    {
        $competition = $groupDivision->competition;
        $teams = $this->teamRepository->getJoinedCompetition($competition);

        $groupDivision->teams()->detach();
        foreach ($teams as $team) {
            $teamParticipants = $this->userRepository->allTeamsParticipant($team);
            Notification::send($teamParticipants,new TeamJoinedRemovedCompetition($team, $competition, 'removed from'));
            Notification::send($teamParticipants, new GroupDivisionCreatedDeleted($loggedUser, $groupDivision, $competition, 'deleted'));
        }
        $groupDivision->delete();
        return $groupDivision;
    }
}
