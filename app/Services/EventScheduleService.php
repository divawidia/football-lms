<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\MatchScore;
use App\Models\Player;
use App\Models\MatchNote;
use App\Models\Team;
use App\Notifications\MatchSchedules\MatchNote as MatchNoteNotification;
use App\Notifications\MatchSchedules\MatchScheduleAttendance;
use App\Notifications\MatchSchedules\MatchSchedule;
use App\Notifications\MatchSchedules\MatchStatsPlayer;
use App\Notifications\TrainingSchedules\TrainingNote;
use App\Notifications\TrainingSchedules\TrainingScheduleAttendance;
use App\Notifications\TrainingSchedules\TrainingSchedule;
use App\Repository\EventScheduleRepository;
use App\Repository\Interface\LeagueStandingRepositoryInterface;
use App\Repository\PlayerPerformanceReviewRepository;
use App\Repository\PlayerSkillStatsRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class EventScheduleService extends Service
{
    private EventScheduleRepository $eventScheduleRepository;
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private PlayerSkillStatsRepository $playerSkillStatsRepository;
    private PlayerPerformanceReviewRepository $playerPerformanceReviewRepository;
    private LeagueStandingRepositoryInterface $leagueStandingRepository;
    private DatatablesHelper $datatablesService;
    public function __construct(
        EventScheduleRepository           $eventScheduleRepository,
        TeamRepository                    $teamRepository,
        UserRepository                    $userRepository,
        PlayerSkillStatsRepository        $playerSkillStatsRepository,
        PlayerPerformanceReviewRepository $playerPerformanceReviewRepository,
        LeagueStandingRepositoryInterface $leagueStandingRepository,
        DatatablesHelper                  $datatablesService)
    {
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->playerSkillStatsRepository = $playerSkillStatsRepository;
        $this->playerPerformanceReviewRepository = $playerPerformanceReviewRepository;
        $this->leagueStandingRepository = $leagueStandingRepository;
        $this->datatablesService = $datatablesService;
    }

    public function indexMatch(): Collection
    {
        return $this->eventScheduleRepository->getEvent(['Scheduled', 'Ongoing'], 'Match');
    }
    public function indexTraining(): Collection
    {
        return $this->eventScheduleRepository->getEvent( ['Scheduled', 'Ongoing'], 'Training');
    }

    public function coachTeamsIndexTraining(Coach $coach): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($coach, 'Training', ['Scheduled', 'Ongoing']);
    }
    public function coachTeamsIndexMatch(Coach $coach): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($coach, 'Match',  ['Scheduled', 'Ongoing']);
    }

    public function playerTeamsIndexTraining(Player $player): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($player, 'Training',  ['Scheduled', 'Ongoing']);
    }
    public function playerTeamsIndexMatch(Player $player): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($player,  'Match',  ['Scheduled', 'Ongoing']);
    }

    public function makeMatchCalendar($matchesData): array
    {
        $events = [];
        foreach ($matchesData as $match) {
            $awayTeam = $match->matchType == 'Internal Match' ? $match->awayTeam->teamName : $match->externalTeam->teamName;
            $events[] = [
                'id' => $match->id,
                'title' => $match->homeTeam->teamName .' Vs. '.$awayTeam,
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
                if (isCoach() || isPlayer()){
                    return $this->datatablesService->buttonTooltips(route('training-schedules.show', $item->hash), 'View Training detail', 'visibility');
                }
                elseif ( isAllAdmin() ) {
                    $statusButton = '';
                    $editButton = '';
                    if ($item->status == 'Scheduled'){
                        $statusButton = $this->datatablesService->buttonDropdownItem('cancelBtn', $item->id, 'danger', icon: 'block', btnText: 'Cancel Schedule');
                        $editButton = $this->datatablesService->buttonDropdownItem('edit-training-btn', $item->id,  icon: 'edit', btnText: 'Edit Training');
                    } elseif ($item->status == 'Cancelled') {
                        $statusButton = $this->datatablesService->buttonDropdownItem('scheduled-btn', $item->id, 'warning', icon: 'check_circle', btnText: 'Set Training to Scheduled');
                        $editButton = $this->datatablesService->buttonDropdownItem('edit-training-btn', $item->id,  icon: 'edit', btnText: 'Edit Training');
                    }
                    return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('training-schedules.show', $item->hash) . '"><span class="material-icons">visibility</span> View Schedule</a>
                            ' . $editButton . '
                            ' . $statusButton . '
                            '.$this->datatablesService->buttonDropdownItem('delete', $item->id, iconColor: 'danger', icon: 'delete', btnText: 'Delete Match').'
                          </div>
                        </div>';
                }
            })
            ->editColumn('team', function ($item) {
                return $this->datatablesService->name($item->teams[0]->logo, $item->teams[0]->teamName, $item->teams[0]->ageGroup, route('team-managements.show', $item->teams[0]->hash));
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesService->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->eventStatus($item->status);
            })
            ->rawColumns(['action','team','date','status'])
            ->make();
    }
    public function makeDataTablesMatch($matchData)
    {
        return Datatables::of($matchData)
            ->addColumn('action', function ($item) {
                if (isCoach() || isPlayer()){
                    return $this->datatablesService->buttonTooltips(route('match-schedules.show', $item->hash), 'View Match detail', 'visibility');
                }
                elseif ( isAllAdmin() ) {
                    $statusButton = '';
                    if ($item->status == 'Scheduled'){
                        $statusButton = $this->datatablesService->buttonDropdownItem('cancelBtn', $item->id, 'danger', icon: 'block', btnText: 'Cancel Schedule');
                    } elseif ($item->status == 'Cancelled') {
                        $statusButton = $this->datatablesService->buttonDropdownItem('scheduled-btn', $item->id, 'warning', icon: 'check_circle', btnText: 'Set Match to Scheduled');
                    }
                    return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('match-schedules.show', $item->hash) . '"><span class="material-icons">visibility</span> View Schedule</a>
                            ' . $statusButton . '
                            '.$this->datatablesService->buttonDropdownItem('delete', $item->id, iconColor: 'danger', icon: 'delete', btnText: 'Delete Match').'
                          </div>
                        </div>';
                }
            })
            ->editColumn('homeTeam', function ($item) {
                return $this->datatablesService->name($item->homeTeam->logo, $item->homeTeam->teamName, $item->homeTeam->ageGroup, route('team-managements.show', $item->homeTeam->hash));
            })
            ->editColumn('awayTeam', function ($item) {
                if ($item->matchType == 'Internal Match') {
                    return $this->datatablesService->name($item->awayTeam->logo, $item->awayTeam->teamName, $item->awayTeam->ageGroup, route('team-managements.show', $item->awayTeam->hash));
                } else {
                    return $item->externalTeam->teamName;
                }
            })
            ->editColumn('score', function ($item) {
                $homeTeam = $this->homeTeamMatch($item);

                if ($item->matchType == 'Internal Match') {
                    $awayTeam = $this->awayTeamMatch($item);
                    return '<p class="mb-0"><strong class="js-lists-values-lead">' .$homeTeam->pivot->teamScore . ' - ' . $awayTeam->pivot->teamScore.'</strong></p>';
                } else {
                    return '<p class="mb-0"><strong class="js-lists-values-lead">' .$homeTeam->pivot->teamScore . ' - ' . $item->externalTeam->teamScore.'</strong></p>';
                }
            })
            ->editColumn('competition', function ($item) {
                if ($item->competition){
                    $competition = $this->datatablesService->name($item->competition->logo, $item->competition->name, $item->competition->type);
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
            ->rawColumns(['action','homeTeam', 'awayTeam', 'score','competition','date','status'])
            ->addIndexColumn()
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

    public function dataTablesPlayerStats(MatchModel $match, $teamId = null){
        $data = $this->eventScheduleRepository->getRelationData($match, 'playerMatchStats', teamId: $teamId, retrieveType: 'multiple');

        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($match) {
                $editPlayer = '';
                if ($match->status == 'Ongoing' || $match->status == 'Completed'){
                    $editPlayer = $this->datatablesService->buttonDropdownItem('edit-player-stats', $item->id, icon: 'edit', btnText: 'Edit Player Stats');
                }
                if (isAllAdmin() || isCoach()){
                    $showPlayer = '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            '.$editPlayer.'
                            <a class="dropdown-item" href="' . route('player-managements.show', ['player'=>$item->hash]) . '"><span class="material-icons">visibility</span> View Player</a>
                          </div>
                        </div>';
                } else {
                    $showPlayer = '';
                }
                return $showPlayer;
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('updated_at', function ($item) {
                return $this->convertToDatetime($item->updated_at);
            })
            ->rawColumns(['action','name','updated_at'])
            ->make();
    }

    public function dataTablesPlayerSkills(MatchModel $match){
        $data = $match->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($match){
                $stats = $item->playerSkillStats()->where('eventId', $match->id)->first();
                $review = $item->playerPerformanceReview()->where('eventId', $match->id)->first();

                if ( isAllAdmin() ){
                    $button = '<a class="dropdown-item" href="' . route('player-managements.skill-stats', ['player'=>$item->hash]) . '"><span class="material-icons">visibility</span> View Player Skill Stats</a>';
                }
                elseif( isCoach() ){
                    if (!$stats){
                        $statsBtn = '<a class="dropdown-item addSkills" id="'.$item->id.'" data-eventId="'.$match->id.'"><span class="material-icons">edit</span> Evaluate Player Skills Stats</a>';
                    } else {
                        $statsBtn = '<a class="dropdown-item editSkills" id="'.$item->id.'" data-eventId="'.$match->id.'" data-statsId="'.$stats->id.'"><span class="material-icons">edit</span> Edit Player Skills Stats</a>';
                    }

                    if (!$review){
                        $reviewBtn = '<a class="dropdown-item addPerformanceReview" id="'.$item->id.'" data-eventId="'.$match->id.'"><span class="material-icons">add</span> Add Player Performance Review</a>';
                    } else {
                        $reviewBtn = '<a class="dropdown-item editPerformanceReview" id="'.$item->id.'" data-eventId="'.$match->id.'"  data-reviewId="'.$review->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>';
                    }

                    $button = '<div class="dropdown">
                                      <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="' . route('player-managements.skill-stats', ['player'=>$item->hash]) . '"><span class="material-icons">visibility</span> View Player Skill Stats</a>
                                            '.$statsBtn.'
                                            '.$reviewBtn.'
                                      </div>
                                </div>';
                }
                return $button;
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name,route('player-managements.show', $item->hash));
            })
            ->editColumn('stats_status', function ($item) use ($match){
                $stats = $item->playerSkillStats()->where('eventId', $match->id)->first();
                if ($stats){
                    $date = 'Skill stats have been added';
                } else{
                    $date = 'Skill stats still not added yet';
                }
                return $date;
            })
            ->editColumn('stats_created', function ($item) use ($match){
                $stats = $item->playerSkillStats()->where('eventId', $match->id)->first();
                if ($stats){
                    $date = $this->convertToDatetime($stats->created_at);
                } else{
                    $date = '-';
                }
                return $date;
            })
            ->editColumn('stats_updated', function ($item) use ($match){
                $stats = $item->playerSkillStats()->where('eventId', $match->id)->first();
                if ($stats){
                    $date = $this->convertToDatetime($stats->updated_at);
                } else{
                    $date = '-';
                }
                return $date;
            })
            ->editColumn('performance_review', function ($item) use ($match){
                $review = $item->playerPerformanceReview()->where('eventId', $match->id)->first();
                if ($review){
                    $text = $review->performanceReview;
                } else{
                    $text = 'Performance review still not added yet';
                }
                return $text;
            })
            ->editColumn('performance_review_created', function ($item) use ($match){
                $review = $item->playerPerformanceReview()->where('eventId', $match->id)->first();
                if ($review){
                    $text = $this->convertToDatetime($review->created_at);
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->editColumn('performance_review_last_updated', function ($item) use ($match){
                $review = $item->playerPerformanceReview()->where('eventId', $match->id)->first();
                if ($review){
                    $text = $this->convertToDatetime($review->updated_at);
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->rawColumns(['action','name', 'stats_status', 'stats_created', 'stats_updated', 'performance_review', 'performance_review_created','performance_review_last_updated'])
            ->make();
    }

    public function show(MatchModel $match, Player $player = null){
        $allSkills = null;
        $playerPerformanceReviews = null;
        if ($player){
            $allSkills = $this->playerSkillStatsRepository->getByPlayer($player, $match)->first();
            $playerPerformanceReviews = $this->playerPerformanceReviewRepository->getByPlayer($player, $match);
        }

        return compact('allSkills', 'playerPerformanceReviews');
    }

    public function getTeamMatchStats(MatchModel $match, $teamSide = 'homeTeam')
    {
        if ($match->matchType == 'Internal Match') {
            if ($teamSide == 'homeTeam') {
                $team = $this->homeTeamMatch($match);
            } else {
                $team = $this->awayTeamMatch($match);
            }
        } else {
            if ($teamSide == 'homeTeam') {
                $team = $this->homeTeamMatch($match);
            } else {
                $team = $match->externalTeam;
            }
        }
        return $team;
    }

    public function homeTeamMatch(MatchModel $match)
    {
        return $this->eventScheduleRepository->getRelationData($match, 'teams', teamId: $match->homeTeamId);
    }
    public function awayTeamMatch(MatchModel $match)
    {
        return $this->eventScheduleRepository->getRelationData($match, 'teams', teamId: $match->awayTeamId);
    }
    public function homeTeamPlayers(MatchModel $match, $exceptPlayerId = null)
    {
        return $this->eventScheduleRepository->getRelationData($match, 'players', with: ['user', 'position'], teamId: $match->homeTeamId, exceptPlayerId: $exceptPlayerId, retrieveType: 'multiple');
    }
    public function awayTeamPlayers(MatchModel $schedule, $exceptPlayerId = null)
    {
        return $this->eventScheduleRepository->getRelationData($schedule, 'players', with: ['user', 'position'], teamId: $schedule->awayTeamId, exceptPlayerId: $exceptPlayerId, retrieveType: 'multiple');
    }
    public function homeTeamCoaches(MatchModel $schedule)
    {
        return $this->eventScheduleRepository->getRelationData($schedule, 'coaches', with: 'user', teamId: $schedule->homeTeamId, retrieveType: 'multiple');
    }
    public function awayTeamCoaches(MatchModel $schedule)
    {
        return $this->eventScheduleRepository->getRelationData($schedule, 'coaches', with: 'user', teamId: $schedule->awayTeamId, retrieveType: 'multiple');
    }
    public function homeTeamMatchScorers(MatchModel $schedule)
    {
//        $data = [];
//        if($schedule->matchScores()->first()) {
//            if ($schedule->matchScores()->first()->teamId != null) {
//                $data = $schedule->matchScores()->where('teamId', '=',$team->id)->get();
//            } else {
//                $data = $schedule->matchScores;
//            }
//        }
        return $this->eventScheduleRepository->getRelationData($schedule, 'matchScores', teamId: $schedule->homeTeamId, retrieveType: 'multiple');
    }
    public function awayTeamMatchScorers(MatchModel $schedule)
    {
        return $this->eventScheduleRepository->getRelationData($schedule, 'matchScores', teamId: $schedule->awayTeamId, retrieveType: 'multiple');
    }
    public function homeTeamNotes(MatchModel $schedule)
    {
        return $this->eventScheduleRepository->getRelationData($schedule, 'notes', teamId: $schedule->homeTeamId, retrieveType: 'multiple');
    }
    public function awayTeamNotes(MatchModel $schedule)
    {
        return $this->eventScheduleRepository->getRelationData($schedule, 'notes', teamId: $schedule->awayTeamId, retrieveType: 'multiple');
    }

    public function getEventPLayers(MatchModel $schedule, $team, $exceptPlayerId)
    {
        $isHomeTeam = $team === 'homeTeam';
        return [
            'players' => $isHomeTeam
                ? $this->homeTeamPlayers($schedule, $exceptPlayerId)
                : $this->awayTeamPlayers($schedule, $exceptPlayerId),
            'team' => $isHomeTeam ? $schedule->homeTeam : $schedule->awayTeam,
        ];
    }

    public function getMatchDetail(MatchModel $schedule)
    {
        if ($schedule->matchType == 'External Match') {
            $opposingTeam = $schedule->externalTeam->teamName;
            return compact('schedule', 'opposingTeam');
        } else {
            return compact('schedule');
        }
    }

    public function eventAttendance(MatchModel $schedule, Team $team = null, $homeTeam = true) {
        if ($homeTeam) {
            $players = $this->homeTeamPlayers($schedule);
            $coaches = $this->homeTeamCoaches($schedule);
        } else {
            $players = $this->awayTeamPlayers($schedule);
            $coaches = $this->awayTeamCoaches($schedule);
        }
        $totalParticipant = count($players) + count($coaches);

        $playerAttended = $this->eventScheduleRepository->getRelationData($schedule, 'players', attendanceStatus: 'Attended', teamId: $team->id, retrieveType: 'count');
        $playerIllness = $this->eventScheduleRepository->getRelationData($schedule, 'players', attendanceStatus: 'Illness', teamId: $team->id, retrieveType: 'count');
        $playerInjured = $this->eventScheduleRepository->getRelationData($schedule, 'players', attendanceStatus: 'Injured', teamId: $team->id, retrieveType: 'count');
        $playerOther = $this->eventScheduleRepository->getRelationData($schedule, 'players', attendanceStatus: 'Other', teamId: $team->id, retrieveType: 'count');
        $playerDidntAttend = $playerIllness + $playerInjured + $playerOther;

        $coachAttended = $this->eventScheduleRepository->getRelationData($schedule, 'coaches', attendanceStatus: 'Attended', teamId: $team->id, retrieveType: 'count');
        $coachIllness = $this->eventScheduleRepository->getRelationData($schedule, 'coaches', attendanceStatus: 'Illness', teamId: $team->id, retrieveType: 'count');
        $coachInjured = $this->eventScheduleRepository->getRelationData($schedule, 'coaches', attendanceStatus: 'Injured', teamId: $team->id, retrieveType: 'count');
        $coachOther = $this->eventScheduleRepository->getRelationData($schedule, 'coaches', attendanceStatus: 'Other', teamId: $team->id, retrieveType: 'count');
        $coachDidntAttend = $coachIllness + $coachInjured + $coachOther;

        $totalAttend = $playerAttended + $coachAttended;
        $totalDidntAttend = $playerDidntAttend + $coachDidntAttend;
        $totalIllness = $playerIllness + $coachIllness;
        $totalInjured = $playerInjured + $coachInjured;
        $totalOthers = $playerOther + $coachOther;
        return compact('totalParticipant', 'totalAttend', 'totalDidntAttend', 'totalIllness', 'totalInjured', 'totalOthers');
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
            $teams = $coach->teams;
        } else {
            $teams = $this->teamRepository->getByTeamside('Academy Team');
        }
        return $teams;
    }

    public function internalMatchTeams($exceptTeamId = null)
    {
        return $this->teamRepository->getByTeamside('Academy Team', $exceptTeamId);
    }
    public function storeTraining(array $data, $userId){
        $data['userId'] = $userId;
        $data['eventType'] = 'Training';
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $schedule =  $this->eventScheduleRepository->create($data);

        $team = $this->teamRepository->find($data['teamId']);

        $teamsParticipant = $this->userRepository->allTeamsParticipant($team);

        Notification::send($teamsParticipant, new TrainingSchedule($schedule, $team, 'create'));

        $schedule->teams()->attach($data['teamId']);
        $schedule->players()->attach($team->players);
        $schedule->coaches()->attach($team->coaches);

        return $schedule;
    }
    public function storeMatch(array $data, $userId){
        $data['userId'] = $userId;
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $schedule =  $this->eventScheduleRepository->create($data);

        $schedule->teams()->attach($data['homeTeamId']);

        $team = $this->teamRepository->find($data['homeTeamId']);
        $teamParticipant = $this->userRepository->allTeamsParticipant($team);

        $schedule->players()->attach($team->players, ['teamId' => $team->id]);
        $schedule->playerMatchStats()->attach($team->players, ['teamId' => $team->id]);
        $schedule->coaches()->attach($team->coaches, ['teamId' => $team->id]);
        $schedule->coachMatchStats()->attach($team->coaches, ['teamId' => $team->id]);

        Notification::send($teamParticipant, new MatchSchedule($schedule, 'create'));

        if ($data['matchType'] == 'Internal Match'){
            $schedule->teams()->attach($data['awayTeamId']);

            $awayTeam = $this->teamRepository->find($data['awayTeamId']);
            $awayTeamsPlayersCoaches = $this->userRepository->allTeamsParticipant($awayTeam, admins: false);

            $schedule->players()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $schedule->playerMatchStats()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $schedule->coaches()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);
            $schedule->coachMatchStats()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);

            Notification::send($awayTeamsPlayersCoaches, new MatchSchedule($schedule, 'create'));
        } else {
            $schedule->externalTeam()->create([
                'teamName' => $data['externalTeamName'],
            ]);
        }
        return $schedule;
    }

    public function updateTraining(array $data, MatchModel $schedule){
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $schedule->update($data);

        if (array_key_exists('teamId', $data)){
            $team = $this->teamRepository->find($data['teamId']);

            $teamsParticipant = $this->userRepository->allTeamsParticipant($team);
            Notification::send($teamsParticipant, new TrainingSchedule($schedule, $team, 'update'));

            $schedule->teams()->sync($data['teamId']);
            $schedule->players()->sync($team->players);
            $schedule->coaches()->sync($team->coaches);
        }
        return $schedule;
    }
    public function updateMatch(array $data, MatchModel $schedule)
    {
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $schedule->update($data);

        $homeTeam = $this->teamRepository->find($data['homeTeamId']);
        $schedule->players()->syncWithPivotValues($homeTeam->players, ['teamId' => $homeTeam->id]);
        $schedule->playerMatchStats()->syncWithPivotValues($homeTeam->players, ['teamId' => $homeTeam->id]);
        $schedule->coaches()->syncWithPivotValues($homeTeam->coaches, ['teamId' => $homeTeam->id]);
        $schedule->coachMatchStats()->syncWithPivotValues($homeTeam->coaches, ['teamId' => $homeTeam->id]);

        $homeTeamParticipant = $this->userRepository->allTeamsParticipant($homeTeam);
        Notification::send($homeTeamParticipant, new MatchSchedule($schedule, 'update'));

        if ($schedule->matchType == 'Internal Match') {
            $schedule->teams()->sync([
                $data['homeTeamId'],
                $data['awayTeamId']
            ]);

            $awayTeam = $this->teamRepository->find($data['awayTeamId']);

            $schedule->players()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $schedule->playerMatchStats()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $schedule->coaches()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);
            $schedule->coachMatchStats()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);

            $awayTeamParticipant = $this->userRepository->allTeamsParticipant($awayTeam, admins: false);
            Notification::send($awayTeamParticipant, new MatchSchedule($schedule, 'update'));
        } else {
            $schedule->teams()->sync([
                $data['homeTeamId'],
            ]);
            $schedule->externalTeam()->update([
                'teamName' => $data['externalTeamName'],
            ]);
        }
        return $schedule;
    }

    public function setStatus(MatchModel $schedule, $status): MatchModel
    {
        $this->eventScheduleRepository->updateStatus($schedule, $status);

        if ($schedule->eventType == 'Training') {
            $team = $schedule->teams()->first();
            $teamParticipants = $this->userRepository->allTeamsParticipant($team);
            Notification::send($teamParticipants, new TrainingSchedule($schedule, $team, $status));
        }
        elseif ($schedule->eventType == 'Match' && $schedule->isOpponentTeamMatch == '0') {

            $homeTeam = $schedule->homeTeam;
            $homeTeamParticipants = $this->userRepository->allTeamsParticipant($homeTeam);
            Notification::send($homeTeamParticipants, new  MatchSchedule($schedule, $status));

            if ($schedule->matchType == 'Internal Match') {
                $awayTeam = $schedule->awayTeam;
                $awayTeamParticipants = $this->userRepository->allTeamsParticipant($awayTeam, admins: false);
                Notification::send($awayTeamParticipants, new  MatchSchedule($schedule, $status));
            }
        }
        return $schedule;
    }


    public function endMatch(MatchModel $schedule)
    {
        $homeTeam = $this->homeTeamMatch($schedule);
        $homeTeamScore = $homeTeam->pivot->teamScore;

        if ($schedule->matchType == 'External Match') {
            $externalTeamScore = $schedule->externalTeam->teamScore;

            if ($externalTeamScore == 0) {
                $homeTeamCleanSheet = $homeTeam->pivot->cleanSheets + 1;
                $schedule->teams()->updateExistingPivot($homeTeam->id, ['cleanSheets' => $homeTeamCleanSheet]);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeam->id, ['cleanSheets' => $homeTeamCleanSheet]);
            }

            $win = 0;
            $lose = 0;
            $draw = 0;

            if ($homeTeamScore > $externalTeamScore) {
                $schedule->externalTeam()->update(['resultStatus' => 'Lose']);
                $schedule->update(['winnerTeamId' => $homeTeam->id]);
                $schedule->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Win']);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Win']);
                $win = 1;
            } elseif ($homeTeamScore < $externalTeamScore) {
                $schedule->externalTeam()->update(['resultStatus' => 'Win']);
                $schedule->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Lose']);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Lose']);
                $lose = 1;
            } elseif ($homeTeamScore == $externalTeamScore) {
                $schedule->externalTeam()->update(['resultStatus' => 'Draw']);
                $schedule->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Draw']);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Draw']);
                $draw = 1;
            }

            if ($schedule->competition->type == 'league') {
                if (count($this->leagueStandingRepository->getAll($schedule->competition, $homeTeam)) == 0) { //check if team is not added in league standing, then add team into league standing
                    $data['teams'] = $homeTeam->id;
                    $this->leagueStandingRepository->create($data, $schedule->competition);
                }
                $teamLeagueStanding = $schedule->competition->standings()->where('teamId', $homeTeam->id)->first();
                $goalsFor = $teamLeagueStanding->goalsFor + $homeTeam->pivot->goalScored;
                $goalsAgainst = $teamLeagueStanding->goalsAgainst + $homeTeam->pivot->goalConceded;
                $goalsDifference = $goalsFor - $goalsAgainst;
                $teamLeagueStanding->update([
                    'matchPlayed' => $teamLeagueStanding->matchPlayed + 1,
                    'won' => $teamLeagueStanding->won + $win,
                    'drawn' => $teamLeagueStanding->drawn + $draw,
                    'lost' => $teamLeagueStanding->lost + $lose,
                    'goalsFor' => $goalsFor,
                    'goalsAgainst' => $goalsAgainst,
                    'goalsDifference' => $goalsDifference,
                ]);
            }
        }
        else {
            $awayTeam = $this->awayTeamMatch($schedule);
            $awayTeamScore = $awayTeam->pivot->teamScore;

            if ($awayTeamScore == 0) {
                $homeTeamCleanSheet = $homeTeam->pivot->cleanSheets + 1;
                $schedule->teams()->updateExistingPivot($homeTeam->id, ['cleanSheets' => $homeTeamCleanSheet]);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeam->id, ['cleanSheets' => $homeTeamCleanSheet]);
            }
            if ($homeTeamScore == 0) {
                $awayTeamCleanSheet = $awayTeam->pivot->cleanSheets + 1;
                $schedule->teams()->updateExistingPivot($awayTeam->id, ['cleanSheets' => $awayTeamCleanSheet]);
                $schedule->coachMatchStats()->updateExistingPivot($awayTeam->id, ['cleanSheets' => $awayTeamCleanSheet]);
            }

            $homeTeamWon = 0;
            $homeTeamDraw = 0;
            $homeTeamLost = 0;
            $awayTeamWon = 0;
            $awayTeamDraw = 0;
            $awayTeamLost = 0;

            if ($homeTeamScore > $awayTeamScore) {
                $schedule->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Win']);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Win']);
                $schedule->update(['winnerTeamId' => $homeTeam->id]);
                $homeTeamWon = 1;
                $schedule->teams()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Lose']);
                $schedule->coachMatchStats()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Lose']);
                $awayTeamLost = 1;
            } elseif ($homeTeamScore < $awayTeamScore) {
                $schedule->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Lose']);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Lose']);
                $homeTeamLost = 1;
                $schedule->teams()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Win']);
                $schedule->coachMatchStats()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Win']);
                $schedule->update(['winnerTeamId' => $awayTeam->id]);
                $awayTeamWon = 1;
            } elseif ($homeTeamScore == $awayTeamScore) {
                $schedule->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Draw']);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Draw']);
                $homeTeamDraw = 1;
                $schedule->teams()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Draw']);
                $schedule->coachMatchStats()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Draw']);
                $awayTeamDraw = 1;
            }

            if ($schedule->competition->type == 'league') {
                if (count($this->leagueStandingRepository->getAll($schedule->competition, $homeTeam)) == 0) { //check if home team is not added in league standing, then add team into league standing
                    $homeData['teams'] = $homeTeam->id;
                    $this->leagueStandingRepository->create($homeData, $schedule->competition);
                }
                if (count($this->leagueStandingRepository->getAll($schedule->competition, $awayTeam)) == 0) { //check if away team is not added in league standing, then add team into league standing
                    $awayData['teams'] = $awayTeam->id;
                    $this->leagueStandingRepository->create($awayData, $schedule->competition);
                }
                $homeTeamLeagueStanding = $schedule->competition->standings()->where('teamId', $homeTeam->id)->first();
                $awayTeamLeagueStanding = $schedule->competition->standings()->where('teamId', $awayTeam->id)->first();

                $homeTeamGoalsFor = $homeTeamLeagueStanding->goalsFor + $homeTeam->pivot->goalScored;
                $homeTeamGoalsAgainst = $homeTeamLeagueStanding->goalsAgainst + $homeTeam->pivot->goalConceded;
                $homeTeamGoalsDifference = $homeTeamGoalsFor - $homeTeamGoalsAgainst;
                $homeTeamLeagueStanding->update([
                    'matchPlayed' => $homeTeamLeagueStanding->matchPlayed + 1,
                    'won' => $homeTeamLeagueStanding->won + $homeTeamWon,
                    'drawn' => $homeTeamLeagueStanding->drawn + $homeTeamDraw,
                    'lost' => $homeTeamLeagueStanding->lost + $homeTeamLost,
                    'goalsFor' => $homeTeamGoalsFor,
                    'goalsAgainst' => $homeTeamGoalsAgainst,
                    'goalsDifference' => $homeTeamGoalsDifference,
                ]);

                $awayTeamGoalsFor = $awayTeamLeagueStanding->goalsFor + $awayTeam->pivot->goalScored;
                $awayTeamGoalsAgainst = $awayTeamLeagueStanding->goalsAgainst + $awayTeam->pivot->goalConceded;
                $awayTeamGoalsDifference = $awayTeamGoalsFor - $awayTeamGoalsAgainst;
                $awayTeamLeagueStanding->update([
                    'matchPlayed' => $awayTeamLeagueStanding->matchPlayed + 1,
                    'won' => $awayTeamLeagueStanding->won + $awayTeamWon,
                    'drawn' => $awayTeamLeagueStanding->drawn + $awayTeamDraw,
                    'lost' => $awayTeamLeagueStanding->lost + $awayTeamLost,
                    'goalsFor' => $awayTeamGoalsFor,
                    'goalsAgainst' => $awayTeamGoalsAgainst,
                    'goalsDifference' => $awayTeamGoalsDifference,
                ]);
            }
        }
        return $this->setStatus($schedule, 'completed');
    }


    public function getPlayerAttendance(MatchModel $schedule, Player $player)
    {
        return $schedule->players()->find($player->id);
    }
    public function getCoachAttendance(MatchModel $schedule, Coach $coach)
    {
        return $schedule->coaches()->find($coach->id);
    }

    public function updatePlayerAttendanceStatus($data, MatchModel $schedule, Player $player){
        $schedule->players()->updateExistingPivot($player->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
        if ($schedule->eventType == 'Training') {
            $player->user->notify(new TrainingScheduleAttendance($schedule, $data['attendanceStatus']));
        } elseif ($schedule->eventType == 'Match') {
            $player->user->notify(new MatchScheduleAttendance($schedule, $data['attendanceStatus']));
        }
        return $schedule;
    }
    public function updateCoachAttendanceStatus($data, MatchModel $schedule, Coach $coach){
        $schedule->coaches()->updateExistingPivot($coach->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
        if ($schedule->eventType == 'Training') {
            $coach->user->notify(new TrainingScheduleAttendance($schedule, $data['attendanceStatus']));
        } elseif ($schedule->eventType == 'Match') {
            $coach->user->notify(new MatchScheduleAttendance($schedule, $data['attendanceStatus']));
        }
        return $schedule;
    }

    public function createNote($data, MatchModel $schedule, $loggedUser){
        $note = $this->eventScheduleRepository->createRelation($schedule, $data, 'notes');

        $teamParticipants = $this->userRepository->allTeamsParticipant($schedule->teams[0]);
        if ($schedule->eventType == 'Training') {
            Notification::send($teamParticipants, new TrainingNote($loggedUser, $schedule, 'created'));
        } elseif ($schedule->eventType == 'Match') {
            Notification::send($teamParticipants, new MatchNoteNotification($loggedUser, $schedule, 'created'));
        }
        return $note;
    }
    public function updateNote($data, MatchModel $schedule, MatchNote $note, $loggedUser){
        $note->update($data);
        $teamParticipants = $this->userRepository->allTeamsParticipant($schedule->teams[0]);
        if ($schedule->eventType == 'Training') {
            Notification::send($teamParticipants, new TrainingNote($loggedUser, $schedule, 'updated'));
        } elseif ($schedule->eventType == 'Match') {
            Notification::send($teamParticipants, new MatchNoteNotification($loggedUser, $schedule, 'updated'));
        }
        return $note;
    }
    public function destroyNote(MatchModel $schedule, MatchNote $note, $loggedUser)
    {
        $note->delete();
        $teamParticipants = $this->userRepository->allTeamsParticipant($schedule->teams[0]);
        if ($schedule->eventType == 'Training') {
            Notification::send($teamParticipants, new TrainingNote($loggedUser, $schedule, 'deleted'));
        } elseif ($schedule->eventType == 'Match') {
            Notification::send($teamParticipants, new MatchNoteNotification($loggedUser, $schedule, 'deleted'));
        }
        return $note;
    }

    public function storeMatchScorer($data, MatchModel $schedule, $awayTeam = false)
    {
        $data['isOwnGoal'] = '0';
        $scorer = $this->eventScheduleRepository->createRelation($schedule, $data, 'matchScores');

        $player = $this->eventScheduleRepository->getRelationData($schedule, 'playerMatchStats', teamId: $data['teamId'], playerId: $data['playerId']);
        $assistPlayer = $this->eventScheduleRepository->getRelationData($schedule, 'playerMatchStats', teamId: $data['teamId'], playerId: $data['assistPlayerId']);

        $playerGoal = $player->pivot->goals + 1;
        $playerAssist = $assistPlayer->pivot->assists + 1;

        $homeTeamData = $this->homeTeamMatch($schedule);

        if ($schedule->matchType == 'External Match') {
            $teamGoalScored = $homeTeamData->pivot->goalScored + 1;
            $teamScore = $teamGoalScored + $schedule->externalTeam->teamOwnGoal;

            $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $teamScore, 'goalScored' => $teamGoalScored]);
            $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' => $teamScore, 'goalScored' => $teamGoalScored]);
            $schedule->externalTeam()->update(['goalConceded' => $schedule->externalTeam->goalConceded + 1]);
        } else {
            $awayTeamData = $this->awayTeamMatch($schedule);

            if ($awayTeam) {
                $wayTeamGoalScored = $awayTeamData->pivot->goalScored + 1;
                $homeTeamGoalConceded = $homeTeamData->pivot->goalConceded + 1;
                $awayTeamScore = $wayTeamGoalScored + $homeTeamData->pivot->teamOwnGoal;

                $schedule->teams()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore, 'goalScored' => $wayTeamGoalScored]);
                $schedule->teams()->updateExistingPivot($homeTeamData->id, ['goalConceded' => $homeTeamGoalConceded]);

                $schedule->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore, 'goalScored' => $wayTeamGoalScored]);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['goalConceded' => $homeTeamGoalConceded]);
            } else {
                $homeTeamGoalScored = $homeTeamData->pivot->goalScored + 1;
                $awayTeamGoalConceded = $awayTeamData->pivot->goalConceded + 1;
                $homeTeamScore = $homeTeamGoalScored + $awayTeamData->pivot->teamOwnGoal;

                $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore, 'goalScored' => $homeTeamGoalScored]);
                $schedule->teams()->updateExistingPivot($awayTeamData->id, ['goalConceded' => $awayTeamGoalConceded]);

                $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore, 'goalScored' => $homeTeamGoalScored]);
                $schedule->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['goalConceded' => $awayTeamGoalConceded]);
            }
        }

        $schedule->playerMatchStats()->updateExistingPivot($data['playerId'], ['goals' => $playerGoal]);
        $schedule->playerMatchStats()->updateExistingPivot($data['assistPlayerId'], ['assists' => $playerAssist]);

        return $scorer;
    }
    public function destroyMatchScorer(MatchModel $schedule, MatchScore $scorer, $awayTeam = false)
    {
        $player = $this->eventScheduleRepository->getRelationData($schedule, 'playerMatchStats', teamId: $scorer->teamId, playerId: $scorer->playerId);
        $assistPlayer = $this->eventScheduleRepository->getRelationData($schedule, 'playerMatchStats', teamId: $scorer->teamId, playerId: $scorer->assistPlayerId);

        $playerGoal = $player->pivot->goals - 1;
        $playerAssist = $assistPlayer->pivot->assists - 1;

        $homeTeamData = $this->homeTeamMatch($schedule);

        if ($schedule->matchType == 'External Match') {
            $teamGoalScored = $homeTeamData->pivot->goalScored - 1;
            $teamScore = $teamGoalScored + $schedule->externalTeam->teamOwnGoal;

            $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $teamScore, 'goalScored' => $teamGoalScored]);
            $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' => $teamScore, 'goalScored' => $teamGoalScored]);
            $schedule->externalTeam()->update(['teamScore' => $schedule->externalTeam->goalConceded + 1]);
        } else {
            $awayTeamData = $this->awayTeamMatch($schedule);

            if ($awayTeam) {
                $awayTeamGoalScored = $awayTeamData->pivot->goalScored - 1;
                $homeTeamGoalConceded = $homeTeamData->pivot->goalConceded - 1;
                $awayTeamScore = $awayTeamGoalScored + $homeTeamData->pivot->teamOwnGoal;

                $schedule->teams()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore, 'goalScored' => $awayTeamGoalScored]);
                $schedule->teams()->updateExistingPivot($homeTeamData->id, ['goalConceded' => $homeTeamGoalConceded]);

                $schedule->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore, 'goalScored' => $awayTeamGoalScored]);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['goalConceded' => $homeTeamGoalConceded]);
            } else {
                $homeTeamGoalScored = $homeTeamData->pivot->goalScored - 1;
                $awayTeamGoalConceded = $awayTeamData->pivot->goalConceded - 1;
                $homeTeamScore = $homeTeamGoalScored + $awayTeamData->pivot->teamOwnGoal;

                $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore, 'goalScored' => $homeTeamGoalScored]);
                $schedule->teams()->updateExistingPivot($awayTeamData->id, ['goalConceded' => $awayTeamGoalConceded]);

                $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore, 'goalScored' => $homeTeamGoalScored]);
                $schedule->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['goalConceded' => $awayTeamGoalConceded]);
            }
        }
        $schedule->playerMatchStats()->updateExistingPivot($scorer->playerId, ['goals' => $playerGoal]);
        $schedule->playerMatchStats()->updateExistingPivot($scorer->assistPlayerId, ['assists' => $playerAssist]);
        return $scorer->delete();
    }

    public function storeOwnGoal($data, MatchModel $schedule, $awayTeam = false)
    {
        $data['isOwnGoal'] = '1';
        $scorer = $this->eventScheduleRepository->createRelation($schedule, $data, 'matchScores');
        $player = $this->eventScheduleRepository->getRelationData($schedule, 'playerMatchStats', teamId: $data['teamId'], playerId: $data['playerId']);

        $playerOwnGoal = $player->pivot->ownGoal + 1;

        $homeTeamData = $this->homeTeamMatch($schedule);

        if ($schedule->matchType == 'External Match') {
            $teamOwnGoal = $homeTeamData->pivot->ownGoal + 1;
            $teamGoalConceded = $homeTeamData->pivot->goalConceded + 1;
            $externalTeamScore = $teamOwnGoal + $schedule->externalTeam->goalScored;

            $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $teamOwnGoal, 'goalConceded' => $teamGoalConceded]);
            $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $teamOwnGoal, 'goalConceded' => $teamGoalConceded]);
            $schedule->externalTeam()->update(['teamScore' => $externalTeamScore]);
        } else {
            $awayTeamData = $this->awayTeamMatch($schedule);

            if ($awayTeam) {
                $awayTeamOwnGoal = $awayTeamData->pivot->ownGoal + 1;
                $awayTeamGoalConceded = $awayTeamData->pivot->goalConceded + 1;
                $homeTeamScore = $awayTeamOwnGoal + $homeTeamData->pivot->goalScored;

                $schedule->teams()->updateExistingPivot($awayTeamData->id, ['teamOwnGoal' => $awayTeamOwnGoal, 'goalConceded' => $awayTeamGoalConceded]);
                $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore]);

                $schedule->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamOwnGoal' =>$awayTeamOwnGoal, 'goalConceded' => $awayTeamGoalConceded]);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' =>$homeTeamScore]);
            } else {
                $homeTeamOwnGoal = $homeTeamData->pivot->ownGoal + 1;
                $homeTeamGoalConceded = $homeTeamData->pivot->goalConceded + 1;
                $awayTeamScore = $homeTeamOwnGoal + $awayTeamData->pivot->goalScored;

                $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $homeTeamOwnGoal, 'goalConceded' => $homeTeamGoalConceded]);
                $schedule->teams()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore]);

                $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $homeTeamOwnGoal, 'goalConceded' => $homeTeamGoalConceded]);
                $schedule->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore]);
            }
        }

        $schedule->playerMatchStats()->updateExistingPivot($data['playerId'], ['ownGoal' => $playerOwnGoal]);
        return $scorer;
    }

    public function destroyOwnGoal(MatchModel $schedule, MatchScore $scorer, $awayTeam = false)
    {
        $player = $this->eventScheduleRepository->getRelationData($schedule, 'playerMatchStats', teamId: $scorer->teamId, playerId: $scorer->playerId);
        $playerOwnGoal = $player->pivot->ownGoal - 1;

        $homeTeamData = $this->homeTeamMatch($schedule);

        if ($schedule->matchType == 'External Match') {
            $teamOwnGoal = $homeTeamData->pivot->teamOwnGoal - 1;
            $teamGoalConceded = $homeTeamData->pivot->goalConceded - 1;
            $externalTeamScore = $teamOwnGoal + $schedule->externalTeam->goalScored;

            $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $teamOwnGoal, 'goalConceded' => $teamGoalConceded]);
            $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $teamOwnGoal, 'goalConceded' => $teamGoalConceded]);
            $schedule->externalTeam()->update(['teamScore' => $externalTeamScore]);
        } else {
            $awayTeamData = $this->awayTeamMatch($schedule);

            if ($awayTeam) {
                $awayTeamOwnGoal = $awayTeamData->pivot->teamOwnGoal - 1;
                $awayTeamGoalConceded = $awayTeamData->pivot->goalConceded - 1;
                $homeTeamScore = $awayTeamOwnGoal + $homeTeamData->pivot->goalScored;

                $schedule->teams()->updateExistingPivot($awayTeamData->id, ['teamOwnGoal' => $awayTeamOwnGoal, 'goalConceded' => $awayTeamGoalConceded]);
                $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore]);

                $schedule->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamOwnGoal' => $awayTeamOwnGoal, 'goalConceded' => $awayTeamGoalConceded]);
                $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' =>$homeTeamScore]);
            } else {
                $homeTeamOwnGoal = $homeTeamData->pivot->teamOwnGoal - 1;
                $homeTeamGoalConceded = $homeTeamData->pivot->goalConceded - 1;
                $awayTeamScore = $homeTeamOwnGoal + $awayTeamData->pivot->goalScored;

                $schedule->teams()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $homeTeamOwnGoal, 'goalConceded' => $homeTeamGoalConceded]);
                $schedule->teams()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore]);

                $schedule->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $homeTeamOwnGoal, 'goalConceded' => $homeTeamGoalConceded]);
                $schedule->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore]);
            }
        }

        $schedule->playerMatchStats()->updateExistingPivot($scorer->playerId, ['ownGoal' => $playerOwnGoal]);
        return $scorer->delete();
    }

    public function updateMatchStats(array $data, MatchModel $schedule)
    {
        if ($schedule->matchType === 'Internal Match' || $data['teamSide'] === 'homeTeam') {
            $this->eventScheduleRepository->updateTeamMatchStats($schedule, $data);
        } else {
            $this->eventScheduleRepository->updateExternalTeamMatchStats($schedule, $data);
        }
        return $schedule;
    }

    public function updateExternalTeamScore(array $data, MatchModel $schedule)
    {
        $homeTeam = $this->homeTeamMatch($schedule);

        $data['goalConceded'] = $schedule->externalTeam->goalConceded + $data['teamOwnGoal'];
        $data['teamScore'] = $data['goalScored'] + $homeTeam->pivot->teamOwnGoal;
        $homeTeamScore = $homeTeam->pivot->goalScored + $data['teamOwnGoal'];

        $schedule->teams()->updateExistingPivot($homeTeam->id, ['teamScore' => $homeTeamScore, 'goalConceded' => $data['goalScored']]);
        $schedule->coachMatchStats()->updateExistingPivot($homeTeam->id, ['teamScore' => $homeTeamScore, 'goalConceded' => $data['goalScored']]);
        $this->eventScheduleRepository->updateExternalTeamMatchStats($schedule, $data);
        return $schedule;
    }

    public function getPlayerStats(MatchModel $schedule, Player $player)
    {
        $data = $schedule->playerMatchStats()->find($player->id);
        $playerData = $data->user;
        $statsData = $data->pivot;
        return compact('playerData', 'statsData');
    }
    public function updatePlayerStats(array $data, MatchModel $schedule, Player $player)
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

        $schedule->playerMatchStats()->updateExistingPivot($player->id, $data);
        $player->user->notify(new MatchStatsPlayer($schedule));
        return $schedule;
    }

    public function destroy(MatchModel $schedule)
    {
        try {
            if ($schedule->eventType == 'Training') {
                $team = $schedule->teams()->first();
                $teamParticipants = $this->userRepository->allTeamsParticipant($team);
                Notification::send($teamParticipants, new TrainingSchedule($schedule, $team, 'delete'));
            }
            elseif ($schedule->eventType == 'Match' && $schedule->isOpponentTeamMatch == '0') {

                $homeTeam = $schedule->homeTeam;
                $homeTeamParticipants = $this->userRepository->allTeamsParticipant($homeTeam);
                Notification::send($homeTeamParticipants, new  MatchSchedule($schedule, 'delete'));

                if ($schedule->matchType == 'Internal Match') {
                    $awayTeam = $schedule->awayTeam;
                    $awayTeamParticipants = $this->userRepository->allTeamsParticipant($awayTeam, admins: false);
                    Notification::send($awayTeamParticipants, new  MatchSchedule($schedule, 'delete'));
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        return $schedule->delete();
    }
}
