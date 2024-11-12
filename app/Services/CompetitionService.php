<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Team;
use App\Notifications\CompetitionManagements\CompetitionCreatedDeleted;
use App\Notifications\CompetitionManagements\CompetitionStatus;
use App\Notifications\CompetitionManagements\CompetitionUpdated;
use App\Notifications\CompetitionManagements\TeamJoinedCompetition;
use App\Repository\CoachRepository;
use App\Repository\CompetitionRepository;
use App\Repository\GroupDivisionRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CompetitionService extends Service
{
    private CompetitionRepository $competitionRepository;
    private GroupDivisionRepository $groupDivisionRepository;
    private TeamRepository $teamRepository;
    private PlayerRepository $playerRepository;
    private CoachRepository $coachRepository;
    private UserRepository $userRepository;

    public function __construct(
        CompetitionRepository $competitionRepository,
        GroupDivisionRepository $groupDivisionRepository,
        TeamRepository $teamRepository,
        PlayerRepository $playerRepository,
        CoachRepository $coachRepository,
        UserRepository $userRepository
    )
    {
        $this->competitionRepository = $competitionRepository;
        $this->groupDivisionRepository = $groupDivisionRepository;
        $this->teamRepository = $teamRepository;
        $this->playerRepository = $playerRepository;
        $this->coachRepository = $coachRepository;
        $this->userRepository = $userRepository;
    }
    public function index(){
        return $this->competitionRepository->getAll();
    }
    public function getActiveCompetition()
    {
        return $this->competitionRepository->getAll(status: '1');
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
                if ($item->status != 'Cancelled') {
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
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . route('competition-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Competition</a>
                                <a class="dropdown-item" href="' . route('competition-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Competition</a>
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
                return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->name . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->type . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('date', function ($item) {
                $startDate = date('M d, Y', strtotime($item->startDate));
                $endDate = date('M d, Y', strtotime($item->endDate));
                return $startDate.' - '.$endDate;
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
                if ($item->status == 'Scheduled') {
                    $status = '<span class="badge badge-pill badge-warning">'.$item->status .'</span>';
                } elseif ($item->status == 'Ongoing') {
                    $status = '<span class="badge badge-pill badge-info">'.$item->status .'</span>';
                } elseif ($item->status == 'Completed') {
                    $status = '<span class="badge badge-pill badge-success">'.$item->status .'</span>';
                } else {
                    $status = '<span class="badge badge-pill badge-danger">'.$item->status .'</span>';
                }
                return $status;
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
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('match-schedules.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Match</a>
                            <a class="dropdown-item" href="' . route('match-schedules.show', $item->id) . '"><span class="material-icons">visibility</span> View Match</a>
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons text-danger">delete</span> Delete Match
                            </button>
                          </div>
                        </div>';
                } elseif (isCoach() || isPlayer()){
                    $actionBtn = '<a class="btn btn-sm btn-outline-secondary" href="' . route('match-schedules.show', $item->id) . '" data-toggle="tooltips" data-placement="bottom" title="View Match Detail">
                                        <span class="material-icons">visibility</span>
                                  </a>';
                }
                return $actionBtn;
            })
            ->editColumn('team', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[0]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[0]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[0]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('opponentTeam', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[1]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[1]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[1]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('score', function ($item){
                return '<p class="mb-0"><strong class="js-lists-values-lead">' .$item->teams[0]->pivot->teamScore . ' - ' . $item->teams[1]->pivot->teamScore.'</strong></p>';
            })
            ->editColumn('date', function ($item) {
                $date = $this->convertToDate($item->date);
                $startTime = $this->convertToTime($item->startTime);
                $endTime = $this->convertToTime($item->endTime);
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                if ($item->status == 'Scheduled') {
                    $status = '<span class="badge badge-pill badge-warning">'.$item->status .'</span>';
                } elseif ($item->status == 'Ongoing') {
                    $status = '<span class="badge badge-pill badge-info">'.$item->status .'</span>';
                } elseif ($item->status == 'Completed') {
                    $status = '<span class="badge badge-pill badge-success">'.$item->status .'</span>';
                } else {
                    $status = '<span class="badge badge-pill badge-danger">'.$item->status .'</span>';
                }
                return $status;
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

        $competitionData['competitionId'] = $competition->id;
        $division = $this->groupDivisionRepository->create($competitionData);

        $admins = $this->userRepository->getAllAdminUsers();
        Notification::send($admins, new CompetitionCreatedDeleted($loggedUser, $competition, 'created'));

        if (array_key_exists('opponentTeams', $competitionData)){
            $division->teams()->attach($competitionData['opponentTeams']);
        }
        if (array_key_exists('teams', $competitionData)){
            $division->teams()->attach($competitionData['teams']);
            $teams = $this->teamRepository->getInArray($competitionData['teams']);

            foreach ($teams as $team){
                $teamParticipants = $this->allTeamsParticipant($team);
                Notification::send($teamParticipants,new TeamJoinedCompetition($team, $competition));
            }
        }
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

    public function allTeamsParticipant(Team $team)
    {
        $admins = $this->userRepository->getAllAdminUsers();
        $playersIds = collect($team->players)->pluck('id')->all();
        $players = $this->userRepository->getInArray('player', $playersIds);
        $coachesIds = collect($team->coaches)->pluck('id')->all();
        $coaches = $this->userRepository->getInArray('coach', $coachesIds);
        return $admins->merge($players)->merge($coaches);
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
                $teamParticipants = $this->allTeamsParticipant($team);
                Notification::send($teamParticipants, new CompetitionStatus($competition, $team, $statusMessage));
            }
        }

        return $competition;
    }

    public function destroy(Competition $competition, $loggedUser): Competition
    {
        $teams = $this->teamRepository->getJoinedCompetition($competition);
        foreach ($teams as $team) {
            $teamParticipants = $this->allTeamsParticipant($team);
            Notification::send($teamParticipants, new CompetitionCreatedDeleted($loggedUser, $competition, 'deleted'));
        }
        $this->deleteImage($competition->logo);
        $competition->delete();
        return $competition;
    }
}
