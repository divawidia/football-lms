<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use App\Repository\EventScheduleRepository;
use App\Repository\PlayerPositionRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use Yajra\DataTables\Facades\DataTables;

class PlayerService extends Service
{
    private EventScheduleService $eventScheduleService;
    private PlayerRepository $playerRepository;
    private TeamRepository $teamRepository;
    private EventScheduleRepository $eventScheduleRepository;
    private PlayerPositionRepository $playerPositionRepository;
    private UserRepository $userRepository;
    public function __construct(
        EventScheduleService $eventScheduleService,
        PlayerRepository $playerRepository,
        EventScheduleRepository $eventScheduleRepository,
        TeamRepository $teamRepository,
        PlayerPositionRepository $playerPositionRepository,
        UserRepository $userRepository
    )
    {
        $this->eventScheduleService = $eventScheduleService;
        $this->playerRepository = $playerRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->teamRepository = $teamRepository;
        $this->playerPositionRepository = $playerPositionRepository;
        $this->userRepository = $userRepository;
    }

    public function makePlayerDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if (isAllAdmin()){
                    if ($item->user->status == '1'){
                        $statusButton = '<form action="' . route('deactivate-player', $item->id) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons text-danger">block</span> Deactivate Player
                                            </button>
                                        </form>';
                    }else{
                        $statusButton = '<form action="' . route('activate-player', $item->id) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons text-success">check_circle</span> Activate Player
                                            </button>
                                        </form>';
                    }
                    $actionBtn = '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('player-managements.edit', $item->id) . '"><span class="material-icons">edit</span>Edit Player</a>
                            <a class="dropdown-item" href="' . route('player-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Player</a>
                            '. $statusButton .'
                            <a class="dropdown-item changePassword" id="'.$item->id.'"><span class="material-icons">lock</span> Change Player Password</a>
                            <button type="button" class="dropdown-item delete-user" id="' . $item->id . '">
                                <span class="material-icons text-danger">delete</span> Delete Player
                            </button>
                          </div>
                        </div>';
                } elseif (isCoach()){
                    $actionBtn = '
                      <a class="btn btn-sm btn-outline-secondary" href="' . route('player-managements.show', $item->id) . '" data-toggle="tooltips" data-placement="bottom" title="View Player">
                        <span class="material-icons">
                            visibility
                        </span>
                      </a>';
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
                                        <a href="'.route('player-managements.show', $item->id).'">
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
        $teams = $this->coachManagedTeams($coach);

        // query player data that included in teams that managed by logged in coach
        $query = $this->playerRepository->getCoachsPLayers($teams);
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
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="'.route('team-managements.edit', $item->id).'"><span class="material-icons">edit</span> Edit Team</a>
                            <a class="dropdown-item" href="'.route('team-managements.show', $item->id).'"><span class="material-icons">visibility</span> View Team</a>
                            <button type="button" class="dropdown-item delete-team" id="' . $item->id . '">
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
        return $player;
    }
    public function updateTeams($teamData, Player $player)
    {
        $player->teams()->attach($teamData);
        return $player;
    }

    public function getPlayerPosition()
    {
        return $this->playerPositionRepository->getAll();
    }

    public  function store(array $data, $academyId){

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
        return $player;
    }

