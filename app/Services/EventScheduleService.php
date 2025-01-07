<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\MatchScore;
use App\Models\Player;
use App\Models\ScheduleNote;
use App\Notifications\MatchSchedules\MatchNote;
use App\Notifications\MatchSchedules\MatchScheduleAttendance;
use App\Notifications\MatchSchedules\MatchScheduleCreatedForAdmin;
use App\Notifications\MatchSchedules\MatchScheduleCreatedForPlayerCoach;
use App\Notifications\MatchSchedules\MatchScheduleDeletedForAdmin;
use App\Notifications\MatchSchedules\MatchScheduleDeletedForPlayersCoaches;
use App\Notifications\MatchSchedules\MatchScheduleUpdatedForAdmin;
use App\Notifications\MatchSchedules\MatchScheduleUpdatedForPlayerCoach;
use App\Notifications\MatchSchedules\MatchStatsPlayer;
use App\Notifications\TrainingSchedules\TrainingNote;
use App\Notifications\TrainingSchedules\TrainingScheduleAttendance;
use App\Notifications\TrainingSchedules\TrainingScheduleCreatedForCoachAdmin;
use App\Notifications\TrainingSchedules\TrainingScheduleCreatedForPlayer;
use App\Notifications\TrainingSchedules\TrainingScheduleDeletedForCoachAdmin;
use App\Notifications\TrainingSchedules\TrainingScheduleDeletedForPlayers;
use App\Notifications\TrainingSchedules\TrainingScheduleUpdatedForCoachAdmin;
use App\Notifications\TrainingSchedules\TrainingScheduleUpdatedForPlayer;
use App\Repository\EventScheduleRepository;
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
    private DatatablesHelper $datatablesService;
    public function __construct(
        EventScheduleRepository           $eventScheduleRepository,
        TeamRepository                    $teamRepository,
        UserRepository                    $userRepository,
        PlayerSkillStatsRepository        $playerSkillStatsRepository,
        PlayerPerformanceReviewRepository $playerPerformanceReviewRepository,
        DatatablesHelper                  $datatablesService)
    {
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->playerSkillStatsRepository = $playerSkillStatsRepository;
        $this->playerPerformanceReviewRepository = $playerPerformanceReviewRepository;
        $this->datatablesService = $datatablesService;
    }

    public function indexMatch(): Collection
    {
        return $this->eventScheduleRepository->getEvent('Scheduled', 'Match');
    }
    public function indexTraining(): Collection
    {
        return $this->eventScheduleRepository->getEvent( 'Scheduled', 'Training');
    }

    public function coachTeamsIndexTraining(Coach $coach): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($coach, 'Training', 'Scheduled');
    }
    public function coachTeamsIndexMatch(Coach $coach): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($coach, 'Match', 'Scheduled');
    }

    public function playerTeamsIndexTraining(Player $player): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($player, 'Training', 'Scheduled');
    }
    public function playerTeamsIndexMatch(Player $player): Collection
    {
        return $this->eventScheduleRepository->getEventByModel($player,  'Match', 'Scheduled');
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
                    $statusButton = '';
                    if ($item->status != 'Cancelled' && $item->status != 'Completed') {
                        $statusButton = '<button type="submit" class="dropdown-item cancelTrainingBtn" id="'.$item->id.'">
                                            <span class="material-icons text-danger">block</span> Cancel Training
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
                            <a class="dropdown-item" href="' . route('training-schedules.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Schedule</a>
                            <a class="dropdown-item" href="' . route('training-schedules.show', $item->hash) . '"><span class="material-icons">visibility</span> View Schedule</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons text-danger">delete</span> Delete Schedule
                            </button>
                          </div>
                        </div>';
                } elseif (isPlayer()){
                    return $this->datatablesService->buttonTooltips(route('training-schedules.show', $item->hash), 'View training detail', 'visibility');
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
                    if ($item->status != 'Cancelled' && $item->status != 'Completed') {
                        $statusButton = '<button type="submit" class="dropdown-item cancelMatchBtn" id="'.$item->id.'">
                                            <span class="material-icons text-danger">block</span> Cancel Schedule
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
                            <a class="dropdown-item" href="' . route('match-schedules.edit', $item->hash) . '"><span class="material-icons">edit</span> Edit Schedule</a>
                            <a class="dropdown-item" href="' . route('match-schedules.show', $item->hash) . '"><span class="material-icons">visibility</span> View Schedule</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Schedule
                            </button>
                          </div>
                        </div>';
                }
            })
            ->editColumn('team', function ($item) {
                return $this->datatablesService->name($item->teams[0]->logo, $item->teams[0]->teamName, $item->teams[0]->ageGroup, route('team-managements.show', $item->teams[0]->hash));
            })
            ->editColumn('opponentTeam', function ($item) {
                return $this->datatablesService->name($item->teams[1]->logo, $item->teams[1]->teamName, $item->teams[1]->ageGroup, route('team-managements.show', $item->teams[1]->hash));
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

    public function dataTablesPlayerStats(EventSchedule $schedule, $teamId = null){
        if ($teamId) {
            $data = $schedule->playerMatchStats()->where('teamId', $teamId)->get();
        }else {
            $data = $schedule->playerMatchStats;
        }

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if (isAllAdmin() || isCoach()){
                    $showPlayer = '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit-player-stats" href="" id="'.$item->id.'"><span class="material-icons">edit</span> Edit Player Stats</a>
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

    public function dataTablesPlayerSkills(EventSchedule $schedule){
        $data = $schedule->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($schedule){
                $stats = $item->playerSkillStats()->where('eventId', $schedule->id)->first();
                $review = $item->playerPerformanceReview()->where('eventId', $schedule->id)->first();

                if ( isAllAdmin() ){
                    $button = '<a class="dropdown-item" href="' . route('player-managements.skill-stats', ['player'=>$item->hash]) . '"><span class="material-icons">visibility</span> View Player Skill Stats</a>';
                }
                elseif( isCoach() ){
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
                    $date = $this->convertToDatetime($stats->created_at);
                } else{
                    $date = '-';
                }
                return $date;
            })
            ->editColumn('stats_updated', function ($item) use ($schedule){
                $stats = $item->playerSkillStats()->where('eventId', $schedule->id)->first();
                if ($stats){
                    $date = $this->convertToDatetime($stats->updated_at);
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
                    $text = $this->convertToDatetime($review->created_at);
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->editColumn('performance_review_last_updated', function ($item) use ($schedule){
                $review = $item->playerPerformanceReview()->where('eventId', $schedule->id)->first();
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

    public function show(EventSchedule $schedule, Player $player = null){
        $allSkills = null;
        $playerPerformanceReviews = null;
        if ($player){
            $allSkills = $this->playerSkillStatsRepository->getByPlayer($player, $schedule)->first();
            $playerPerformanceReviews = $this->playerPerformanceReviewRepository->getByPlayer($player, $schedule);

        }

        return compact('allSkills', 'playerPerformanceReviews');
    }
    public function eventAttendance(EventSchedule $schedule, $team = null) {

        if ($schedule->players[0]->pivot->teamId != null) {
            $totalParticipant = $schedule->players()->where('teamId', $team->id)->count() + $schedule->coaches()->where('teamId', $team->id)->count();
        } else {
            $team = null;
            $totalParticipant = $schedule->players()->count() + $schedule->coaches()->count();
        }


        $playerAttended = $this->eventScheduleRepository->playerAttendanceCount('Attended', $schedule->id, $team);
        $playerIllness = $this->eventScheduleRepository->playerAttendanceCount('Illness', $schedule->id, $team);
        $playerInjured = $this->eventScheduleRepository->playerAttendanceCount('Injured', $schedule->id, $team);
        $playerOther = $this->eventScheduleRepository->playerAttendanceCount('Other', $schedule->id, $team);
        $playerDidntAttend = $playerIllness + $playerInjured + $playerOther;

        $coachAttended = $this->eventScheduleRepository->coachesAttendanceCount('Attended', $schedule->id, $team);
        $coachIllness = $this->eventScheduleRepository->coachesAttendanceCount('Illness', $schedule->id, $team);
        $coachInjured = $this->eventScheduleRepository->coachesAttendanceCount('Injured', $schedule->id, $team);
        $coachOther = $this->eventScheduleRepository->coachesAttendanceCount('Other', $schedule->id, $team);
        $coachDidntAttend = $coachIllness + $coachInjured + $coachOther;

        $totalAttend = $playerAttended + $coachAttended;
        $totalDidntAttend = $playerDidntAttend + $coachDidntAttend;
        $totalIllness = $playerIllness + $coachIllness;
        $totalInjured = $playerInjured + $coachInjured;
        $totalOthers = $playerOther + $coachOther;
        return compact('totalParticipant', 'totalAttend', 'totalDidntAttend', 'totalIllness', 'totalInjured', 'totalOthers');
    }

    public function getmatchScorers(EventSchedule $schedule, $team)
    {
        $data = [];
        if($schedule->matchScores()->first()) {
            if ($schedule->matchScores()->first()->teamId != null) {
                $data = $schedule->matchScores()->where('teamId', '=',$team->id)->get();
            } else {
                $data = $schedule->matchScores;
            }
        }

        return $data;
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

        $loggedUser = $this->userRepository->find($userId);
        $creatorUserName = $this->getUserFullName($loggedUser);

        $teamsCoachesAdmins = $this->userRepository->allTeamsParticipant($team, players: false);
        $teamsPlayers = $this->userRepository->allTeamsParticipant($team, admins: false, coaches: false);

        Notification::send($teamsCoachesAdmins, new TrainingScheduleCreatedForCoachAdmin($schedule, $team, $creatorUserName));
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
        if ($data['matchType'] == 'Internal Match'){
            $schedule->teams()->attach($data['opponentTeamId']);
        } else {
            $schedule->externalMatch()->create([
                'teamName' => $data['externalTeamName'],
            ]);
        }

        $loggedUser = $this->userRepository->find($userId);
        $creatorUserName = $this->getUserFullName($loggedUser);

        if ($data['isOpponentTeamMatch'] == '0' and $data['matchType'] != 'Internal Match'){
            $team = $this->teamRepository->find($data['teamId']);

            $teamsPlayersCoaches = $this->userRepository->allTeamsParticipant($team, admins: false);

            Notification::send($this->userRepository->getAllAdminUsers(), new MatchScheduleCreatedForAdmin($schedule, $creatorUserName));
            Notification::send($teamsPlayersCoaches, new MatchScheduleCreatedForPlayerCoach($schedule));

            $schedule->players()->attach($team->players, ['teamId' => $team->id]);
            $schedule->playerMatchStats()->attach($team->players, ['teamId' => $team->id]);
            $schedule->coaches()->attach($team->coaches, ['teamId' => $team->id]);
            $schedule->coachMatchStats()->attach($team->coaches, ['teamId' => $team->id]);

        } elseif ($data['isOpponentTeamMatch'] == '0' and $data['matchType'] == 'Internal Match') {
            $homeTeam = $this->teamRepository->find($data['teamId']);
            $awayTeam = $this->teamRepository->find($data['opponentTeamId']);

            $awayTeamsPlayersCoaches = $this->userRepository->allTeamsParticipant($awayTeam, admins: false);
            $homeTeamsPlayersCoaches = $this->userRepository->allTeamsParticipant($homeTeam, admins: false);

            Notification::send($this->userRepository->getAllAdminUsers(), new MatchScheduleCreatedForAdmin($schedule, $creatorUserName));
            Notification::send($awayTeamsPlayersCoaches, new MatchScheduleCreatedForPlayerCoach($schedule));
            Notification::send($homeTeamsPlayersCoaches, new MatchScheduleCreatedForPlayerCoach($schedule));

            $schedule->players()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $schedule->players()->attach($homeTeam->players, ['teamId' => $homeTeam->id]);
            $schedule->playerMatchStats()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $schedule->playerMatchStats()->attach($homeTeam->players, ['teamId' => $homeTeam->id]);

            $schedule->coaches()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);
            $schedule->coaches()->attach($homeTeam->coaches, ['teamId' => $homeTeam->id]);
            $schedule->coachMatchStats()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);
            $schedule->coachMatchStats()->attach($homeTeam->coaches, ['teamId' => $homeTeam->id]);
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
            'Ongoing' => 'is now ongoing. Please check the schedule for details and arrive on time!',
            'Completed' => 'have been completed',
            'Cancelled' => 'have been cancelled',
            'Scheduled' => 'have been set to scheduled. Please check the schedule for the updated details!',
        ];

        $team = $schedule->teams()->first();
        $teamParticipants = $this->userRepository->allTeamsParticipant($team);

        // Check if the status exists in the defined mapping
        if (array_key_exists($status, $statusMessages)) {
            $statusMessage = $statusMessages[$status];

            if ($schedule->eventType == 'Training') {
                Notification::send($teamParticipants, new TrainingScheduleUpdatedForPlayer($schedule, $team, $statusMessage));
            } elseif ($schedule->eventType == 'Match' && $schedule->isOpponentTeamMatch == '0') {
                Notification::send($teamParticipants, new MatchScheduleUpdatedForPlayerCoach($schedule, $statusMessage));
            }

        }

        return $schedule;
    }


    public function endMatch(EventSchedule $schedule)
    {

        $academyTeamScore = $schedule->teams[0]->pivot->teamScore;
        $opponentTeamScore = $schedule->teams[1]->pivot->teamScore;

        // update teams goal conceded data
        $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['goalConceded'=> $opponentTeamScore]);
        $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['goalConceded'=> $academyTeamScore]);

        // calculate teams goal difference data
        $academyTeamGoalsDifference = $academyTeamScore - $opponentTeamScore;
        $opponentTeamGoalsDifference = $opponentTeamScore - $academyTeamScore;

        // update match result data of each coach match stats if match is not opponent team match
        if ($schedule->isOpponentTeamMatch == '0') {
            foreach ($schedule->coaches as $coach){
                $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['goalConceded' => $opponentTeamScore]);
            }
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
            if ($schedule->isOpponentTeamMatch == '0') {
                foreach ($schedule->coaches as $coach) {
                    $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['resultStatus' => 'Win']);
                }
            }
            // check if match are in competition
            if ($schedule->competition()->exists()){
                // update our teams stats in competition
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
                // update opponent teams stats in competition
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

        } elseif ($academyTeamScore < $opponentTeamScore){ // if our team result are lose
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['resultStatus'=> 'Win']);
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['resultStatus'=> 'Lose']);
            // update match result data of each coach match stats
            if ($schedule->isOpponentTeamMatch == '0') {
                foreach ($schedule->coaches as $coach) {
                    $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['resultStatus' => 'Lose']);
                }
            }

            // check if match are in competition
            if ($schedule->competition()->exists()){
                // update our teams stats in competition
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
                // update opponent teams stats in competition
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

        } elseif ($academyTeamScore == $opponentTeamScore){ // if team match result are draw
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['resultStatus'=> 'Draw']);
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['resultStatus'=> 'Draw']);
            // update match result data of each coach match stats
            if ($schedule->isOpponentTeamMatch == '0') {
                foreach ($schedule->coaches as $coach) {
                    $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['resultStatus' => 'Draw']);
                }
            }

            // check if match are in competition
            if ($schedule->competition()->exists()){
                // update our teams stats in competition
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
                // update opponent teams stats in competition
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

        // check if our team is cleansheet
        if ($academyTeamScore == 0){
            // update teams clean sheets data
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['cleanSheets'=> 1]);

            // update clean sheets data of each coach match stats
            if ($schedule->isOpponentTeamMatch == '0') {
                foreach ($schedule->coaches as $coach) {
                    $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['cleanSheets' => 1]);
                }
            }
        }

        // check if opponent team is cleansheet
        if ($opponentTeamScore == 0){
            // update teams clean sheets data
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['cleanSheets'=> 1]);
        }

        return $this->setStatus($schedule, 'Completed');
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
        $schedule->players()->updateExistingPivot($player->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
        if ($schedule->eventType == 'Training') {
            $player->user->notify(new TrainingScheduleAttendance($schedule, $data['attendanceStatus']));
        } elseif ($schedule->eventType == 'Match') {
            $player->user->notify(new MatchScheduleAttendance($schedule, $data['attendanceStatus']));
        }
        return $schedule;
    }
    public function updateCoachAttendanceStatus($data, EventSchedule $schedule, Coach $coach){
        $schedule->coaches()->updateExistingPivot($coach->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
        if ($schedule->eventType == 'Training') {
            $coach->user->notify(new TrainingScheduleAttendance($schedule, $data['attendanceStatus']));
        } elseif ($schedule->eventType == 'Match') {
            $coach->user->notify(new MatchScheduleAttendance($schedule, $data['attendanceStatus']));
        }
        return $schedule;
    }

    public function createNote($data, EventSchedule $schedule, $loggedUser){
        $data['scheduleId'] = $schedule->id;
        $note = ScheduleNote::create($data);

        $teamParticipants = $this->userRepository->allTeamsParticipant($schedule->teams[0]);
        if ($schedule->eventType == 'Training') {
            Notification::send($teamParticipants, new TrainingNote($loggedUser, $schedule, 'created'));
        } elseif ($schedule->eventType == 'Match') {
            Notification::send($teamParticipants, new MatchNote($loggedUser, $schedule, 'created'));
        }
        return $note;
    }
    public function updateNote($data, EventSchedule $schedule, ScheduleNote $note, $loggedUser){
        $note->update($data);
        $teamParticipants = $this->userRepository->allTeamsParticipant($schedule->teams[0]);
        if ($schedule->eventType == 'Training') {
            Notification::send($teamParticipants, new TrainingNote($loggedUser, $schedule, 'updated'));
        } elseif ($schedule->eventType == 'Match') {
            Notification::send($teamParticipants, new MatchNote($loggedUser, $schedule, 'updated'));
        }
        return $note;
    }
    public function destroyNote(EventSchedule $schedule, ScheduleNote $note, $loggedUser)
    {
        $note->delete();
        $teamParticipants = $this->userRepository->allTeamsParticipant($schedule->teams[0]);
        if ($schedule->eventType == 'Training') {
            Notification::send($teamParticipants, new TrainingNote($loggedUser, $schedule, 'deleted'));
        } elseif ($schedule->eventType == 'Match') {
            Notification::send($teamParticipants, new MatchNote($loggedUser, $schedule, 'deleted'));
        }
        return $note;
    }

    public function storeMatchScorer($data, EventSchedule $schedule, $awayTeam = false)
    {
        $data['eventId'] = $schedule->id;
        $data['isOwnGoal'] = '0';
        $scorer = MatchScore::create($data);
        // $player = $schedule->playerMatchStats()->find($data['playerId']);
        // $assistPlayer = $schedule->playerMatchStats()->find($data['assistPlayerId']);

        $player = $schedule->playerMatchStats()->where('playerId', $data['playerId'])->where('teamId', $data['teamId'])->first();
        $assistPlayer = $schedule->playerMatchStats()->where('playerId', $data['assistPlayerId'])->where('teamId', $data['teamId'])->first();

        $playerGoal = $player->pivot->goals + 1;
        $playerAssist = $assistPlayer->pivot->assists + 1;

        if ($awayTeam) {
            $teamScore = $schedule->teams[1]->pivot->teamScore + 1;
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamScore' => $teamScore]);
        } else {
            $teamScore = $schedule->teams[0]->pivot->teamScore + 1;
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamScore' => $teamScore]);
        }

        $coaches = $schedule->coachMatchStats()->where('teamId', $data['teamId'])->get();
        // update team score data of each coach match stats
        foreach ($coaches as $coach){
            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['teamScore' => $teamScore]);
        }

        $schedule->playerMatchStats()->updateExistingPivot($data['playerId'], ['goals' => $playerGoal]);
        $schedule->playerMatchStats()->updateExistingPivot($data['assistPlayerId'], ['assists' => $playerAssist]);

        // update team score data of each coach match stats
//        foreach ($schedule->coaches as $coach){
//            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['teamScore' => $teamScore]);
//        }

        return $scorer;
    }
    public function destroyMatchScorer(EventSchedule $schedule, MatchScore $scorer, $awayTeam = false)
    {
        $player = $schedule->playerMatchStats()->where('playerId', $scorer->playerId)->where('teamId', $scorer->teamId)->first();
        $assistPlayer = $schedule->playerMatchStats()->where('playerId', $scorer->assistPlayerId)->where('teamId', $scorer->teamId)->first();

        $playerGoal = $player->pivot->goals - 1;
        $playerAssist = $assistPlayer->pivot->assists - 1;

        if ($awayTeam) {
            $teamScore = $schedule->teams[1]->pivot->teamScore - 1;
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamScore' => $teamScore]);
        } else {
            $teamScore = $schedule->teams[0]->pivot->teamScore - 1;
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamScore' => $teamScore]);
        }

        $coaches = $schedule->coachMatchStats()->where('teamId', $scorer->teamId)->get();
        // update team score data of each coach match stats
        foreach ($coaches as $coach){
            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['teamScore' => $teamScore]);
        }

        $schedule->playerMatchStats()->updateExistingPivot($scorer->playerId, ['goals' => $playerGoal]);
        $schedule->playerMatchStats()->updateExistingPivot($scorer->assistPlayerId, ['assists' => $playerAssist]);

        return $scorer->delete();
    }

    public function storeOwnGoal($data, EventSchedule $schedule, $awayTeam = false)
    {
        $data['eventId'] = $schedule->id;
        $data['isOwnGoal'] = '1';
        $scorer = MatchScore::create($data);
        $player = $schedule->playerMatchStats()->where('playerId', $data['playerId'])->where('teamId', $data['teamId'])->first();

        $playerOwnGoal = $player->pivot->ownGoal + 1;

        if ($awayTeam) {
            $teamScore = $schedule->teams[0]->pivot->teamScore + 1;
            $teamOwnGoal = $schedule->teams[1]->pivot->teamOwnGoal + 1;
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamScore' => $teamScore]);
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamOwnGoal' => $teamOwnGoal]);
        } else {
            $teamOwnGoal = $schedule->teams[0]->pivot->teamOwnGoal + 1;
            $teamScore = $schedule->teams[1]->pivot->teamScore + 1;
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamScore' => $teamScore]);
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamOwnGoal' => $teamOwnGoal]);
        }

        $schedule->playerMatchStats()->updateExistingPivot($data['playerId'], ['ownGoal' => $playerOwnGoal]);

        $coaches = $schedule->coachMatchStats()->where('teamId', $scorer->teamId)->get();
        // update team score data of each coach match stats
        foreach ($coaches as $coach){
            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['teamOwnGoal' => $teamOwnGoal]);
        }

        return $scorer;
    }

    public function destroyOwnGoal(EventSchedule $schedule, MatchScore $scorer, $awayTeam = false)
    {
        $player = $schedule->playerMatchStats()->where('playerId', $scorer->playerId)->where('teamId', $scorer->teamId)->first();
        $playerOwnGoal = $player->pivot->ownGoal - 1;

        if ($awayTeam) {
            $teamScore = $schedule->teams[0]->pivot->teamScore - 1;
            $teamOwnGoal = $schedule->teams[1]->pivot->teamOwnGoal - 1;
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamScore' => $teamScore]);
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamOwnGoal' => $teamOwnGoal]);
        } else {
            $teamOwnGoal = $schedule->teams[0]->pivot->teamOwnGoal - 1;
            $teamScore = $schedule->teams[1]->pivot->teamScore - 1;
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, ['teamScore' => $teamScore]);
            $schedule->teams()->updateExistingPivot($schedule->teams[0]->id, ['teamOwnGoal' => $teamOwnGoal]);
        }

        $schedule->playerMatchStats()->updateExistingPivot($scorer->playerId, ['ownGoal' => $playerOwnGoal]);

        $coaches = $schedule->coachMatchStats()->where('teamId', $scorer->teamId)->get();
        // update team score data of each coach match stats
        foreach ($coaches as $coach){
            $schedule->coachMatchStats()->updateExistingPivot($coach->id, ['teamOwnGoal' => $teamOwnGoal]);
        }

        return $scorer->delete();
    }

    public function updateMatchStats(array $data, EventSchedule $schedule)
    {
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
            $coaches = $schedule->coachMatchStats()->where('teamId', $schedule->teams[0]->id)->get();
            foreach ($coaches as $coach){
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


        if ($schedule->matchType == 'Internal Match') {
            $schedule->teams()->updateExistingPivot($schedule->teams[1]->id, [
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

            $coaches = $schedule->coachMatchStats()->where('teamId', $schedule->teams[1]->id)->get();
            foreach ($coaches as $coach){
                $schedule->coachMatchStats()->updateExistingPivot($coach->id, [
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
            }
        } else {
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
        }

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

        $schedule->playerMatchStats()->updateExistingPivot($player->id, $data);
        $player->user->notify(new MatchStatsPlayer($schedule));
        return $schedule;
    }

    public function destroy(EventSchedule $schedule, $loggedUser)
    {
        $team = $schedule->teams()->first();
        $deletedBy = $this->getUserFullName($loggedUser);

        $admins = $this->userRepository->getAllAdminUsers();
        $adminsCoachesTeamsParticipant = $this->userRepository->allTeamsParticipant($team, players: false);
        $playersTeamsParticipants = $this->userRepository->allTeamsParticipant($team, admins: false, coaches: false);
        $playersCoachesTeamsParticipants = $this->userRepository->allTeamsParticipant($team, admins: false);

        try {
            if ($schedule->eventType == 'Training') {
                Notification::send($adminsCoachesTeamsParticipant, new TrainingScheduleDeletedForCoachAdmin($schedule, $team, $deletedBy));
                Notification::send($playersTeamsParticipants, new TrainingScheduleDeletedForPlayers($schedule));
            } elseif ($schedule->eventType == 'Match' && $schedule->isOpponentTeamMatch == '0') {
                Notification::send($admins, new MatchScheduleDeletedForAdmin($schedule, $deletedBy));
                Notification::send($playersCoachesTeamsParticipants, new MatchScheduleDeletedForPlayersCoaches($schedule));
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

//        $schedule->teams()->detach();
//        $schedule->players()->detach();
//        $schedule->coaches()->detach();
//        $schedule->delete();
        return $schedule->delete();
    }
}
