<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
use App\Notifications\OpponentTeamsManagements\OpponentTeamUpdated;
use App\Notifications\AddOrRemoveFromTeamNotification;
use App\Notifications\PlayerCoachRemoveToTeam;
use App\Notifications\TeamsManagements\TeamCreatedDeleted;
use App\Notifications\TeamsManagements\TeamUpdated;
use App\Repository\CoachRepository;
use App\Repository\MatchRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamMatchRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class TeamService extends Service
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private MatchRepository $eventScheduleRepository;
    private TeamMatchRepository $teamMatchRepository;
    private DatatablesHelper $datatablesService;
    private PlayerRepository $playerRepository;
    private CoachRepository $coachRepository;

    public function __construct(
        TeamRepository      $teamRepository,
        UserRepository      $userRepository,
        MatchRepository     $eventScheduleRepository,
        TeamMatchRepository $teamMatchRepository,
        PlayerRepository    $playerRepository,
        CoachRepository     $coachRepository,
        DatatablesHelper    $datatablesService)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->teamMatchRepository = $teamMatchRepository;
        $this->playerRepository = $playerRepository;
        $this->coachRepository = $coachRepository;
        $this->datatablesService = $datatablesService;
    }
    public function indexDatatables($teamsData)
    {
        return Datatables::of($teamsData)
            ->addColumn('action', function ($item) {
                $actionButton = '';
                if (isCoach() || isPlayer()){
                    $actionButton = $this->datatablesService->buttonTooltips(route('team-managements.show', $item->hash), 'View Team', 'visibility');
                } elseif (isAllAdmin()){
                    if ($item->status == '1') {
                        $statusButton = '<button type="submit" class="dropdown-item setDeactivate" id="'.$item->id.'">
                                                <span class="material-icons text-danger">check_circle</span>
                                                Deactivate Team
                                        </button>';
                    } else {
                        $statusButton = '<button type="submit" class="dropdown-item setActivate" id="'.$item->id.'">
                                                <span class="material-icons text-success">check_circle</span>
                                                Activate Team
                                        </button>';
                    }
                    $actionButton =  '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('team-managements.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Team</a>
                            <a class="dropdown-item" href="' . route('team-managements.show', $item->hash) . '"><span class="material-icons">visibility</span> View Team</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete-team" id="' . $item->id . '">
                                <span class="material-icons text-danger">delete</span> Delete Team
                            </button>
                          </div>
                        </div>';
                }
                return $actionButton;
            })
            ->editColumn('players', function ($item) {
                return count($item->players).' Player(s)';
            })
            ->editColumn('coaches', function ($item) {
                return count($item->coaches).' Coach(es)';
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->logo, $item->teamName, $item->ageGroup, route('team-managements.show', $item->hash));
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->activeNonactiveStatus($item->status);
            })
            ->rawColumns(['action', 'name', 'status', 'players', 'coaches'])
            ->addIndexColumn()
            ->make();
    }

    public function index(): JsonResponse
    {
        $query = $this->teamRepository->getByTeamside('Academy Team');
        return $this->indexDatatables($query);
    }

    public function allTeams($exceptTeamId = null): Collection|array
    {
        return $this->teamRepository->getAll(exceptTeamId:  $exceptTeamId);
    }

    public function coachTeamsIndex($coach)
    {
        return $this->indexDatatables($coach->teams);
    }
    public function playerTeamsIndex($player)
    {
        return $this->indexDatatables($player->teams);
    }

    public function teamPlayers(Team $team){
        $query = $team->players;

        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                $actionButton = '';
                if (isCoach()){
                    $actionButton =  $this->datatablesService->buttonTooltips(route('player-managements.show', $item->hash), 'View player', 'visibility');
                } elseif (isAllAdmin()){
                    $actionButton =  '
                                <div class="dropdown">
                                  <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="material-icons">
                                        more_vert
                                    </span>
                                  </button>
                                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="' . route('player-managements.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Player</a>
                                    <a class="dropdown-item" href="' . route('player-managements.show', $item->hash) . '"><span class="material-icons">visibility</span> View Player</a>
                                    <button type="button" class="dropdown-item remove-player" id="' . $item->id . '">
                                        <span class="material-icons">delete</span> Remove Player From Team
                                    </button>
                                  </div>
                                </div>';
                }
                return $actionButton;
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->editColumn('name', function ($item) {
                if (isAllAdmin() || isCoach()){
                    $playerName = $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
                } else {
                    $playerName = $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name);
                }
                return $playerName;
            })
            ->addColumn('minutesPlayed', function ($item) use ($team){
                return $item->playerMatchStats()
                    ->where('teamId', $team->id)
                    ->sum('minutesPlayed');
            })
            ->addColumn('apps', function ($item) use ($team){
                return $item->playerMatchStats()
                    ->where('teamId', $team->id)
                    ->where('minutesPlayed', '>', '0')
                    ->count();
            })
            ->addColumn('goals', function ($item) use ($team){
                return $item->playerMatchStats()->where('teamId', $team->id)->sum('goals');
            })
            ->addColumn('assists', function ($item) use ($team){
                return $item->playerMatchStats()->where('teamId', $team->id)->sum('assists');
            })
            ->addColumn('ownGoals', function ($item) use ($team){
                return $item->playerMatchStats()->where('teamId', $team->id)->sum('ownGoal');
            })
            ->addColumn('shots', function ($item) use ($team){
                return $item->playerMatchStats()->where('teamId', $team->id)->sum('shots');
            })
            ->addColumn('passes', function ($item) use ($team){
                return $item->playerMatchStats()->where('teamId', $team->id)->sum('passes');
            })
            ->addColumn('fouls', function ($item) use ($team){
                return $item->playerMatchStats()->where('teamId', $team->id)->sum('fouls');
            })
            ->addColumn('yellowCards', function ($item) use ($team){
                return $item->playerMatchStats()->where('teamId', $team->id)->sum('yellowCards');
            })
            ->addColumn('redCards', function ($item) use ($team){
                return $item->playerMatchStats()->where('teamId', $team->id)->sum('redCards');
            })
            ->addColumn('saves', function ($item) use ($team){
                return $item->playerMatchStats()->where('teamId', $team->id)->sum('saves');
            })
            ->rawColumns([
                'action',
                'age',
                'name',
                'minutesPlayed',
                'apps',
                'goals',
                'assists',
                'ownGoals',
                'shots',
                'passes',
                'fouls',
                'yellowCards',
                'redCards',
                'saves',
            ])
            ->addIndexColumn()
            ->make();
    }

    public function teamCoaches(Team $team){
        $query = $team->coaches;
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                $actionButton = '';
                if (isAllAdmin()){
                    $actionButton =  '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('coach-managements.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Coach</a>
                            <a class="dropdown-item" href="' . route('coach-managements.show', $item->hash) . '"><span class="material-icons">visibility</span> View Coach</a>
                            <button type="button" class="dropdown-item remove-coach" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Remove Coach From Team
                            </button>
                          </div>
                        </div>';
                }
                return $actionButton;
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->specializations->name. ' - '.$item->certification->name, route('coach-managements.show', $item->hash));
            })
            ->editColumn('joinedDate', function ($item) {
                return $this->datatablesService->convertToDatetime($item->pivot->created_at);
            })
            ->editColumn('gender', function ($item) {
                return $item->user->gender;
            })
            ->rawColumns(['action', 'name', 'age', 'gender','joinedDate'])
            ->addIndexColumn()
            ->make();
    }

    public function teamCompetition(Team $team){
        $query = $team->divisions;

        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                $actionButton = '';
                if (isCoach() || isPlayer()){
                    $actionButton =  $this->datatablesService->buttonTooltips(route('competition-managements.show', $item->competition->hash), 'View Competition', 'visibility');
                } elseif (isAllAdmin()){
                    $statusButton = '';
                    if ($item->competition->status != 'Cancelled' && $item->competition->status != 'Completed') {
                        $statusButton = '<button type="submit" class="dropdown-item cancelBtn" id="'.$item->id.'">
                                            <span class="material-icons text-danger">block</span> Cancel Competition
                                        </button>';
                    }
                    $actionButton =  '
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . route('competition-managements.edit', $item->competition->hash) . '"><span class="material-icons">edit</span> Edit Competition</a>
                                <a class="dropdown-item" href="' . route('competition-managements.show', $item->competition->hash) . '"><span class="material-icons">visibility</span> View Competition</a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item delete" id="' . $item->competitionId . '">
                                    <span class="material-icons">delete</span> Delete Competition
                                </button>
                              </div>
                            </div>';
                }
                return $actionButton;
            })
            ->editColumn('divisions', function ($item) {
                return '<span class="badge badge-pill badge-danger">'.$item->groupName.'</span>';
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->competition->logo, $item->competition->name, $item->competition->type, route('competition-managements.show', $item->competition->hash));
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesService->competitionStartEndDate($item->competition);
            })
            ->editColumn('contact', function ($item) {
                if ($item->competition->contactName != null && $item->competition->contactPhone != null){
                    $contact = $item->competition->contactName. ' ~ '.$item->competition->contactPhone;
                }else{
                    $contact = 'No cantact added';
                }
                return $contact;
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->eventStatus($item->competition->status);
            })
            ->editColumn('location', function ($item) {
                return $item->competition->location;
            })
            ->rawColumns(['action', 'name', 'divisions', 'date', 'contact', 'status', 'location'])
            ->addIndexColumn()
            ->make();
    }

    public function teamOverviewStats(Team $team)
    {
        $stats = [
            'teamScore',
            'cleanSheets',
            'teamOwnGoal',
        ];
        $results = ['Win', 'Lose', 'Draw'];
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();

        $statsData['matchPlayed'] = $this->eventScheduleRepository->getTeamsMatchPlayed($team);
        $statsData['matchPlayedThisMonth'] = $this->eventScheduleRepository->getTeamsMatchPlayed($team, startDate: $startDate, endDate: $endDate);

        foreach ($stats as $stat){
            $statsData[$stat] = $this->teamMatchRepository->getTeamsStats($team, stats: $stat);
            $statsData[$stat.'ThisMonth'] = $this->teamMatchRepository->getTeamsStats($team, startDate: $startDate, endDate: $endDate, stats: $stat);
        }
        foreach ($results as $result){
            $statsData[$result] = $this->teamMatchRepository->getTeamsStats($team, results: $result);
            $statsData[$result.'ThisMonth'] = $this->teamMatchRepository->getTeamsStats($team, startDate: $startDate, endDate: $endDate, results: $result);
        }

        $statsData['goalsConceded'] = $this->teamMatchRepository->getTeamsStats($team, teamSide:'Opponent Team', stats: 'teamScore');
        $statsData['goalsConcededThisMonth'] = $this->teamMatchRepository->getTeamsStats($team, teamSide:'Opponent Team', startDate: $startDate, endDate: $endDate, stats: 'teamScore');

        $statsData['goalsDifference'] = $statsData['teamScore'] - $statsData['goalsConceded'];
        $statsData['goalDifferenceThisMonth'] = $statsData['teamScoreThisMonth'] - $statsData['goalsConcededThisMonth'];

        return $statsData;
    }

    public function teamLatestMatch(Team $team)
    {
        return $this->eventScheduleRepository->getTeamsEvents($team, 'Match', 'Completed', true, 4);
    }

    public function teamUpcomingMatch(Team $team)
    {
        $scheduled = $this->eventScheduleRepository->getTeamsEvents($team, 'Match', 'Scheduled', true, 2);
        $ongoing = $this->eventScheduleRepository->getTeamsEvents($team, 'Match', 'Ongoing', true, 2);
        return $scheduled->merge($ongoing);
    }

    public function teamUpcomingTraining(Team $team)
    {
        $scheduled = $this->eventScheduleRepository->getTeamsEvents($team, 'Training', 'Scheduled', true, 2);
        $ongoing = $this->eventScheduleRepository->getTeamsEvents($team, 'Training', 'Ongoing', true, 2);
        return $scheduled->merge($ongoing);
    }

    public function playersNotJoinTheTeam(Team $team)
    {
        return $this->playerRepository->getPlayerNotJoinSpecificTeam($team);
    }
    public function coachesNotJoinTheTeam(Team $team)
    {
        return $this->coachRepository->getCoachNotJoinSpecificTeam($team);
    }

    public function teamTrainingHistories(Team $team){
        $data = $this->eventScheduleRepository->getTeamsEvents($team, 'Training', 'Completed', true);

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesService->buttonTooltips(route('training-schedules.show', $item->hash), 'View training session', 'visibility');
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesService->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->eventStatus($item->status);
            })
            ->editColumn('note', function ($item) {
                if ($item->pivot->note == null) {
                    return 'No note added';
                } else {
                    return $item->pivot->note;
                }
            })
            ->editColumn('last_updated', function ($item) {
                return $this->datatablesService->convertToDatetime($item->pivot->updated_at);
            })
            ->rawColumns(['action','date','status', 'last_updated', 'note'])
            ->addIndexColumn()
            ->make();
    }

    public function teamMatchHistories(Team $team){
        $data = $this->eventScheduleRepository->getTeamsEvents($team, 'Match', 'Completed', true);

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesService->buttonTooltips(route('match-schedules.show', $item->hash), 'View match session', 'visibility');
            })
            ->editColumn('opponentTeam', function ($item) use ($team){
                if ($team->teamSide == 'Academy Team'){
                    $data = $item->teams[1];
                } else {
                    $data = $item->teams[0];
                }
                return $this->datatablesService->name($data->logo, $data->teamName, $data->ageGroup, route('team-managements.show', $data->hash));
            })
            ->editColumn('competition', function ($item) {
                if ($item->competition){
                    $competition = $this->datatablesService->name($item->competition->logo, $item->competition->teamName, $item->competition->type, route('competition-managements.show', $item->competition->hash));
                }else{
                    $competition = 'No Competition';
                }
                return $competition;
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesService->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->eventStatus($item->status);
            })
            ->editColumn('teamScore', function ($item) use ($team) {
                if ($team->teamSide == 'Academy Team'){
                    $data = $item->teams[0];
                } else {
                    $data = $item->teams[1];
                }
                return $data->pivot->teamScore;
            })
            ->editColumn('opponentTeamScore', function ($item) use ($team) {
                if ($team->teamSide == 'Academy Team'){
                    $data = $item->teams[1];
                } else {
                    $data = $item->teams[0];
                }
                return $data->pivot->teamScore;
            })
            ->editColumn('note', function ($item) {
                if ($item->pivot->note == null) {
                    $note = 'No note added';
                } else {
                    $note = $item->pivot->note;
                }
                return $note;
            })
            ->editColumn('last_updated', function ($item) {
                return $this->convertToDatetime($item->pivot->updated_at);
            })
            ->rawColumns(['action', 'competition','opponentTeam','date','status', 'teamScore', 'opponentTeamScore', 'last_updated', 'note'])
            ->addIndexColumn()
            ->make();
    }

    public  function store(array $teamData, $academyId, $loggedUser)
    {
        $teamData['logo'] = $this->storeImage($teamData, 'logo', 'assets/team-logo', 'images/undefined-user.png');
        $teamData['status'] = '1';
        $teamData['teamSide'] = 'Academy Team';
        $teamData['academyId'] = $academyId;

        $team = $this->teamRepository->create($teamData);

        $superAdminName = $this->getUserFullName($loggedUser);
        Notification::send($this->userRepository->getAllAdminUsers(),new TeamCreatedDeleted($superAdminName, $team, 'created'));

        if (array_key_exists('players', $teamData)){
            $this->updatePlayerTeam($teamData, $team);
        }
        if (array_key_exists('coaches', $teamData)){
            $this->updateCoachTeam($teamData, $team);
        }
        return $team;
    }

    public function update(array $teamData, Team $team, $loggedUser): Team
    {
        $teamData['logo'] = $this->updateImage($teamData, 'logo', 'team-logo', $team->logo);
        $team->update($teamData);

        $admins = $this->userRepository->getAllAdminUsers();
        $loggedAdminName = $this->getUserFullName($loggedUser);

        Notification::send($this->teamsCoaches($team),new TeamUpdated($loggedAdminName, $team, 'updated'));
        Notification::send($admins,new TeamUpdated($loggedAdminName, $team, 'updated'));
        return $team;
    }

    public function teamsCoaches(Team $team)
    {
        $coachesIds = collect($team->coaches)->pluck('id')->all();
        return $this->userRepository->getInArray('coach', $coachesIds);
    }
    public function teamsPlayers(Team $team)
    {
        $playersIds = collect($team->players)->pluck('id')->all();
        return $this->userRepository->getInArray('player', $playersIds);
    }

    public function updatePlayerTeam(array $teamData, Team $team): Team
    {
        $team->players()->attach($teamData['players']);
        $players = $this->userRepository->getInArray('player', $teamData['players']);
        Notification::send($players, new AddOrRemoveFromTeamNotification($team));
        return $team;
    }

    public function updateCoachTeam(array $teamData, Team $team): Team
    {
        $team->coaches()->attach($teamData['coaches']);
        $coaches = $this->userRepository->getInArray('coach', $teamData['coaches']);
        Notification::send($coaches, new AddOrRemoveFromTeamNotification($team));
        return $team;
    }

    public function removePlayer(Team $team, Player $player): Team
    {
        $team->players()->detach($player);
        $player->user->notify(new PlayerCoachRemoveToTeam($team));
        return $team;
    }

    public function removeCoach(Team $team, Coach $coach): Team
    {
        $team->coaches()->detach($coach);
        $coach->user->notify(new PlayerCoachRemoveToTeam($team));
        return $team;
    }

    public function setStatus(Team $team, $status, $loggedUser): Team
    {
        $team->update(['status' => $status]);
        $loggedAdminName = $this->getUserFullName($loggedUser);
        $admins = $this->userRepository->getAllAdminUsers();

        if ($status == '1') {
            $message = 'activated';
        } elseif ($status == '0') {
            $message = 'deactivated';
        }

        if ($team->teamSide == 'Academy Team'){
            $coaches = $this->teamsCoaches($team);
            $players = $this->teamsPlayers($team);
            Notification::send($coaches,new TeamUpdated($loggedAdminName, $team, $message));
            Notification::send($players,new TeamUpdated($loggedAdminName, $team, $message));
            Notification::send($admins,new TeamUpdated($loggedAdminName, $team, $message));
        } elseif ($team->teamSide == 'Opponent Team'){
            Notification::send($admins,new OpponentTeamUpdated($loggedAdminName, $team, $message));
        }
        return $team;
    }

    public function destroy(Team $team, $loggedUser): Team
    {
        $this->deleteImage($team->logo);
        $team->coaches()->detach();
        $team->players()->detach();

        $loggedAdminName = $this->getUserFullName($loggedUser);
        $admins = $this->userRepository->getAllAdminUsers();
        $coaches = $this->teamsCoaches($team);
        $players = $this->teamsPlayers($team);
        Notification::send($coaches,new TeamUpdated($loggedAdminName, $team, 'deleted'));
        Notification::send($players,new TeamUpdated($loggedAdminName, $team, 'deleted'));
        Notification::send($admins,new TeamUpdated($loggedAdminName, $team, 'deleted'));

        $team->delete();

        return $team;
    }
}
