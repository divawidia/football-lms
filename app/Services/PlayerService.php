<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Admin;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\Team;
use App\Notifications\AdminManagements\AdminAccountUpdated;
use App\Notifications\PlayerCoachAddToTeam;
use App\Notifications\PlayerManagements\PlayerAccountCreatedDeleted;
use App\Notifications\PlayerManagements\PlayerAccountUpdated;
use App\Notifications\PlayerCoachRemoveToTeam;
use App\Repository\EventScheduleRepository;
use App\Repository\Interface\PlayerRepositoryInterface;
use App\Repository\PlayerPositionRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use function PHPUnit\Framework\isEmpty;

class PlayerService extends Service
{
    private EventScheduleService $eventScheduleService;
    private PlayerRepositoryInterface $playerRepository;
    private TeamRepository $teamRepository;
    private EventScheduleRepository $eventScheduleRepository;
    private PlayerPositionRepository $playerPositionRepository;
    private UserRepository $userRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(
        EventScheduleService $eventScheduleService,
        PlayerRepositoryInterface $playerRepository,
        EventScheduleRepository $eventScheduleRepository,
        TeamRepository $teamRepository,
        PlayerPositionRepository $playerPositionRepository,
        UserRepository $userRepository,
        DatatablesHelper $datatablesHelper
    )
    {
        $this->eventScheduleService = $eventScheduleService;
        $this->playerRepository = $playerRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->teamRepository = $teamRepository;
        $this->playerPositionRepository = $playerPositionRepository;
        $this->userRepository = $userRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function makePlayerDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if (isAllAdmin()){
                    $statusButton = '';
                    if ($item->user->status == '1'){
                        $statusButton = '<button type="submit" class="dropdown-item setDeactivate" id="'.$item->hash.'">
                                                <span class="material-icons text-danger">check_circle</span>
                                                Deactivate Admin
                                        </button>';
                    }elseif ($item->user->status == '0') {
                        $statusButton = '<button type="submit" class="dropdown-item setActivate" id="'.$item->hash.'">
                                                <span class="material-icons text-success">check_circle</span>
                                                Activate Admin
                                        </button>';
                    }
                    $actionBtn = '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="' . route('player-managements.edit', $item->hash) . '"><span class="material-icons">edit</span>Edit Player</a>
                            <a class="dropdown-item" href="' . route('player-managements.show', $item->hash) . '"><span class="material-icons">visibility</span> View Player</a>
                            '. $statusButton .'
                            <a class="dropdown-item changePassword" id="'.$item->hash.'"><span class="material-icons">lock</span> Change Player Password</a>
                            <button type="button" class="dropdown-item delete-user" id="' . $item->hash . '">
                                <span class="material-icons text-danger">delete</span> Delete Player
                            </button>
                          </div>
                        </div>';
                } elseif (isCoach()){
                    $actionBtn = $this->datatablesHelper->buttonTooltips(route('player-managements.show', $item->hash), "View Player", "visibility");
                }
                return $actionBtn;
            })
            ->editColumn('teams.name', function ($item) {
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
                return '
                        <div class="media flex-nowrap align-items-center"
                             style="white-space: nowrap;">
                            <div class="avatar avatar-sm mr-8pt">
                                <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                            </div>
                            <div class="media-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex d-flex flex-column">
                                        <a href="'.route('player-managements.show', $item->hash).'">
                                            <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        </a>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->editColumn('status', function ($item){
                $badge = '';
                if ($item->user->status == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Active</span>';
                }elseif ($item->user->status == '0'){
                    $badge = '<span class="badge badge-pill badge-danger">Non-Active</span>';
                }
                return $badge;
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->rawColumns(['action', 'name','status', 'age', 'teams.name'])
            ->addIndexColumn()
            ->make();
    }
    public function index(): JsonResponse
    {
        $query = $this->playerRepository->getAll();
        return $this->makePlayerDatatables($query);
    }
    // retrieve player data based on coach managed teams
    public function coachPlayerIndex($coach): JsonResponse
    {
        $teams = $coach->teams;

        // query player data that included in teams that managed by logged in coach
        $query = $this->playerRepository->getPLayersByTeams($teams);
        return $this->makePlayerDatatables($query);
    }

    public function playerTeams(Player $player): JsonResponse
    {
        return Datatables::of($player->teams)
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
                            <button type="button" class="dropdown-item delete-team" id="' . $item->hash . '">
                                <span class="material-icons">delete</span> Remove Player from Team
                            </button>
                          </div>
                        </div>';
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
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->division . ' - '.$item->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('date', function ($item){
                return date('M d, Y', strtotime($item->pivot->created_at));
            })
            ->rawColumns(['action', 'name','date'])
            ->make();
    }
    public function removeTeam(Player $player, Team $team)
    {
        $player->teams()->detach($team->id);
        $player->user->notify(new PlayerCoachRemoveToTeam($team));
        return $player;
    }
    public function updateTeams($teamData, Player $player)
    {
        $player->teams()->attach($teamData);
        $team =$this->teamRepository->find($teamData)->first();
        $player->user->notify(new PlayerCoachAddToTeam($team));
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

        $superAdminName = $this->getUserFullName($loggedUser);

        Notification::send($this->userRepository->getAllAdminUsers(),new PlayerAccountCreatedDeleted($superAdminName, $player, 'created'));

        PlayerParrent::create([
            'firstName' => $data['firstName2'],
            'lastName' => $data['lastName2'],
            'relations' => $data['relations'],
            'email' => $data['email2'],
            'phoneNumber' => $data['phoneNumber2'],
            'playerId' => $player->id,
        ]);
        return $player;
    }

    public function show(Player $player)
    {
        $stats = [
            'minutesPlayed',
            'goals',
            'assists',
            'ownGoal',
            'shots',
            'passes',
            'fouls',
            'yellowCards',
            'redCards',
            'saves',
            ];
        $results = ['Win', 'Lose', 'Draw'];

        $matchPlayed = $this->playerRepository->countMatchPlayed($player);
        $thisMonthMatchPlayed = $this->playerRepository->countMatchPlayed($player, Carbon::now()->startOfMonth(),Carbon::now());

        foreach ($stats as $stat){
            $statsData[$stat] = $this->playerRepository->playerMatchStatsSum($player, $stat);
            $statsData[$stat.'ThisMonth'] = $this->playerRepository->playerMatchStatsSum($player, $stat, Carbon::now()->startOfMonth(),Carbon::now());
        }
        foreach ($results as $result){
            $statsData[$result] = $this->playerRepository->matchResults($player, $result);
            $statsData[$result.'ThisMonth'] = $this->playerRepository->matchResults($player, $result, Carbon::now()->startOfMonth(),Carbon::now());
        }

        $scheduledMatches = $this->eventScheduleRepository->getEventByModel($player, 'Match', 'Scheduled', 2);
        $ongoingMatches = $this->eventScheduleRepository->getEventByModel($player, 'Match', 'Ongoing', 2);
        $upcomingMatches = $scheduledMatches->merge($ongoingMatches);

        $scheduledTrainings = $this->eventScheduleRepository->getEventByModel($player, 'Training', 'Scheduled',2);
        $ongoingTrainings = $this->eventScheduleRepository->getEventByModel($player, 'Training', 'Ongoing',2);
        $upcomingTrainings = $scheduledTrainings->merge($ongoingTrainings);

        $playerAge = $this->getAge($player->user->dob);
        $playerDob = $this->convertToDate($player->user->dob);
        $playerJoinDate = $this->convertToDate($player->joinDate);
        $playerCreatedAt = $this->convertToDate($player->user->created_at);
        $playerUpdatedAt = $this->convertToDate($player->user->updated_at);
        $playerLastSeen = $this->convertToDate($player->user->lastSeen);

        return compact(
            'matchPlayed',
            'thisMonthMatchPlayed',
            'statsData',
            'upcomingMatches',
            'upcomingTrainings',
            'playerAge',
            'playerDob',
            'playerJoinDate',
            'playerCreatedAt',
            'playerUpdatedAt',
            'playerLastSeen',
        );
    }

    // retrieve teams data that the player hasn't joined for add player to another team purpose
    public function hasntJoinedTeams(Player $player)
    {
        return $this->teamRepository->getTeamsHaventJoinedByPLayer($player);
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

    public function skillStatsHistoryChart(Player $player, $startDate, $endDate)
    {
        if ($startDate == null) {
            $startDate = Carbon::now()->startOfYear();
        }
        if ($endDate == null) {
            $endDate = Carbon::now();
        }

        $results =  $player->playerSkillStats()->whereBetween('created_at', [$startDate, $endDate])->get();
//        $results = $results->sortBy('created_at');

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

    public function playerUpcomingMatches(Player $player)
    {
        $scheduled = $this->eventScheduleRepository->getEventByModel($player, 'Match', 'Scheduled');
        $ongoing = $this->eventScheduleRepository->getEventByModel($player, 'Match', 'Ongoing');
        $data = $scheduled->merge($ongoing);
        return $this->eventScheduleService->makeDataTablesMatch($data);
    }
    public function playerUpcomingTraining(Player $player)
    {
        $scheduled = $this->eventScheduleRepository->getEventByModel($player, 'Training', 'Scheduled');
        $ongoing = $this->eventScheduleRepository->getEventByModel($player, 'Training', 'Ongoing');
        $data = $scheduled->merge($ongoing);
        return $this->eventScheduleService->makeDataTablesTraining($data);
    }

    public function playerMatchCalendar(Player $player)
    {
        $scheduled = $this->eventScheduleRepository->getEventByModel($player, 'Match', 'Scheduled');
        $ongoing = $this->eventScheduleRepository->getEventByModel($player, 'Match', 'Ongoing');
        $data = $scheduled->merge($ongoing);
        return $this->eventScheduleService->makeMatchCalendar($data);
    }
    public function playerTrainingCalendar(Player $player)
    {
        $scheduled = $this->eventScheduleRepository->getEventByModel($player, 'Training', 'Scheduled');
        $ongoing = $this->eventScheduleRepository->getEventByModel($player, 'Training', 'Ongoing');
        $data = $scheduled->merge($ongoing);
        return $this->eventScheduleService->makeTrainingCalendar($data);
    }

    public function playerLatestMatch(Player $player)
    {
        return $this->eventScheduleRepository->playerLatestEvent($player, 'Match');
    }
    public function playerLatestTraining(Player $player)
    {
        return $this->eventScheduleRepository->playerLatestEvent($player, 'Training');
    }

    public function update(array $data, Player $player)
    {
        $data['foto'] = $this->updateImage($data, 'foto', 'assets/user-profile', $player->user->foto);
        $this->playerRepository->update($player, $data);
        $player->user->notify(new PlayerAccountUpdated($player, 'updated'));
        return $player;
    }

    public function setStatus(Player $player, $status)
    {
        $this->userRepository->updateUserStatus($player, $status);

        if ($status == '1') {
            $message = 'activated';
        } elseif ($status == '0') {
            $message = 'deactivated';
        }

        $player->user->notify(new PlayerAccountUpdated($player, $message));
        return $player;
    }

    public function changePassword($data, Player $player){
        $this->userRepository->changePassword($data, $player);
        $player->user->notify(new PlayerAccountUpdated($player, 'updated the password'));
        return $player;
    }

    public function destroy(Player $player, $loggedUser)
    {
        $this->deleteImage($player->user->foto);

        $superAdminName = $this->getUserFullName($loggedUser);
        Notification::send($this->userRepository->getAllAdminUsers(),new PlayerAccountCreatedDeleted($superAdminName, $player, 'created'));

        $this->userRepository->delete($player);
        return $player;
    }
}