    public function show(Player $player)
    {
        $matchPlayed = $this->playerRepository->countMatchPlayed($player);
        $thisMonthMatchPlayed = $this->playerRepository->countMatchPlayed($player, Carbon::now()->startOfMonth(),Carbon::now());

        $minutesPlayed = $this->playerRepository->playerMatchStatsSum($player, 'minutesPlayed');
        $thisMonthMinutesPlayed = $this->playerRepository->playerMatchStatsSum($player, 'minutesPlayed', Carbon::now()->startOfMonth(),Carbon::now());

        $fouls = $this->playerRepository->playerMatchStatsSum($player, 'fouls');
        $thisMonthFouls = $this->playerRepository->playerMatchStatsSum($player, 'fouls', Carbon::now()->startOfMonth(),Carbon::now());

        $saves = $this->playerRepository->playerMatchStatsSum($player, 'saves');
        $thisMonthSaves = $this->playerRepository->playerMatchStatsSum($player, 'saves', Carbon::now()->startOfMonth(),Carbon::now());

        $goals = $this->playerRepository->playerMatchStatsSum($player, 'goals');
        $thisMonthGoals = $this->playerRepository->playerMatchStatsSum($player, 'goals', Carbon::now()->startOfMonth(),Carbon::now());

        $assists = $this->playerRepository->playerMatchStatsSum($player, 'assists');
        $thisMonthAssists = $this->playerRepository->playerMatchStatsSum($player, 'assists', Carbon::now()->startOfMonth(),Carbon::now());

        $ownGoals = $this->playerRepository->playerMatchStatsSum($player, 'ownGoal');
        $thisMonthOwnGoals = $this->playerRepository->playerMatchStatsSum($player, 'ownGoal', Carbon::now()->startOfMonth(),Carbon::now());

        $wins = $this->playerRepository->matchResults($player, 'Win');
        $thisMonthWins = $this->playerRepository->matchResults($player, 'Win', Carbon::now()->startOfMonth(),Carbon::now());

        $losses = $this->playerRepository->matchResults($player, 'Lose');
        $thisMonthLosses = $this->playerRepository->matchResults($player, 'Lose', Carbon::now()->startOfMonth(),Carbon::now());

        $draws = $this->playerRepository->matchResults($player, 'Draw');
        $thisMonthDraws = $this->playerRepository->matchResults($player, 'Draw', Carbon::now()->startOfMonth(),Carbon::now());

        $upcomingMatches = $this->eventScheduleRepository->playerUpcomingEvent($player, 'Match', 2);

        $upcomingTrainings = $this->eventScheduleRepository->playerUpcomingEvent($player, 'Training', 2);

        $playerAge = $this->getAge($player->user->dob);
        $playerDob = $this->convertToDate($player->user->dob);
        $playerJoinDate = $this->convertToDate($player->joinDate);
        $playerCreatedAt = $this->convertToDate($player->user->created_at);
        $playerUpdatedAt = $this->convertToDate($player->user->updated_at);
        $playerLastSeen = $this->convertToDate($player->user->lastSeen);

        return compact(
            'matchPlayed',
            'thisMonthMatchPlayed',
            'minutesPlayed',
            'thisMonthMinutesPlayed',
            'fouls',
            'thisMonthFouls',
            'saves',
            'thisMonthSaves',
            'goals',
            'thisMonthGoals',
            'assists',
            'thisMonthAssists',
            'ownGoals',
            'thisMonthOwnGoals',
            'wins',
            'thisMonthWins',
            'losses',
            'thisMonthLosses',
            'draws',
            'thisMonthDraws',
            'upcomingMatches',
            'upcomingTrainings',
            'playerAge',
            'playerDob',
            'playerJoinDate',
            'playerCreatedAt',
            'playerUpdatedAt',
            'playerLastSeen'
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

    public function skillStatsHistoryChart(Player $player){
        $results =  $this->getSkillStats($player)->take(10)->get();
        $results = $results->sortBy('created_at');

        $label = [];
        $data = [];

        if ($results != null){
            foreach ($results as $result){
                $label[] = $this->convertToDate($result->created_at);
            }

            $skills = $this->skillStatsLabel();

            foreach ($results as $result) {
                foreach ($skills as $skill => $value) {
                    $data[$skill][] = $result[$value];
                }
            }

//        if ($player->position->category == 'Forward'){
//            $data['label'] = [
//                'Controlling',
//                'Receiving',
//                'Dribbling',
//                'Shooting',
//                'Power Kicking',
//                'Offensive Play',
//            ];
//            foreach ($results as $result){
//                $data['Controlling'][] = $result->controlling;
//                $data['Receiving'][] = $result->recieving;
//                $data['Dribbling'][] = $result->dribbling;
//                $data['Shooting'][] = $result->shooting;
//                $data['Power Kicking'][] = $result->powerKicking;
//                $data['Offensive Play'][] = $result->offensivePlay;
//            }
//        }
//        elseif ($player->position->category == 'Midfielder'){
//            $data['label'] = [
//                'Controlling',
//                'Receiving',
//                'Dribbling',
//                'Passing',
//                'Ball Handling',
//                'Offensive Play',
//            ];
//
//            foreach ($results as $result){
//                $data['Controlling'][] = $result->controlling;
//                $data['Receiving'][] = $result->recieving;
//                $data['Dribbling'][] = $result->dribbling;
//                $data['Passing'][] = $result->passing;
//                $data['Ball Handling'][] = $result->ballHandling;
//                $data['Offensive Play'][] = $result->offensivePlay;
//            }
//        }
//        elseif ($player->position->category == 'Defender'){
//            $data['label'] = [
//                'Controlling',
//                'Receiving',
//                'Turning',
//                'Passing',
//                'Ball Handling',
//                'Defensive Play',
//            ];
//            foreach ($results as $result){
//                $data['Controlling'][] = $result->controlling;
//                $data['Receiving'][] = $result->recieving;
//                $data['Turning'][] = $result->turning;
//                $data['Passing'][] = $result->passing;
//                $data['Ball Handling'][] = $result->ballHandling;
//                $data['Defensive Play'][] = $result->defensivePlay;
//            }
//        }
//        elseif ($player->position->category == 'Defender' && $player->position->name == 'Goalkeeper (GK)' ){
//            $data['label'] = [
//                'Controlling',
//                'Receiving',
//                'Ball Handling',
//                'Passing',
//                'GoalKeeping',
//                'Defensive Play',
//            ];
//            foreach ($results as $result){
//                $data['Controlling'][] = $result->controlling;
//                $data['Receiving'][] = $result->recieving;
//                $data['Ball Handling'][] = $result->ballHandling;
//                $data['Passing'][] = $result->passing;
//                $data['GoalKeeping'][] = $result->goalKeeping;
//                $data['Defensive Play'][] = $result->defensivePlay;
//            }
//        }

        }
        return compact('label', 'data');
    }

    public function playerUpcomingMatches(Player $player)
    {
        $data = $this->eventScheduleRepository->playerUpcomingEvent($player, 'Match');
        return $this->eventScheduleService->makeDataTablesMatch($data);
    }
    public function playerUpcomingTraining(Player $player)
    {
        $data = $this->eventScheduleRepository->playerUpcomingEvent($player, 'Training');
        return $this->eventScheduleService->makeDataTablesTraining($data);
    }

    public function playerMatchCalendar(Player $player)
    {
        $data = $this->eventScheduleRepository->playerUpcomingEvent($player, 'Match');
        return $this->eventScheduleService->makeMatchCalendar($data);
    }
    public function playerTrainingCalendar(Player $player)
    {
        $data = $this->eventScheduleRepository->playerUpcomingEvent($player, 'Training');
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
        return $this->playerRepository->update($player, $data);
    }
    public function activate(Player $player)
    {
        return $this->userRepository->updateUserStatus($player, '1');
    }

    public function deactivate(Player $player)
    {
        return $this->userRepository->updateUserStatus($player, '0');
    }

    public function changePassword($data, Player $player){
        return $this->userRepository->changePassword($data, $player);
    }

    public function destroy(Player $player)
    {
        $this->deleteImage($player->user->foto);
        return $this->userRepository->delete($player);
    }
}
