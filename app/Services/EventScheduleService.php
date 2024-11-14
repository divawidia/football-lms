<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\MatchScore;
use App\Models\Player;
use App\Models\PlayerMatchStats;
use App\Models\ScheduleNote;
use App\Models\Team;
use App\Notifications\MatchSchedules\MatchScheduleCreatedForAdmin;
use App\Notifications\MatchSchedules\MatchScheduleCreatedForPlayerCoach;
use App\Notifications\MatchSchedules\MatchScheduleUpdatedForAdmin;
use App\Notifications\MatchSchedules\MatchScheduleUpdatedForPlayerCoach;
use App\Notifications\TrainingSchedules\TrainingScheduleCreatedForCoachAdmin;
use App\Notifications\TrainingSchedules\TrainingScheduleCreatedForPlayer;
use App\Notifications\TrainingSchedules\TrainingScheduleDeletedForCoachAdmin;
use App\Notifications\TrainingSchedules\TrainingScheduleUpdatedForCoachAdmin;
use App\Notifications\TrainingSchedules\TrainingScheduleUpdatedForPlayer;
use App\Repository\EventScheduleRepository;
use App\Repository\PlayerPerformanceReviewRepository;
use App\Repository\PlayerSkillStatsRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use function PHPUnit\Framework\isFalse;

class EventScheduleService extends Service
{
    private EventScheduleRepository $eventScheduleRepository;
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private PlayerSkillStatsRepository $playerSkillStatsRepository;
    private PlayerPerformanceReviewRepository $playerPerformanceReviewRepository;
    public function __construct(
        EventScheduleRepository $eventScheduleRepository,
        TeamRepository $teamRepository,
        UserRepository $userRepository,
        PlayerSkillStatsRepository $playerSkillStatsRepository,
        PlayerPerformanceReviewRepository $playerPerformanceReviewRepository)
    {
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->playerSkillStatsRepository = $playerSkillStatsRepository;
        $this->playerPerformanceReviewRepository = $playerPerformanceReviewRepository;
    }

    public function indexMatch(): Collection
    {
        return $this->eventScheduleRepository->getEvent('Match', '1');
    }
    public function indexTraining(): Collection
    {
        return $this->eventScheduleRepository->getEvent('Training', '1');
    }

