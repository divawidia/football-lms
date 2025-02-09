<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\Team;
use App\Notifications\PlayerManagements\Admin\AddTeamForAdminNotification;
use App\Notifications\PlayerManagements\Admin\PlayerChangePasswordForAdmin;
use App\Notifications\PlayerManagements\Admin\PlayerCreatedForAdminNotification;
use App\Notifications\PlayerManagements\Admin\PlayerActivatedForAdmin;
use App\Notifications\PlayerManagements\Admin\PlayerDeactivatedForAdmin;
use App\Notifications\PlayerManagements\Admin\PlayerDeletedForAdminNotification;
use App\Notifications\PlayerManagements\Admin\PlayerUpdatedForAdmin;
use App\Notifications\PlayerManagements\Admin\RemoveTeamForAdminNotification;
use App\Notifications\PlayerManagements\Player\AddTeamForPlayerNotification;
use App\Notifications\PlayerManagements\Player\PlayerActivatedForPlayer;
use App\Notifications\PlayerManagements\Player\PlayerChangePasswordForPlayer;
use App\Notifications\PlayerManagements\Player\PlayerCreatedForPlayerNotification;
use App\Notifications\PlayerManagements\Player\PlayerDeactivatedForPlayer;
use App\Notifications\PlayerManagements\Player\PlayerUpdatedForPlayer;
use App\Notifications\PlayerManagements\Player\RemoveTeamForPlayerNotification;
use App\Repository\Interface\TrainingRepositoryInterface;
use App\Repository\MatchRepository;
use App\Repository\Interface\PlayerRepositoryInterface;
use App\Repository\Interface\TeamRepositoryInterface;
use App\Repository\PlayerPositionRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class PlayerService extends Service
{
    private MatchService $matchService;
    private TrainingService $trainingService;
    private PlayerRepositoryInterface $playerRepository;
    private TeamRepositoryInterface $teamRepository;
    private MatchRepository $matchRepository;
    private TrainingRepositoryInterface $trainingRepository;
    private PlayerPositionRepository $playerPositionRepository;
    private UserRepository $userRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(
        MatchService              $matchService,
        TrainingService           $trainingService,
        PlayerRepositoryInterface $playerRepository,
        MatchRepository           $matchRepository,
        TrainingRepositoryInterface $trainingRepository,
        TeamRepositoryInterface   $teamRepository,
        PlayerPositionRepository  $playerPositionRepository,
        UserRepository            $userRepository,
        DatatablesHelper          $datatablesHelper
    )
    {
        $this->matchService = $matchService;
        $this->trainingService = $trainingService;
        $this->playerRepository = $playerRepository;
        $this->matchRepository = $matchRepository;
        $this->trainingRepository = $trainingRepository;
        $this->teamRepository = $teamRepository;
        $this->playerPositionRepository = $playerPositionRepository;
        $this->userRepository = $userRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function makePlayerDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->indexActionButton($item);
            })
            ->editColumn('teams.name', function ($item) {
                return $this->datatablesHelper->usersTeams($item);
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('status', function ($item){
                return $this->datatablesHelper->activeNonactiveStatus($item->user->status);
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->rawColumns(['action', 'name','status', 'teams.name'])
            ->addIndexColumn()
            ->make();
    }
    public function indexActionButton(Player $player)
    {
        $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('player-managements.show', $player->hash), icon: 'visibility', btnText: 'View player Profile');
        if (isAllAdmin()){
            ($player->user->status == '1')
                ? $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setDeactivate', $player->hash, 'danger', icon: 'check_circle', btnText: 'Deactivate player Account')
                : $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setActivate', $player->hash, 'success', icon: 'check_circle', btnText: 'Activate player Account');
            $dropdownItem .= $this->datatablesHelper->linkDropdownItem(route: route('player-managements.edit', $player->hash), icon: 'edit', btnText: 'Edit player Profile');
            $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('changePassword', $player->hash, icon: 'lock', btnText: 'Change Player Account Password');
            $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete-user', $player->hash, 'danger', icon: 'delete', btnText: 'Delete Player Account');
        }
        return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
            return $dropdownItem;
        });
    }

    public function index($position, $skill, $team, $status): JsonResponse
    {
        $query = $this->playerRepository->getAll($team, $position, $skill, $status);
        return $this->makePlayerDatatables($query);
    }

    // retrieve player data based on coach managed teams
    public function coachPlayerIndex($coach, $position, $skill, $status, $teams = null): JsonResponse
    {
        if ($teams == null) {
            $teams = $coach->teams;
        }

        // query player data that included in teams that managed by logged in coach
        $query = $this->playerRepository->getAll($teams, $position, $skill, $status);
        return $this->makePlayerDatatables($query);
    }

    public function playerTeams(Player $player): JsonResponse
    {
        return Datatables::of($player->teams)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('team-managements.edit', $item->hash), icon: 'edit', btnText: 'Edit team Profile');
                $dropdownItem .= $this->datatablesHelper->linkDropdownItem(route: route('team-managements.show', $item->hash), icon: 'visibility', btnText: 'View team Profile');
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete-team', $item->hash, 'danger',icon: 'delete', btnText: 'Remove Player from Team');

                return $this->datatablesHelper->dropdown(function () use ($dropdownItem, $item) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->logo, $item->teamName, $item->division, route('team-managements.show', $item->hash));
            })
            ->editColumn('date', function ($item){
                return $this->convertToDate($item->pivot->created_at);
            })
            ->rawColumns(['action', 'name',])
            ->addIndexColumn()
            ->make();
    }

    public function removeTeam(Player $player, Team $team, $loggedUser)
    {
        $player->teams()->detach($team->id);
        $teamCoachesAndAdmin = $this->userRepository->allTeamsParticipant($team, players: false);

        Notification::send($teamCoachesAndAdmin,new RemoveTeamForAdminNotification($loggedUser, $team, $player));
        $player->user->notify(new RemoveTeamForPlayerNotification($team));

        return $player;
    }

    public function updateTeams($teamData, Player $player, $loggedUser)
    {
        $player->teams()->attach($teamData['teams']);
        $team =$this->teamRepository->find($teamData['teams']);
        $teamCoachesAndAdmin = $this->userRepository->allTeamsParticipant($team, players: false);

        Notification::send($teamCoachesAndAdmin,new AddTeamForAdminNotification($loggedUser, $team, $player));
        $player->user->notify(new AddTeamForPlayerNotification($team));

        return $player;
    }

    public function getPlayerPosition()
    {
        return $this->playerPositionRepository->getAll();
    }

    public  function store(array $data, $academyId, $loggedUser){

        $data['foto'] = $this->storeImage($data, 'foto', 'assets/user-profile', 'images/undefined-user.png');
        $data['password'] = bcrypt($data['password']);
        $data['status'] = '1';
        $data['academyId'] = $academyId;

        $user = $this->userRepository->createUserWithRole($data, 'player');
        $data['userId'] = $user->id;
        $player = $this->playerRepository->create($data);

        PlayerParrent::create([
            'firstName' => $data['firstName2'],
            'lastName' => $data['lastName2'],
            'relations' => $data['relations'],
            'email' => $data['email2'],
            'phoneNumber' => $data['phoneNumber2'],
            'playerId' => $player->id,
        ]);

        Notification::send($this->userRepository->getAllAdminUsers(),new PlayerCreatedForAdminNotification($loggedUser, $player));
        $player->user->notify(new PlayerCreatedForPlayerNotification());

        return $player;
    }

    public function playerUpcomingMatches(Player $player)
    {
        return $this->matchRepository->getByRelation($player, status: ['Scheduled', 'Ongoing']);
    }

    public function playerUpcomingTrainings(Player $player)
    {
        return $this->trainingRepository->getByRelation($player, status: ['Scheduled', 'Ongoing']);
    }


    public function playerMatchPlayed(Player $player)
    {
        return $this->playerRepository->countMatchPlayed($player);
    }

    public function playerMatchPlayedThisMonth(Player $player)
    {
        return $this->playerRepository->countMatchPlayed($player, Carbon::now()->startOfMonth(),Carbon::now());
    }

    public function playerStats(Player $player)
    {
        $stats = ['minutesPlayed', 'goals', 'assists', 'ownGoal', 'shots', 'passes', 'fouls', 'yellowCards', 'redCards', 'saves',];
        foreach ($stats as $stat){
            $statsData[$stat] = $this->playerRepository->playerMatchStatsSum($player, $stat);
            $statsData[$stat.'ThisMonth'] = $this->playerRepository->playerMatchStatsSum($player, $stat, Carbon::now()->startOfMonth(),Carbon::now());
        }
        return $statsData;
    }

    public function matchStats(Player $player)
    {
        $results = ['Win', 'Lose', 'Draw'];
        foreach ($results as $result){
            $matchStats[$result] = $this->playerRepository->matchResults($player, $result);
            $matchStats[$result.'ThisMonth'] = $this->playerRepository->matchResults($player, $result, Carbon::now()->startOfMonth(),Carbon::now());
        }
        return $matchStats;
    }

    public function winRate(Player $player)
    {
        $totalMatch = $this->playerRepository->matchResults($player);
        $wins = $this->matchStats($player)['Win'];
        ($totalMatch > 0) ? $winRate = ( $wins /$totalMatch) * 100 : $winRate = 0; // check if totalMatch is 0 then set win rate to 0
        return round($winRate, 2);
    }


    // retrieve teams data that the player hasn't joined for add player to another team purpose
    public function hasntJoinedTeams(Player $player)
    {
        return $this->teamRepository->getAll(exceptPLayer: $player);
    }

    public function getSkillStats(Player $player){
        return $player->playerSkillStats()->latest();
    }
    public function skillStatsLabel()
    {
        $label = [
            'Controlling' => 'controlling',
            'Receiving' => 'recieving',
            'Dribbling' => 'dribbling',
            'Passing' => 'passing',
            'Shooting' => 'shooting',
            'Crossing' => 'crossing',
            'Turning' => 'turning',
            'Ball Handling' => 'ballHandling',
            'Power Kicking' => 'powerKicking',
            'Goal Keeping' => 'goalKeeping',
            'Offensive Play' => 'offensivePlay',
            'Defensive Play' => 'defensivePlay',
        ];

        return $label;
    }

    public function skillStatsChart(Player $player){
        $results = $this->getSkillStats($player)->first();
        $label = [];
        $data = [];
        if ($results != null){
            if ($player->position->category == 'Forward'){
                $label = [
                    'Controlling',
                    'Receiving',
                    'Dribbling',
                    'Shooting',
                    'PowerKicking',
                    'OffensivePlay',
                ];
                $data = [
                    $results->controlling,
                    $results->recieving,
                    $results->dribbling,
                    $results->shooting,
                    $results->powerKicking,
                    $results->offensivePlay,
                ];
            }
            elseif ($player->position->category == 'Midfielder'){
                $label = [
                    'Controlling',
                    'Receiving',
                    'Dribbling',
                    'Passing',
                    'BallHandling',
                    'OffensivePlay',
                ];
                $data = [
                    $results->controlling,
                    $results->recieving,
                    $results->dribbling,
                    $results->passing,
                    $results->ballHandling,
                    $results->offensivePlay,
                ];
            }
            elseif ($player->position->category == 'Defender'){
                $label = [
                    'Controlling',
                    'Receiving',
                    'Turning',
                    'Passing',
                    'BallHandling',
                    'DefensivePlay',
                ];
                $data = [
                    $results->controlling,
                    $results->recieving,
                    $results->turning,
                    $results->passing,
                    $results->ballHandling,
                    $results->defensivePlay,
                ];
            }
            elseif ($player->position->category == 'Defender' && $player->position->name == 'Goalkeeper (GK)' ){
                $label = [
                    'Controlling',
                    'Receiving',
                    'PowerKicking',
                    'Passing',
                    'GoalKeeping',
                    'DefensivePlay',
                ];
                $data = [
                    $results->controlling,
                    $results->recieving,
                    $results->powerKicking,
                    $results->passing,
                    $results->goalKeeping,
                    $results->defensivePlay,
                ];
            }
        }
        return compact('label', 'data');
    }

    public function skillStatsHistoryChart(Player $player, $startDate = null, $endDate = null)
    {
        $results =  $player->playerSkillStats();
        if ($startDate != null && $endDate != null){
            $results->whereBetween('created_at', [$startDate, $endDate]);
        }
        $results = $results->get();

        $label = [];
        $data = [];
        $chartDataset = [];
        $skills = $this->skillStatsLabel();

        if (count($results) > 0){ //check if retrieved skill results is not null
            foreach ($results as $result){
                $label[] = $this->convertToDate($result->created_at);
            }
            foreach ($results as $result) {
                foreach ($skills as $skill => $value) {
                    $data[$skill][] = $result[$value];
                }
            }
        } else { //check if retrieved skill results is null then only fill data with skills label
            foreach ($skills as $skill => $value) {
                $data[$skill][] = $value;
            }
        }

        foreach ($data as $key => $value) {
            $chartDataset[] = [
                'label' => $key,
                'data'=> $value,
                'tension'=> 0.4,
            ];
        }

        return [
            'labels' => $label,
            'datasets'=> $chartDataset
        ];
    }

    public function playerUpcomingMatchesDatatables(Player $player)
    {
        return $this->matchService->makeDataTablesMatch($this->playerUpcomingMatches($player));
    }
    public function playerUpcomingTrainingDatatables(Player $player)
    {
        return $this->trainingService->makeDataTablesTraining($this->playerUpcomingTrainings($player));
    }

    public function playerMatchCalendar(Player $player)
    {
        $data = $this->matchRepository->getByRelation($player, ['teams'],['Scheduled', 'Ongoing']);
        return $this->matchService->makeMatchCalendar($data);
    }
    public function playerTrainingCalendar(Player $player)
    {
        $data = $this->trainingRepository->getByRelation($player, ['team'], ['Scheduled', 'Ongoing']);
        return $this->trainingService->makeTrainingCalendar($data);
    }

    public function latestMatches(Player $player)
    {
        return $this->matchRepository->getByRelation($player, ['homeTeam', 'awayTeam', 'externalTeam'],status: ['Completed'], take: 4, orderDirection: 'desc');
    }

    public function latestTrainings(Player $player)
    {
        return $this->trainingRepository->getByRelation($player, status: ['Completed'], take: 4, orderDirection: 'desc');
    }

    public function update(array $data, Player $player, $loggedUser)
    {
        $data['foto'] = $this->updateImage($data, 'foto', 'assets/user-profile', $player->user->foto);

        $player->user->notify(new PlayerUpdatedForPlayer());
        Notification::send($this->userRepository->getAllAdminUsers(),new PlayerUpdatedForAdmin($loggedUser, $player));

        return $this->playerRepository->update($player, $data);
    }

    public function setStatus(Player $player, $status, $loggedUser)
    {
        if ($status == '1') {
            $player->user->notify(new PlayerActivatedForPlayer());
            Notification::send($this->userRepository->getAllAdminUsers(),new PlayerActivatedForAdmin($loggedUser, $player));
        } else {
            $player->user->notify(new PlayerDeactivatedForPlayer());
            Notification::send($this->userRepository->getAllAdminUsers(),new PlayerDeactivatedForAdmin($loggedUser, $player));
        }

        return $this->userRepository->updateUserStatus($player, $status);
    }

    public function changePassword($data, Player $player, $loggedUser)
    {
        $player->user->notify(new PlayerChangePasswordForPlayer());
        Notification::send($this->userRepository->getAllAdminUsers(),new PlayerChangePasswordForAdmin($loggedUser, $player));

        return $this->userRepository->changePassword($data, $player);
    }

    public function destroy(Player $player, $loggedUser)
    {
        $this->deleteImage($player->user->foto);

        Notification::send($this->userRepository->getAllAdminUsers(),new PlayerDeletedForAdminNotification($loggedUser, $player));

        return $this->userRepository->delete($player);
    }
}
