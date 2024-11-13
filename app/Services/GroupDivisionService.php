<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\GroupDivision;
use App\Models\Team;
use App\Notifications\CompetitionManagements\CompetitionCreatedDeleted;
use App\Notifications\CompetitionManagements\GroupDivisions\GroupDivisionCreatedDeleted;
use App\Notifications\CompetitionManagements\GroupDivisions\GroupDivisionUpdated;
use App\Notifications\CompetitionManagements\TeamJoinedRemovedCompetition;
use App\Repository\GroupDivisionRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class GroupDivisionService extends Service
{
    private GroupDivisionRepository $groupDivisionRepository;
    private UserRepository $userRepository;
    private TeamRepository $teamRepository;

    public function __construct(
        GroupDivisionRepository $groupDivisionRepository,
        UserRepository $userRepository,
        TeamRepository $teamRepository
    )
    {
        $this->groupDivisionRepository = $groupDivisionRepository;
        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;
    }
    public function index(Competition $competition, GroupDivision $groupDivision): JsonResponse
    {
        $query = GroupDivision::with('teams')->find($groupDivision->id);
        return Datatables::of($query->teams)
            ->addColumn('action', function ($item) use ($competition) {
                if ($item->teamSide == 'Opponent Team'){
                    $editTeam = '<a class="dropdown-item" href="' . route('opponentTeam-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Team</a>';
                    $showTeam = '<a class="dropdown-item" href="' . route('opponentTeam-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Team</a>';
                }else{
                    $editTeam = '<a class="dropdown-item" href="' . route('team-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Team</a>';
                    $showTeam = '<a class="dropdown-item" href="' . route('team-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Team</a>';
                }

                if (isAllAdmin()){
                    $actionBtn = '
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                '.$editTeam.'
                                '.$showTeam.'
                                <form action="'.route('division-managements.removeTeam', ['competition' => $competition->id,'group'=>$item->pivot->divisionId, 'team'=>$item->id]).'" method="POST">
                                    '.method_field("PUT").'
                                    '.csrf_field().'
                                    <button type="submit" class="dropdown-item delete-team" id="' . $item->id . '">
                                        <span class="material-icons">delete</span> Remove Team
                                    </button>
                                </form>
                              </div>
                            </div>';
                } elseif (isCoach() || isPlayer()){
                    $actionBtn = '
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                '.$showTeam.'
                              </div>
                            </div>';
                }
                return $actionBtn;
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

    public  function store(array $data, Competition $competition, $loggedUser){
        $data['competitionId'] = $competition->id;
        $division = $this->groupDivisionRepository->create($data);

        Notification::send($this->userRepository->getAllAdminUsers(), new GroupDivisionCreatedDeleted($loggedUser, $division ,$competition, 'created'));

        $this->storeTeam($data, $division, $competition);

        return $division;
    }

    public function allTeamsParticipant(Team $team)
    {
        $admins = $this->userRepository->getAllAdminUsers();

        $playersIds = collect($team->players)->pluck('id')->all();
        $players = $this->userRepository->getInArray('player', $playersIds);

        $coachesIds = collect($team->coaches)->pluck('id')->all();
        $coaches = $this->userRepository->getInArray('coach', $coachesIds);

        return $admins->merge($players)->merge($coaches);
    }

    public  function storeTeam(array $data, GroupDivision $groupDivision, Competition $competition){
        if (array_key_exists('opponentTeams', $data)){
            $groupDivision->teams()->attach($data['opponentTeams']);
        }
        if (array_key_exists('teams', $data)){
            $groupDivision->teams()->attach($data['teams']);
            $teams = $this->teamRepository->getInArray($data['teams']);

            foreach ($teams as $team){
                $teamParticipants = $this->allTeamsParticipant($team);
                Notification::send($teamParticipants,new TeamJoinedRemovedCompetition($team, $competition, 'joined'));
            }
        }
        return $groupDivision;
    }

    public function removeTeam(GroupDivision $group, Team $team)
    {
        $competition = $group->competition;
        $group->teams()->detach($team);
        $teamParticipants = $this->allTeamsParticipant($team);
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
            $teamParticipants = $this->allTeamsParticipant($team);
            Notification::send($teamParticipants,new TeamJoinedRemovedCompetition($team, $competition, 'removed from'));
            Notification::send($teamParticipants, new GroupDivisionCreatedDeleted($loggedUser, $groupDivision, $competition, 'deleted'));
        }
        $groupDivision->delete();
        return $groupDivision;
    }
}