    public function coachTeamsIndexTraining(Coach $coach): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($coach, 'Training', '1');
    }
    public function coachTeamsIndexMatch(Coach $coach): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($coach, 'Match', '1');
    }

    public function playerTeamsIndexTraining(Player $player): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($player, 'Training', '1');
    }
    public function playerTeamsIndexMatch(Player $player): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($player,  'Match', '1');
    }

    public function makeMatchCalendar($matchesData): array
    {
        $events = [];
        foreach ($matchesData as $match) {
            $events[] = [
                'id' => $match->id,
                'title' => $match->teams[0]->teamName .' Vs. '.$match->teams[1]->teamName,
                'start' => $match->date.' '.$match->startTime,
                'end' => $match->date.' '.$match->endTime,
                'className' => 'bg-primary text-white'
            ];
        }
        return $events;
    }
    public function makeTrainingCalendar($trainingsData): array
    {
        $events = [];
        foreach ($trainingsData as $training) {
            $events[] = [
                'id' => $training->id,
                'title' => $training->teams[0]->teamName.' - '.$training->eventName,
                'start' => $training->date.' '.$training->startTime,
                'end' => $training->date.' '.$training->endTime,
                'className' => 'bg-warning'
            ];
        }
        return $events;
    }

    public function matchCalendar(){
        $matches = $this->indexMatch();
        return $this->makeMatchCalendar($matches);
    }
    public function trainingCalendar(){
        $trainings = $this->indexTraining();
        return $this->makeTrainingCalendar($trainings);
    }

    public function coachTeamsTrainingCalendar(Coach $coach){
        $trainings = $this->coachTeamsIndexTraining($coach);
        return $this->makeTrainingCalendar($trainings);
    }
    public function coachTeamsMatchCalendar(Coach $coach){
        $data = $this->coachTeamsIndexMatch($coach);
        return $this->makeMatchCalendar($data);
    }

    public function playerTeamsTrainingCalendar(Player $player){
        $trainings = $this->playerTeamsIndexTraining($player);
        return $this->makeTrainingCalendar($trainings);
    }
    public function playerTeamsMatchCalendar(Player $player){
        $data = $this->playerTeamsIndexMatch($player);
        return $this->makeMatchCalendar($data);
    }

    public function makeDataTablesTraining($trainingData)
    {
        return Datatables::of($trainingData)
            ->addColumn('action', function ($item) {
                if (isAllAdmin() || isCoach()){
                    if ($item->status == '1') {
                        $statusButton = '<form action="' . route('deactivate-training', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">block</span> End Training
                                                </button>
                                            </form>';
                    } else {
                        $statusButton = '<form action="' . route('activate-training', $item->id) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">check_circle</span> Start Training
                                                </button>
                                            </form>';
                    }
                    return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('training-schedules.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Schedule</a>
                            <a class="dropdown-item" href="' . route('training-schedules.show', $item->id) . '"><span class="material-icons">visibility</span> View Schedule</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Schedule
                            </button>
                          </div>
                        </div>';
                } elseif (isPlayer()){
                    return '<a class="btn btn-sm btn-outline-secondary" href="' . route('training-schedules.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View training detail">
                                <span class="material-icons">
                                    visibility
                                </span>
                          </a>';
                }
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
            ->editColumn('date', function ($item) {
                $date = $this->convertToDate($item->date);
                $startTime = $this->convertToTime($item->startTime);
                $endTime = $this->convertToTime($item->endTime);
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    return '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Ended</span>';
                }
            })
            ->rawColumns(['action','team','date','status'])
            ->make();
    }
    public function makeDataTablesMatch($matchData)
    {
        return Datatables::of($matchData)
            ->addColumn('action', function ($item) {
                if (isCoach() || isPlayer()){
                    return '
                        <a class="btn btn-sm btn-outline-secondary" href="' . route('match-schedules.show', $item->id) . '" data-toggle="tooltips" data-placement="bottom" title="View Match Detail">
                            <span class="material-icons">
                                visibility
                            </span>
                        </a>';
                } elseif (isAllAdmin()){
                    if ($item->status == '1') {
                        $statusButton = '<form action="' . route('end-match', $item->id) . '" method="POST">
                                            ' . method_field("PATCH") . '
                                            ' . csrf_field() . '
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">block</span> End Match
                                            </button>
                                        </form>';
                    } else {
                        $statusButton = '<form action="' . route('activate-match', $item->id) . '" method="POST">
                                            ' . method_field("PATCH") . '
                                            ' . csrf_field() . '
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">check_circle</span> Start Match
                                            </button>
                                        </form>';
                    }
                    return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('match-schedules.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Schedule</a>
                            <a class="dropdown-item" href="' . route('match-schedules.show', $item->id) . '"><span class="material-icons">visibility</span> View Schedule</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Schedule
                            </button>
                          </div>
                        </div>';
                }
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
            ->editColumn('competition', function ($item) {
                if ($item->competition){
                    $competition = '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->competition->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->competition->name . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->competition->type.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                }else{
                    $competition = 'No Competition';
                }
                return $competition;
            })
            ->editColumn('date', function ($item) {
                $date = $this->convertToDate($item->date);
                $startTime = $this->convertToTime($item->startTime);
                $endTime = $this->convertToTime($item->endTime);
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    return '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Ended</span>';
                }
            })
            ->rawColumns(['action','team', 'competition','opponentTeam','date','status'])
            ->make();
    }

    public function dataTablesTraining(){
        $data = $this->indexTraining();
        return $this->makeDataTablesTraining($data);
    }
    public function dataTablesMatch(){
        $data = $this->indexMatch();
        return $this->makeDataTablesMatch($data);
    }

    public function coachTeamsDataTablesTraining(Coach $coach){
        $data = $this->coachTeamsIndexTraining($coach);
        return $this->makeDataTablesTraining($data);
    }
    public function coachTeamsDataTablesMatch(Coach $coach){
        $data = $this->coachTeamsIndexMatch($coach);
        return $this->makeDataTablesMatch($data);
    }

    public function playerTeamsDataTablesTraining(Player $player){
        $data = $this->playerTeamsIndexTraining($player);
        return $this->makeDataTablesTraining($data);
    }
    public function playerTeamsDataTablesMatch(Player $player){
        $data = $this->playerTeamsIndexMatch($player);
        return $this->makeDataTablesMatch($data);
    }

    public function dataTablesPlayerStats(EventSchedule $schedule){
        $data = $schedule->playerMatchStats;
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if (isAllAdmin() || isCoach()){
                    $showPlayer = '<div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit-player-stats" href="" id="'.$item->id.'"><span class="material-icons">edit</span> Edit Player Stats</a>
                            <a class="dropdown-item" href="' . route('player-managements.show', ['player'=>$item->id]) . '"><span class="material-icons">visibility</span> View Player</a>
                          </div>
                        </div>';
                } else {
                    $showPlayer = '';
                }
                return $showPlayer;
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
                                        <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>
                            </div>
                        </div>';
            })
            ->editColumn('updated_at', function ($item) {
                $date = date('M d, Y h:i A', strtotime($item->updated_at));
                return $date;
            })
            ->rawColumns(['action','name','updated_at'])
            ->make();
    }

    public function dataTablesPlayerSkills(EventSchedule $schedule){
        $data = $schedule->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($schedule){
                $stats = $item->playerSkillStats()->where('eventId', $schedule->id)->first();
                $review = $item->playerPerformanceReview()->where('eventId', $schedule->id)->first();

                if (isAllAdmin()){
                    $button = '<a class="dropdown-item" href="' . route('player-managements.skill-stats', ['player'=>$item->id]) . '"><span class="material-icons">visibility</span> View Player Skill Stats</a>';
                } elseif(isCoach()){
                    if (!$stats){
                        $statsBtn = '<a class="dropdown-item addSkills" id="'.$item->id.'" data-eventId="'.$schedule->id.'"><span class="material-icons">edit</span> Evaluate Player Skills Stats</a>';
                    } else {
                        $statsBtn = '<a class="dropdown-item editSkills" id="'.$item->id.'" data-eventId="'.$schedule->id.'" data-statsId="'.$stats->id.'"><span class="material-icons">edit</span> Edit Player Skills Stats</a>';
                    }

                    if (!$review){
                        $reviewBtn = '<a class="dropdown-item addPerformanceReview" id="'.$item->id.'" data-eventId="'.$schedule->id.'"><span class="material-icons">add</span> Add Player Performance Review</a>';
                    } else {
                        $reviewBtn = '<a class="dropdown-item editPerformanceReview" id="'.$item->id.'" data-eventId="'.$schedule->id.'"  data-reviewId="'.$review->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>';
                    }

                    $button = '<div class="dropdown">
                                      <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="' . route('player-managements.skill-stats', ['player'=>$item->id]) . '"><span class="material-icons">visibility</span> View Player Skill Stats</a>
                                            '.$statsBtn.'
                                            '.$reviewBtn.'
                                      </div>
                                </div>';
                }
                return $button;
            })
            ->editColumn('name', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center" style="white-space: nowrap;">
                            <div class="avatar avatar-sm mr-8pt">
                                <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                            </div>
                            <div class="media-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex d-flex flex-column">
                                        <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->editColumn('stats_status', function ($item) use ($schedule){
                $stats = $item->playerSkillStats()->where('eventId', $schedule->id)->first();
                if ($stats){
                    $date = 'Skill stats have been added';
                } else{
                    $date = 'Skill stats still not added yet';
                }
                return $date;
            })
            ->editColumn('stats_created', function ($item) use ($schedule){
                $stats = $item->playerSkillStats()->where('eventId', $schedule->id)->first();
                if ($stats){
                    $date = date('M d, Y h:i A', strtotime($stats->created_at));
                } else{
                    $date = '-';
                }
                return $date;
            })
            ->editColumn('stats_updated', function ($item) use ($schedule){
                $stats = $item->playerSkillStats()->where('eventId', $schedule->id)->first();
                if ($stats){
                    $date = date('M d, Y h:i A', strtotime($stats->updated_at));
                } else{
                    $date = '-';
                }
                return $date;
            })
            ->editColumn('performance_review', function ($item) use ($schedule){
                $review = $item->playerPerformanceReview()->where('eventId', $schedule->id)->first();
                if ($review){
                    $text = $review->performanceReview;
                } else{
                    $text = 'Performance review still not added yet';
                }
                return $text;
            })
            ->editColumn('performance_review_created', function ($item) use ($schedule){
                $review = $item->playerPerformanceReview()->where('eventId', $schedule->id)->first();
                if ($review){
                    $text = date('M d, Y h:i A', strtotime($review->created_at));
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->editColumn('performance_review_last_updated', function ($item) use ($schedule){
                $review = $item->playerPerformanceReview()->where('eventId', $schedule->id)->first();
                if ($review){
                    $text = date('M d, Y h:i A', strtotime($review->updated_at));
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->rawColumns(['action','name', 'stats_status', 'stats_created', 'stats_updated', 'performance_review', 'performance_review_created','performance_review_last_updated'])
            ->make();
    }

    public function show(EventSchedule $schedule, Player $player = null){
        $totalParticipant = count($schedule->players) + count($schedule->coaches);

        $playerAttended = $schedule->players()
            ->where('attendanceStatus', 'Attended')
            ->get();

        $playerIllness = $schedule->players()
            ->where('attendanceStatus', 'Illness')
            ->get();
        $playerInjured = $schedule->players()
            ->where('attendanceStatus', 'Injured')
            ->get();
        $playerOther = $schedule->players()
            ->where('attendanceStatus', 'Other')
            ->get();
        $playerDidntAttend = count($playerIllness) + count($playerInjured) + count($playerOther);

        $coachAttended = $schedule->coaches()
            ->where('attendanceStatus', 'Attended')
            ->get();

        $coachIllness = $schedule->coaches()
            ->where('attendanceStatus', 'Illness')
            ->get();
        $coachInjured = $schedule->coaches()
            ->where('attendanceStatus', 'Injured')
            ->get();
        $coachOther = $schedule->coaches()
            ->where('attendanceStatus', 'Other')
            ->get();
        $coachDidntAttend = count($coachIllness) + count($coachInjured) + count($coachOther);

        $totalAttend = count($playerAttended) + count($coachAttended);
        $totalDidntAttend = $playerDidntAttend + $coachDidntAttend;
        $totalIllness = count($playerIllness) + count($coachIllness);
        $totalInjured = count($playerInjured) + count($coachInjured);
        $totalOthers = count($playerOther) + count($coachOther);

        $dataSchedule = $schedule;

        $allSkills = null;
        $playerPerformanceReviews = null;
        if ($player){
            $allSkills = $this->playerSkillStatsRepository->getByPlayer($player, $schedule)->first();
            $playerPerformanceReviews = $this->playerPerformanceReviewRepository->getByPlayer($player, $schedule);

        }

        return compact('totalParticipant', 'totalAttend', 'totalDidntAttend', 'totalIllness', 'totalInjured', 'totalOthers', 'dataSchedule', 'allSkills', 'playerPerformanceReviews');
    }

    public function getFriendlyMatchTeam()
    {
        $teams = $this->teamRepository->getByTeamside('Academy Team');
        $opponentTeams = $this->teamRepository->getByTeamside('Opponent Team');
        return compact('teams', 'opponentTeams');
    }
    public function createTraining(Coach $coach = null)
    {
        if ($coach){
            $teams = $this->teamRepository->getCoachManagedTeams($coach);
        } else {
            $teams = $this->teamRepository->getByTeamside('Academy Team');
        }
        return $teams;
    }

    public function storeTraining(array $data, $userId){
        $data['userId'] = $userId;
        $data['eventType'] = 'Training';
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $schedule =  $this->eventScheduleRepository->create($data);

        $team = $this->teamRepository->find($data['teamId']);

        $loggedUser = $this->userRepository->find($userId);
        $creatorUserName = $this->getUserFullName($loggedUser);

        $teamsCoachesAdmins = $this->userRepository->allTeamsParticipant($team, players: false);
        Notification::send($teamsCoachesAdmins, new TrainingScheduleCreatedForCoachAdmin($schedule, $team, $creatorUserName));

        $teamsPlayers = $this->userRepository->allTeamsParticipant($team, admins: false, coaches: false);
        Notification::send($teamsPlayers, new TrainingScheduleCreatedForPlayer($schedule, $team));

        $schedule->teams()->attach($data['teamId']);
        $schedule->players()->attach($team->players);
        $schedule->coaches()->attach($team->coaches);

        return $schedule;
    }
    public function storeMatch(array $data, $userId){
        $data['userId'] = $userId;
        $data['eventType'] = 'Match';
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $schedule =  $this->eventScheduleRepository->create($data);

        $schedule->teams()->attach($data['teamId']);
        $schedule->teams()->attach($data['opponentTeamId']);

        $loggedUser = $this->userRepository->find($userId);
        $creatorUserName = $this->getUserFullName($loggedUser);

        if ($data['isOpponentTeamMatch'] == '0'){
            $team = $this->teamRepository->find($data['teamId']);

            Notification::send($this->userRepository->getAllAdminUsers(), new MatchScheduleCreatedForAdmin($schedule, $team, $creatorUserName));

            $teamsPlayersCoaches = $this->userRepository->allTeamsParticipant($team, admins: false);
            Notification::send($teamsPlayersCoaches, new MatchScheduleCreatedForPlayerCoach($schedule));

            $schedule->players()->attach($team->players);
            $schedule->playerMatchStats()->attach($team->players, ['teamId' => $team->id]);
            $schedule->coaches()->attach($team->coaches);
            $schedule->coachMatchStats()->attach($team->coaches, ['teamId' => $team->id]);
        }

        return $schedule;
    }

    public function updateTraining(array $data, EventSchedule $schedule, $loggedUser){
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $schedule->update($data);

        if (array_key_exists('teamId', $data)){
            $team = $this->teamRepository->find($data['teamId']);

            $creatorUserName = $this->getUserFullName($loggedUser);

            $teamsCoachesAdmins = $this->userRepository->allTeamsParticipant($team, players: false);
            Notification::send($teamsCoachesAdmins, new TrainingScheduleUpdatedForCoachAdmin($schedule, $team, $creatorUserName, 'Has Been Updated'));

            $teamsPlayers = $this->userRepository->allTeamsParticipant($team, admins: false, coaches: false);
            Notification::send($teamsPlayers, new TrainingScheduleUpdatedForPlayer($schedule, $team, 'Has Been Updated'));

            $schedule->teams()->sync($data['teamId']);
            $schedule->players()->sync($team->players);
            $schedule->coaches()->sync($team->coaches);
        }
        return $schedule;
    }
    public function updateMatch(array $data, EventSchedule $schedule, $loggedUser){
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $schedule->update($data);

        $team = $schedule->teams()->first();

        $creatorUserName = $this->getUserFullName($loggedUser);

        Notification::send($this->userRepository->getAllAdminUsers(), new MatchScheduleUpdatedForAdmin($schedule, $creatorUserName, 'Has Been Updated'));

        $teamsPlayersCoaches = $this->userRepository->allTeamsParticipant($team, admins: false);
        Notification::send($teamsPlayersCoaches, new MatchScheduleUpdatedForPlayerCoach($schedule, 'Has Been Updated'));

//        $team = Team::with('players', 'coaches')->where('id', $data['teamId'])->where('teamSide', 'Academy Team')->first();
//        $schedule->teams()->sync([$data['teamId'], $data['opponentTeamId']]);
//        $schedule->players()->sync($team->players);
//        $schedule->coaches()->sync($team->coaches);
        return $schedule;
    }

    public function setStatus(EventSchedule $schedule, $status): EventSchedule
    {
        $this->eventScheduleRepository->updateStatus($schedule, $status);

        $statusMessages = [
            'Ongoing' => 'is now ongoing',
            'Completed' => 'have been completed',
            'Cancelled' => 'have been cancelled',
            'Scheduled' => 'have been set to scheduled',
        ];

        $team = $schedule->teams()->first();
        $teamParticipants = $this->userRepository->allTeamsParticipant($team);

        // Check if the status exists in the defined mapping
        if (array_key_exists($status, $statusMessages)) {
            $statusMessage = $statusMessages[$status];

            if ($schedule->eventType == 'Training') {
                Notification::send($teamParticipants, new TrainingScheduleUpdatedForPlayer($schedule, $team, $statusMessage));
            } elseif ($schedule->eventType == 'Training' && $schedule->isOpponentTeamMatch == '0') {
                Notification::send($teamParticipants, new MatchScheduleUpdatedForPlayerCoach($schedule, $statusMessage));
            }

        }

        return $schedule;
    }


    public function endMatch(EventSchedule $schedule)
    {

        $academyTeamScore = $schedule->teams[0]->pivot->teamScore;
        $opponentTeamScore = $schedule->teams[1]->pivot->teamScore;

        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['goalConceded'=> $opponentTeamScore]);
        $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['goalConceded'=> $academyTeamScore]);

        $academyTeamGoalsDifference = $academyTeamScore - $opponentTeamScore;
        $opponentTeamGoalsDifference = $opponentTeamScore - $academyTeamScore;

        // update match result data of each coach match stats
        foreach ($schedule->coaches as $coach){
            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['goalConceded' => $opponentTeamScore]);
        }

        // chack if match is in competition
        if ($schedule->competition()->exists()){
            //get group division data of our teams
            $groupDivision = $schedule->competition->groups()
                ->whereRelation('teams','teamId', $schedule->teams[0]->id)
                ->first();

            $academyTeam = $groupDivision->teams()->where('teamId', $schedule->teams[0]->id)->first();
            $opponentTeam = $groupDivision->teams()->where('teamId', $schedule->teams[0]->id)->first();
        }

        // if our teams win the match
        if ($academyTeamScore > $opponentTeamScore){
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['resultStatus'=> 'Win']);
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['resultStatus'=> 'Lose']);

            // update match result data of each coach match stats
            foreach ($schedule->coaches as $coach){
                $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['resultStatus' => 'Win']);
            }

            if ($schedule->competition()->exists()){
                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[0]->id, [
                        'matchPlayed'=> $academyTeam->pivot->matchPlayed + 1,
                        'won'=> $academyTeam->pivot->won + 1,
                        'goalsFor'=> $academyTeam->pivot->goalsFor + $academyTeamScore,
                        'goalsAgaints'=> $academyTeam->pivot->goalsAgaints + $opponentTeamScore,
                        'goalsDifference'=> $academyTeam->pivot->goalsDifference + $academyTeamGoalsDifference,
                        'points'=> $academyTeam->pivot->points + 3,
                        'redCards'=> $academyTeam->pivot->redCards + $schedule->teams[0]->pivot->teamRedCards,
                        'yellowCards'=> $academyTeam->pivot->yellowCards + $schedule->teams[0]->pivot->teamYellowCards,
                    ]);

                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[1]->id, [
                        'matchPlayed'=> $opponentTeam->pivot->matchPlayed + 1,
                        'lost'=> $opponentTeam->pivot->lost + 1,
                        'goalsFor'=> $opponentTeam->pivot->goalsFor + $opponentTeamScore,
                        'goalsAgaints'=> $opponentTeam->pivot->goalsAgaints + $academyTeamScore,
                        'goalsDifference'=> $opponentTeam->pivot->goalsDifference + $opponentTeamGoalsDifference,
                        'points'=> $opponentTeam->pivot->points + 0,
                        'redCards'=> $opponentTeam->pivot->redCards + $schedule->teams[1]->pivot->teamRedCards,
                        'yellowCards'=> $opponentTeam->pivot->yellowCards + $schedule->teams[1]->pivot->teamYellowCards,
                    ]);
            }

        } elseif ($academyTeamScore < $opponentTeamScore){
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['resultStatus'=> 'Win']);
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['resultStatus'=> 'Lose']);
            // update match result data of each coach match stats
            foreach ($schedule->coaches as $coach){
                $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['resultStatus' => 'Lose']);
            }

            if ($schedule->competition()->exists()){
                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[0]->id, [
                        'matchPlayed'=> $academyTeam->pivot->matchPlayed + 1,
                        'lost'=> $academyTeam->pivot->lost + 1,
                        'goalsFor'=> $academyTeam->pivot->goalsFor + $academyTeamScore,
                        'goalsAgaints'=> $academyTeam->pivot->goalsAgaints + $opponentTeamScore,
                        'goalsDifference'=> $academyTeam->pivot->goalsDifference + $academyTeamGoalsDifference,
                        'points'=> $academyTeam->pivot->points + 0,
                        'redCards'=> $academyTeam->pivot->redCards + $schedule->teams[0]->pivot->teamRedCards,
                        'yellowCards'=> $academyTeam->pivot->yellowCards + $schedule->teams[0]->pivot->teamYellowCards,
                    ]);

                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[1]->id, [
                        'matchPlayed'=> $opponentTeam->pivot->matchPlayed + 1,
                        'won'=> $opponentTeam->pivot->won + 1,
                        'goalsFor'=> $opponentTeam->pivot->goalsFor + $opponentTeamScore,
                        'goalsAgaints'=> $opponentTeam->pivot->goalsAgaints + $academyTeamScore,
                        'goalsDifference'=> $opponentTeam->pivot->goalsDifference + $opponentTeamGoalsDifference,
                        'points'=> $opponentTeam->pivot->points + 3,
                        'redCards'=> $opponentTeam->pivot->redCards + $schedule->teams[1]->pivot->teamRedCards,
                        'yellowCards'=> $opponentTeam->pivot->yellowCards + $schedule->teams[1]->pivot->teamYellowCards,
                    ]);
            }

        }elseif ($academyTeamScore == $opponentTeamScore){
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['resultStatus'=> 'Draw']);
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['resultStatus'=> 'Draw']);
            // update match result data of each coach match stats
            foreach ($schedule->coaches as $coach){
                $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['resultStatus' => 'Draw']);
            }

            if ($schedule->competition()->exists()){
                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[0]->id, [
                        'matchPlayed'=> $academyTeam->pivot->matchPlayed + 1,
                        'drawn'=> $academyTeam->pivot->drawn + 1,
                        'goalsFor'=> $academyTeam->pivot->goalsFor + $academyTeamScore,
                        'goalsAgaints'=> $academyTeam->pivot->goalsAgaints + $opponentTeamScore,
                        'goalsDifference'=> $academyTeam->pivot->goalsDifference + $academyTeamGoalsDifference,
                        'points'=> $academyTeam->pivot->points + 1,
                        'redCards'=> $academyTeam->pivot->redCards + $schedule->teams[0]->pivot->teamRedCards,
                        'yellowCards'=> $academyTeam->pivot->yellowCards + $schedule->teams[0]->pivot->teamYellowCards,
                    ]);

                $groupDivision->teams()
                    ->updateExistingPivot($schedule->teams[1]->id, [
                        'matchPlayed'=> $opponentTeam->pivot->matchPlayed + 1,
                        'drawn'=> $opponentTeam->pivot->drawn + 1,
                        'goalsFor'=> $opponentTeam->pivot->goalsFor + $opponentTeamScore,
                        'goalsAgaints'=> $opponentTeam->pivot->goalsAgaints + $academyTeamScore,
                        'goalsDifference'=> $opponentTeam->pivot->goalsDifference + $opponentTeamGoalsDifference,
                        'points'=> $opponentTeam->pivot->points + 1,
                        'redCards'=> $opponentTeam->pivot->redCards + $schedule->teams[1]->pivot->teamRedCards,
                        'yellowCards'=> $opponentTeam->pivot->yellowCards + $schedule->teams[1]->pivot->teamYellowCards,
                    ]);
            }
        }

        if ($academyTeamScore == 0){
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['cleanSheets'=> 1]);

            // update clean sheets data of each coach match stats
            foreach ($schedule->coaches as $coach){
                $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['cleanSheets' => 1]);
            }
        }

        if ($opponentTeamScore == 0){
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['cleanSheets'=> 1]);
        }

        return $schedule->update(['status' => '0']);
    }


    public function getPlayerAttendance(EventSchedule $schedule, Player $player)
    {
        return $schedule->players()->find($player->id);
    }
    public function getCoachAttendance(EventSchedule $schedule, Coach $coach)
    {
        return $schedule->coaches()->find($coach->id);
    }

    public function updatePlayerAttendanceStatus($data, EventSchedule $schedule, Player $player){
        return $schedule->players()->updateExistingPivot($player->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
    }
    public function updateCoachAttendanceStatus($data, EventSchedule $schedule, Coach $coach){
        return $schedule->coaches()->updateExistingPivot($coach->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
    }

    public function createNote($data, EventSchedule $schedule){
        $data['scheduleId'] = $schedule->id;
        return ScheduleNote::create($data);
    }
    public function updateNote($data, EventSchedule $schedule, ScheduleNote $note){
        return $note->update($data);
    }
    public function destroyNote(EventSchedule $schedule, ScheduleNote $note)
    {
        return $note->delete();
    }

    public function storeMatchScorer($data, EventSchedule $schedule)
    {
        $data['eventId'] = $schedule->id;
        $data['isOwnGoal'] = '0';
        MatchScore::create($data);
        $player = $schedule->playerMatchStats()->find($data['playerId']);
        $assistPlayer = $schedule->playerMatchStats()->find($data['assistPlayerId']);

        $playerGoal = $player->pivot->goals + 1;
        $playerAssist = $assistPlayer->pivot->assists + 1;
        $teamScore = $schedule->teams[0]->pivot->teamScore + 1;

        $schedule->playerMatchStats()->updateExistingPivot($data['playerId'], ['goals' => $playerGoal]);
        $schedule->playerMatchStats()->updateExistingPivot($data['assistPlayerId'], ['assists' => $playerAssist]);
        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamScore' => $teamScore]);

        // update team score data of each coach match stats
        foreach ($schedule->coaches as $coach){
            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['teamScore' => $teamScore]);
        }

        return $schedule;
    }
    public function destroyMatchScorer(EventSchedule $schedule, MatchScore $scorer)
    {
        $player = $schedule->playerMatchStats()->find($scorer->playerId);
        $assistPlayer = $schedule->playerMatchStats()->find($scorer->assistPlayerId);

        $teamScore = $schedule->teams[0]->pivot->teamScore - 1;
        $playerGoal = $player->pivot->goals - 1;
        $playerAssist = $assistPlayer->pivot->assists - 1;

        $schedule->playerMatchStats()->updateExistingPivot($scorer->playerId, ['goals' => $playerGoal]);
        $schedule->playerMatchStats()->updateExistingPivot($scorer->assistPlayerId, ['assists' => $playerAssist]);
        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamScore' => $teamScore]);

        // update team score data of each coach match stats
        foreach ($schedule->coaches as $coach){
            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['teamScore' => $teamScore]);
        }
        return $scorer->delete();
    }

    public function storeOwnGoal($data, EventSchedule $schedule)
    {
        $data['eventId'] = $schedule->id;
        $data['isOwnGoal'] = '1';
        MatchScore::create($data);
        $player = $schedule->playerMatchStats()->find($data['playerId']);

        $playerOwnGoal = $player->pivot->ownGoal + 1;
        $opponentTeamScore = $schedule->teams[1]->pivot->teamScore + 1;
        $teamOwnGoal = $schedule->teams[0]->pivot->teamOwnGoal + 1;

        $schedule->playerMatchStats()->updateExistingPivot($data['playerId'], ['ownGoal' => $playerOwnGoal]);
        $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamScore' => $opponentTeamScore]);
        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamOwnGoal' => $teamOwnGoal]);

        // update own goal data of each coach match stats
        foreach ($schedule->coaches as $coach){
            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['teamOwnGoal' => $teamOwnGoal]);
        }

        return $schedule;
    }

    public function destroyOwnGoal(EventSchedule $schedule, MatchScore $scorer)
    {
        $player = $schedule->playerMatchStats()->find($scorer->playerId);

        $opponentTeamScore = $schedule->teams[1]->pivot->teamScore - 1;
        $playerOwnGoal = $player->pivot->ownGoal - 1;
        $teamOwnGoal = $schedule->teams[0]->pivot->teamOwnGoal - 1;

        $schedule->playerMatchStats()->updateExistingPivot($scorer->playerId, ['ownGoal' => $playerOwnGoal]);
        $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamScore' => $opponentTeamScore]);
        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamOwnGoal' => $teamOwnGoal]);

        // update own goal data of each coach match stats
        foreach ($schedule->coaches as $coach){
            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['teamOwnGoal' => $teamOwnGoal]);
        }

        return $scorer->delete();
    }

    public function updateMatchStats(array $data, EventSchedule $schedule)
    {
//        $schedule->coachMatchStats()->updateExistingPivot($schedule->teams[0]->id, [
//                'teamPossesion' => $data['teamAPossession'],
//                'teamShotOnTarget' => $data['teamAShotOnTarget'],
//                'teamShots' => $data['teamAShots'],
//                'teamTouches' => $data['teamATouches'],
//                'teamTackles' => $data['teamATackles'],
//                'teamClearances' => $data['teamAClearances'],
//                'teamCorners' => $data['teamACorners'],
//                'teamOffsides' => $data['teamAOffsides'],
//                'teamYellowCards' => $data['teamAYellowCards'],
//                'teamRedCards' => $data['teamARedCards'],
//                'teamFoulsConceded' => $data['teamAFoulsConceded'],
//                'teamPasses' => $data['teamAPasses'],
//            ]);

        if ($schedule->isOpponentTeamMatch == '1'){
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, [
                'teamScore' => $data['teamATeamScore'],
                'teamOwnGoal' => $data['teamAOwnGoal'],
                'teamPossesion' => $data['teamAPossession'],
                'teamShotOnTarget' => $data['teamAShotOnTarget'],
                'teamShots' => $data['teamAShots'],
                'teamTouches' => $data['teamATouches'],
                'teamTackles' => $data['teamATackles'],
                'teamClearances' => $data['teamAClearances'],
                'teamCorners' => $data['teamACorners'],
                'teamOffsides' => $data['teamAOffsides'],
                'teamYellowCards' => $data['teamAYellowCards'],
                'teamRedCards' => $data['teamARedCards'],
                'teamFoulsConceded' => $data['teamAFoulsConceded'],
                'teamPasses' => $data['teamAPasses'],
            ]);
        } else {
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, [
                'teamPossesion' => $data['teamAPossession'],
                'teamShotOnTarget' => $data['teamAShotOnTarget'],
                'teamShots' => $data['teamAShots'],
                'teamTouches' => $data['teamATouches'],
                'teamTackles' => $data['teamATackles'],
                'teamClearances' => $data['teamAClearances'],
                'teamCorners' => $data['teamACorners'],
                'teamOffsides' => $data['teamAOffsides'],
                'teamYellowCards' => $data['teamAYellowCards'],
                'teamRedCards' => $data['teamARedCards'],
                'teamFoulsConceded' => $data['teamAFoulsConceded'],
                'teamPasses' => $data['teamAPasses'],
            ]);
            // update match stats data of each coach match stats
            foreach ($schedule->coaches as $coach){
                $schedule->coachMatchStats()->updateExistingPivot($coach->id, [
                    'teamPossesion' => $data['teamAPossession'],
                    'teamShotOnTarget' => $data['teamAShotOnTarget'],
                    'teamShots' => $data['teamAShots'],
                    'teamTouches' => $data['teamATouches'],
                    'teamTackles' => $data['teamATackles'],
                    'teamClearances' => $data['teamAClearances'],
                    'teamCorners' => $data['teamACorners'],
                    'teamOffsides' => $data['teamAOffsides'],
                    'teamYellowCards' => $data['teamAYellowCards'],
                    'teamRedCards' => $data['teamARedCards'],
                    'teamFoulsConceded' => $data['teamAFoulsConceded'],
                    'teamPasses' => $data['teamAPasses'],
                ]);
            }

        }

        $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, [
            'teamScore' => $data['teamBTeamScore'],
            'teamOwnGoal' => $data['teamBOwnGoal'],
            'teamPossesion' => $data['teamBPossession'],
            'teamShotOnTarget' => $data['teamBShotOnTarget'],
            'teamShots' => $data['teamBShots'],
            'teamTouches' => $data['teamBTouches'],
            'teamTackles' => $data['teamBTackles'],
            'teamClearances' => $data['teamBClearances'],
            'teamCorners' => $data['teamBCorners'],
            'teamOffsides' => $data['teamBOffsides'],
            'teamYellowCards' => $data['teamBYellowCards'],
            'teamRedCards' => $data['teamBRedCards'],
            'teamFoulsConceded' => $data['teamBFoulsConceded'],
            'teamPasses' => $data['teamBPasses'],
        ]);

        return $schedule;
    }
    public function getPlayerStats(EventSchedule $schedule, Player $player)
    {
        $data = $schedule->playerMatchStats()->find($player->id);
        $playerData = $data->user;
        $statsData = $data->pivot;
        return compact('playerData', 'statsData');
    }
    public function updatePlayerStats(array $data, EventSchedule $schedule, Player $player)
    {
//        $schedule->playerMatchStats()->updateExistingPivot($player->id, [
//            'minutesPlayed' => $data['minutesPlayed'],
//            'shots' => $data['shots'],
//            'passes' => $data['passes'],
//            'fouls' => $data['fouls'],
//            'yellowCards' => $data['yellowCards'],
//            'redCards' => $data['redCards'],
//            'saves' => $data['saves'],
//        ]);
//        dd($schedule);

        return $schedule->playerMatchStats()->updateExistingPivot($player->id, $data);
    }

    public function destroy(EventSchedule $schedule, $loggedUser)
    {
        $schedule->teams()->detach();
        $schedule->players()->detach();
        $schedule->coaches()->detach();

        $deletedBy = $this->getUserFullName($loggedUser);
        $team = $schedule->teams()->first();
        $allTeamsParticipant = $this->userRepository->allTeamsParticipant($team);

        if ($schedule->eventType == 'Training') {
            Notification::send($allTeamsParticipant, new TrainingScheduleDeletedForCoachAdmin($schedule, $team, $deletedBy));
        } elseif ($schedule->eventType == 'Match' && $schedule->isOpponentTeamMatch == '0') {

        }

        $schedule->delete();
        return $schedule;
    }
}
