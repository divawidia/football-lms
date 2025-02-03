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
use App\Repository\Interface\PlayerRepositoryInterface;
use App\Repository\MatchRepository;
use App\Repository\Interface\LeagueStandingRepositoryInterface;
use App\Repository\PlayerPerformanceReviewRepository;
use App\Repository\PlayerSkillStatsRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class MatchService extends Service
{
    private MatchRepository $matchRepository;
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private PlayerRepositoryInterface $playerRepository;
    private PlayerSkillStatsRepository $playerSkillStatsRepository;
    private PlayerPerformanceReviewRepository $playerPerformanceReviewRepository;
    private LeagueStandingRepositoryInterface $leagueStandingRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(
        MatchRepository                   $matchRepository,
        TeamRepository                    $teamRepository,
        UserRepository                    $userRepository,
        PlayerRepositoryInterface $playerRepository,
        PlayerSkillStatsRepository        $playerSkillStatsRepository,
        PlayerPerformanceReviewRepository $playerPerformanceReviewRepository,
        LeagueStandingRepositoryInterface $leagueStandingRepository,
        DatatablesHelper                  $datatablesHelper)
    {
        $this->matchRepository = $matchRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->playerRepository = $playerRepository;
        $this->playerSkillStatsRepository = $playerSkillStatsRepository;
        $this->playerPerformanceReviewRepository = $playerPerformanceReviewRepository;
        $this->leagueStandingRepository = $leagueStandingRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function indexMatch(): Collection
    {
        return $this->matchRepository->getAll(relations: ['teams', 'competition'], status: ['Scheduled', 'Ongoing']);
    }

    public function coachTeamsIndexMatch(Coach $coach): Collection
    {
        return $this->matchRepository->getByRelation($coach, withRelation: ['team', 'competition'], status: ['Scheduled', 'Ongoing']);
    }

    public function playerTeamsIndexMatch(Player $player): Collection
    {
        return $this->matchRepository->getByRelation($player,  withRelation: ['team', 'competition'], status: ['Scheduled', 'Ongoing']);
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

    public function matchCalendar(){
        $matches = $this->indexMatch();
        return $this->makeMatchCalendar($matches);
    }
    public function coachTeamsMatchCalendar(Coach $coach){
        $data = $this->coachTeamsIndexMatch($coach);
        return $this->makeMatchCalendar($data);
    }

    public function playerTeamsMatchCalendar(Player $player){
        $data = $this->playerTeamsIndexMatch($player);
        return $this->makeMatchCalendar($data);
    }

    public function makeDataTablesMatch($matchData)
    {
        return Datatables::of($matchData)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('match-schedules.show', $item->hash), icon: 'visibility', btnText: 'View match session');
                if (isAllAdmin()) {
                    if ($item->status == 'Scheduled'){
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('cancelBtn', $item->id, 'danger', icon: 'block', btnText: 'Cancel match');
                    } elseif ($item->status == 'Cancelled') {
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('scheduled-btn', $item->id, 'warning', icon: 'check_circle', btnText: 'Set Match to Scheduled');
                    }
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete', $item->id, iconColor: 'danger', icon: 'delete', btnText: 'Delete Match');
                }

                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('homeTeam', function ($item) {
                return $this->datatablesHelper->name($item->homeTeam->logo, $item->homeTeam->teamName, $item->homeTeam->ageGroup, route('team-managements.show', $item->homeTeam->hash));
            })
            ->editColumn('awayTeam', function ($item) {
                return ($item->matchType == 'Internal Match') ? $this->datatablesHelper->name($item->awayTeam->logo, $item->awayTeam->teamName, $item->awayTeam->ageGroup, route('team-managements.show', $item->awayTeam->hash)) : $item->externalTeam->teamName;
            })
            ->editColumn('score', function ($item) {
                $awayTeamScore = ($item->matchType == 'Internal Match') ? $this->awayTeamMatch($item)->pivot->teamScore : $item->externalTeam->teamScore;
                return '<p class="mb-0"><strong class="js-lists-values-lead">' .$this->homeTeamMatch($item)->pivot->teamScore . ' - ' . $awayTeamScore.'</strong></p>';
            })
            ->editColumn('competition', function ($item) {
                return ($item->competition) ?  $this->datatablesHelper->name($item->competition->logo, $item->competition->name, $item->competition->type) : 'No Competition';
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesHelper->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->rawColumns(['action','homeTeam', 'awayTeam', 'score','competition','status'])
            ->addIndexColumn()
            ->make();
    }

    public function dataTablesMatch(){
        $data = $this->indexMatch();
        return $this->makeDataTablesMatch($data);
    }

    public function coachTeamsDataTablesMatch(Coach $coach){
        $data = $this->coachTeamsIndexMatch($coach);
        return $this->makeDataTablesMatch($data);
    }

    public function playerTeamsDataTablesMatch(Player $player){
        $data = $this->playerTeamsIndexMatch($player);
        return $this->makeDataTablesMatch($data);
    }

    public function dataTablesPlayerStats(MatchModel $match, $teamId = null){
        $data = $this->matchRepository->getRelationData($match, 'playerMatchStats', teamId: $teamId, retrieveType: 'multiple');

        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($match) {
                ($match->status == 'Ongoing' || $match->status == 'Completed') ? $editPlayer = $this->datatablesHelper->buttonDropdownItem('edit-player-stats', $item->id, icon: 'edit', btnText: 'Edit Player Stats') : $editPlayer = '';

                return (isAllAdmin() || isCoach()) ?
                    $this->datatablesHelper->dropdown(function () use ($editPlayer, $item) {
                        return $this->datatablesHelper->linkDropdownItem(route: route('player-managements.show', $item->hash), icon: 'visibility', btnText: 'View player profile') . $editPlayer;
                    }) : '';
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('updated_at', function ($item) {
                return $this->convertToDatetime($item->updated_at);
            })
            ->rawColumns(['action','name'])
            ->make();
    }

//    public function dataTablesPlayerSkills(MatchModel $match){
//        $data = $match->players;
//        return Datatables::of($data)
//            ->addColumn('action', function ($item) use ($match){
//                $stats = $item->playerSkillStats()->where('eventId', $match->id)->first();
//                $review = $item->playerPerformanceReview()->where('eventId', $match->id)->first();
//
//                if ( isAllAdmin() ){
//                    $button = '<a class="dropdown-item" href="' . route('player-managements.skill-stats', ['player'=>$item->hash]) . '"><span class="material-icons">visibility</span> View Player Skill Stats</a>';
//                }
//                elseif( isCoach() ){
//                    if (!$stats){
//                        $statsBtn = '<a class="dropdown-item addSkills" id="'.$item->id.'" data-eventId="'.$match->id.'"><span class="material-icons">edit</span> Evaluate Player Skills Stats</a>';
//                    } else {
//                        $statsBtn = '<a class="dropdown-item editSkills" id="'.$item->id.'" data-eventId="'.$match->id.'" data-statsId="'.$stats->id.'"><span class="material-icons">edit</span> Edit Player Skills Stats</a>';
//                    }
//
//                    if (!$review){
//                        $reviewBtn = '<a class="dropdown-item addPerformanceReview" id="'.$item->id.'" data-eventId="'.$match->id.'"><span class="material-icons">add</span> Add Player Performance Review</a>';
//                    } else {
//                        $reviewBtn = '<a class="dropdown-item editPerformanceReview" id="'.$item->id.'" data-eventId="'.$match->id.'"  data-reviewId="'.$review->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>';
//                    }
//
//                    $button = '<div class="dropdown">
//                                      <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
//                                        <span class="material-icons">
//                                            more_vert
//                                        </span>
//                                      </button>
//                                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
//                                            <a class="dropdown-item" href="' . route('player-managements.skill-stats', ['player'=>$item->hash]) . '"><span class="material-icons">visibility</span> View Player Skill Stats</a>
//                                            '.$statsBtn.'
//                                            '.$reviewBtn.'
//                                      </div>
//                                </div>';
//                }
//                return $button;
//            })
//            ->editColumn('name', function ($item) {
//                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name,route('player-managements.show', $item->hash));
//            })
//            ->editColumn('stats_status', function ($item) use ($match){
//                $stats = $item->playerSkillStats()->where('eventId', $match->id)->first();
//                if ($stats){
//                    $date = 'Skill stats have been added';
//                } else{
//                    $date = 'Skill stats still not added yet';
//                }
//                return $date;
//            })
//            ->editColumn('stats_created', function ($item) use ($match){
//                $stats = $item->playerSkillStats()->where('eventId', $match->id)->first();
//                if ($stats){
//                    $date = $this->convertToDatetime($stats->created_at);
//                } else{
//                    $date = '-';
//                }
//                return $date;
//            })
//            ->editColumn('stats_updated', function ($item) use ($match){
//                $stats = $item->playerSkillStats()->where('eventId', $match->id)->first();
//                if ($stats){
//                    $date = $this->convertToDatetime($stats->updated_at);
//                } else{
//                    $date = '-';
//                }
//                return $date;
//            })
//            ->editColumn('performance_review', function ($item) use ($match){
//                $review = $item->playerPerformanceReview()->where('eventId', $match->id)->first();
//                if ($review){
//                    $text = $review->performanceReview;
//                } else{
//                    $text = 'Performance review still not added yet';
//                }
//                return $text;
//            })
//            ->editColumn('performance_review_created', function ($item) use ($match){
//                $review = $item->playerPerformanceReview()->where('eventId', $match->id)->first();
//                if ($review){
//                    $text = $this->convertToDatetime($review->created_at);
//                } else{
//                    $text = '-';
//                }
//                return $text;
//            })
//            ->editColumn('performance_review_last_updated', function ($item) use ($match){
//                $review = $item->playerPerformanceReview()->where('eventId', $match->id)->first();
//                if ($review){
//                    $text = $this->convertToDatetime($review->updated_at);
//                } else{
//                    $text = '-';
//                }
//                return $text;
//            })
//            ->rawColumns(['action','name', 'stats_status', 'stats_created', 'stats_updated', 'performance_review', 'performance_review_created','performance_review_last_updated'])
//            ->make();
//    }

    public function playerSkills(MatchModel $match, Player $player = null)
    {
        return $this->playerSkillStatsRepository->getByPlayer($player, $match)->first();
    }

    public function playerPerformanceReviews(MatchModel $match, Player $player = null)
    {
        return $this->playerPerformanceReviewRepository->getByPlayer($player, $match);
    }

    public function getTeamMatchStats(MatchModel $match, $teamSide = 'homeTeam')
    {
        if ($match->matchType == 'Internal Match') {
            ($teamSide == 'homeTeam') ? $team = $this->homeTeamMatch($match) : $team = $this->awayTeamMatch($match);
        } else {
            ($teamSide == 'homeTeam') ? $team = $this->homeTeamMatch($match) : $team = $match->externalTeam;
        }
        return $team;
    }

    public function homeTeamMatch(MatchModel $match)
    {
        return $this->matchRepository->getRelationData($match, 'teams', teamId: $match->homeTeamId);
    }
    public function awayTeamMatch(MatchModel $match)
    {
        return $this->matchRepository->getRelationData($match, 'teams', teamId: $match->awayTeamId);
    }
    public function homeTeamPlayers(MatchModel $match, $exceptPlayerId = null)
    {
        return $this->matchRepository->getRelationData($match, 'players', with: ['user', 'position'], teamId: $match->homeTeamId, exceptPlayerId: $exceptPlayerId, retrieveType: 'multiple');
    }
    public function awayTeamPlayers(MatchModel $match, $exceptPlayerId = null)
    {
        return $this->matchRepository->getRelationData($match, 'players', with: ['user', 'position'], teamId: $match->awayTeamId, exceptPlayerId: $exceptPlayerId, retrieveType: 'multiple');
    }
    public function homeTeamCoaches(MatchModel $match)
    {
        return $this->matchRepository->getRelationData($match, 'coaches', with: 'user', teamId: $match->homeTeamId, retrieveType: 'multiple');
    }
    public function awayTeamCoaches(MatchModel $match)
    {
        return $this->matchRepository->getRelationData($match, 'coaches', with: 'user', teamId: $match->awayTeamId, retrieveType: 'multiple');
    }
    public function homeTeamMatchScorers(MatchModel $match)
    {
        return $this->matchRepository->getRelationData($match, 'matchScores', teamId: $match->homeTeamId, retrieveType: 'multiple');
    }
    public function awayTeamMatchScorers(MatchModel $match)
    {
        return $this->matchRepository->getRelationData($match, 'matchScores', teamId: $match->awayTeamId, retrieveType: 'multiple');
    }
    public function homeTeamNotes(MatchModel $match)
    {
        return $this->matchRepository->getRelationData($match, 'notes', teamId: $match->homeTeamId, retrieveType: 'multiple');
    }
    public function awayTeamNotes(MatchModel $match)
    {
        return $this->matchRepository->getRelationData($match, 'notes', teamId: $match->awayTeamId, retrieveType: 'multiple');
    }

    public function getMatchPLayers(MatchModel $match, $team, $exceptPlayerId)
    {
        $isHomeTeam = $team === 'homeTeam';
        return [
            'players' => $isHomeTeam
                ? $this->homeTeamPlayers($match, $exceptPlayerId)
                : $this->awayTeamPlayers($match, $exceptPlayerId),
            'team' => $isHomeTeam ? $match->homeTeam : $match->awayTeam,
        ];
    }

    public function getMatchDetail(MatchModel $match)
    {
        if ($match->matchType == 'External Match') {
            $opposingTeam = $match->externalTeam->teamName;
            return compact('match', 'opposingTeam');
        } else {
            return compact('match');
        }
    }

    public function totalParticipant(MatchModel $match, $homeTeam = true)
    {
        if ($homeTeam) {
            $players = $this->homeTeamPlayers($match);
            $coaches = $this->homeTeamCoaches($match);
        } else {
            $players = $this->awayTeamPlayers($match);
            $coaches = $this->awayTeamCoaches($match);
        }
        return count($players) + count($coaches);
    }

    public function totalIllness(MatchModel $match, Team $team = null)
    {
        $playerIllness = $this->matchRepository->getRelationData($match, 'players', attendanceStatus: 'Illness', teamId: $team->id, retrieveType: 'count');
        $coachIllness = $this->matchRepository->getRelationData($match, 'coaches', attendanceStatus: 'Illness', teamId: $team->id, retrieveType: 'count');
        return $playerIllness + $coachIllness;
    }

    public function totalOther(MatchModel $match, Team $team = null)
    {
        $playerOther = $this->matchRepository->getRelationData($match, 'players', attendanceStatus: 'Other', teamId: $team->id, retrieveType: 'count');
        $coachOther = $this->matchRepository->getRelationData($match, 'coaches', attendanceStatus: 'Other', teamId: $team->id, retrieveType: 'count');
        return $playerOther + $coachOther;
    }

    public function totalInjured(MatchModel $match, Team $team = null)
    {
        $playerInjured = $this->matchRepository->getRelationData($match, 'players', attendanceStatus: 'Injured', teamId: $team->id, retrieveType: 'count');
        $coachInjured = $this->matchRepository->getRelationData($match, 'coaches', attendanceStatus: 'Injured', teamId: $team->id, retrieveType: 'count');
        return $playerInjured + $coachInjured;
    }

    public function totalDidntAttend(MatchModel $match, Team $team = null)
    {
        return $this->totalInjured($match, $team) + $this->totalIllness($match, $team) + $this->totalOther($match, $team);
    }

    public function totalAttended(MatchModel $match, Team $team = null)
    {
        $playerAttended = $this->matchRepository->getRelationData($match, 'players', attendanceStatus: 'Attended', teamId: $team->id, retrieveType: 'count');
        $coachAttended = $this->matchRepository->getRelationData($match, 'coaches', attendanceStatus: 'Attended', teamId: $team->id, retrieveType: 'count');
        return $playerAttended + $coachAttended;
    }

    public function playerTotalMatch(Player $player, $startDate = null, $endDate = null, $matchStatus = null)
    {
        return $this->playerRepository->matchResults($player, $matchStatus, $startDate, $endDate);
    }

    public function getFriendlyMatchTeam()
    {
        $teams = $this->teamRepository->getByTeamside('Academy Team');
        $opponentTeams = $this->teamRepository->getByTeamside('Opponent Team');
        return compact('teams', 'opponentTeams');
    }

    public function internalMatchTeams($exceptTeamId = null)
    {
        return $this->teamRepository->getByTeamside('Academy Team', $exceptTeamId);
    }

    public function storeMatch(array $data, $userId){
        $data['userId'] = $userId;
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $match =  $this->matchRepository->create($data);

        $match->teams()->attach($data['homeTeamId']);

        $team = $this->teamRepository->find($data['homeTeamId']);
        $teamParticipant = $this->userRepository->allTeamsParticipant($team);

        $match->players()->attach($team->players, ['teamId' => $team->id]);
        $match->playerMatchStats()->attach($team->players, ['teamId' => $team->id]);
        $match->coaches()->attach($team->coaches, ['teamId' => $team->id]);
        $match->coachMatchStats()->attach($team->coaches, ['teamId' => $team->id]);

        Notification::send($teamParticipant, new MatchSchedule($match, 'create'));

        if ($data['matchType'] == 'Internal Match'){
            $match->teams()->attach($data['awayTeamId']);

            $awayTeam = $this->teamRepository->find($data['awayTeamId']);
            $awayTeamsPlayersCoaches = $this->userRepository->allTeamsParticipant($awayTeam, admins: false);

            $match->players()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $match->playerMatchStats()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $match->coaches()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);
            $match->coachMatchStats()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);

            Notification::send($awayTeamsPlayersCoaches, new MatchSchedule($match, 'create'));
        } else {
            $match->externalTeam()->create([
                'teamName' => $data['externalTeamName'],
            ]);
        }
        return $match;
    }

    public function updateMatch(array $data, MatchModel $match)
    {
        $data['startDatetime'] = $this->convertToTimestamp($data['date'], $data['startTime']);
        $data['endDatetime'] = $this->convertToTimestamp($data['date'], $data['endTime']);
        $match->update($data);

        $homeTeam = $this->teamRepository->find($data['homeTeamId']);
        $match->players()->syncWithPivotValues($homeTeam->players, ['teamId' => $homeTeam->id]);
        $match->playerMatchStats()->syncWithPivotValues($homeTeam->players, ['teamId' => $homeTeam->id]);
        $match->coaches()->syncWithPivotValues($homeTeam->coaches, ['teamId' => $homeTeam->id]);
        $match->coachMatchStats()->syncWithPivotValues($homeTeam->coaches, ['teamId' => $homeTeam->id]);

        $homeTeamParticipant = $this->userRepository->allTeamsParticipant($homeTeam);
        Notification::send($homeTeamParticipant, new MatchSchedule($match, 'update'));

        if ($match->matchType == 'Internal Match') {
            $match->teams()->sync([
                $data['homeTeamId'],
                $data['awayTeamId']
            ]);

            $awayTeam = $this->teamRepository->find($data['awayTeamId']);

            $match->players()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $match->playerMatchStats()->attach($awayTeam->players, ['teamId' => $awayTeam->id]);
            $match->coaches()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);
            $match->coachMatchStats()->attach($awayTeam->coaches, ['teamId' => $awayTeam->id]);

            $awayTeamParticipant = $this->userRepository->allTeamsParticipant($awayTeam, admins: false);
            Notification::send($awayTeamParticipant, new MatchSchedule($match, 'update'));
        } else {
            $match->teams()->sync([
                $data['homeTeamId'],
            ]);
            $match->externalTeam()->update([
                'teamName' => $data['externalTeamName'],
            ]);
        }
        return $match;
    }

    public function setStatus(MatchModel $match, $status): MatchModel
    {
        $this->matchRepository->updateStatus($match, $status);
            $homeTeam = $match->homeTeam;
            $homeTeamParticipants = $this->userRepository->allTeamsParticipant($homeTeam);
            Notification::send($homeTeamParticipants, new  MatchSchedule($match, $status));

            if ($match->matchType == 'Internal Match') {
                $awayTeam = $match->awayTeam;
                $awayTeamParticipants = $this->userRepository->allTeamsParticipant($awayTeam, admins: false);
                Notification::send($awayTeamParticipants, new  MatchSchedule($match, $status));
            }
        return $match;
    }


    public function endMatch(MatchModel $match)
    {
        $homeTeam = $this->homeTeamMatch($match);
        $homeTeamScore = $homeTeam->pivot->teamScore;

        if ($match->matchType == 'External Match') {
            $externalTeamScore = $match->externalTeam->teamScore;

            if ($externalTeamScore == 0) {
                $homeTeamCleanSheet = $homeTeam->pivot->cleanSheets + 1;
                $match->teams()->updateExistingPivot($homeTeam->id, ['cleanSheets' => $homeTeamCleanSheet]);
                $match->coachMatchStats()->updateExistingPivot($homeTeam->id, ['cleanSheets' => $homeTeamCleanSheet]);
            }

            $win = 0;
            $lose = 0;
            $draw = 0;

            if ($homeTeamScore > $externalTeamScore) {
                $match->externalTeam()->update(['resultStatus' => 'Lose']);
                $match->update(['winnerTeamId' => $homeTeam->id]);
                $match->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Win']);
                $match->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Win']);
                $win = 1;
            } elseif ($homeTeamScore < $externalTeamScore) {
                $match->externalTeam()->update(['resultStatus' => 'Win']);
                $match->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Lose']);
                $match->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Lose']);
                $lose = 1;
            } elseif ($homeTeamScore == $externalTeamScore) {
                $match->externalTeam()->update(['resultStatus' => 'Draw']);
                $match->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Draw']);
                $match->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Draw']);
                $draw = 1;
            }

            if ($match->competition->type == 'league') {
                if (count($this->leagueStandingRepository->getAll($match->competition, $homeTeam)) == 0) { //check if team is not added in league standing, then add team into league standing
                    $data['teams'] = $homeTeam->id;
                    $this->leagueStandingRepository->create($data, $match->competition);
                }
                $teamLeagueStanding = $match->competition->standings()->where('teamId', $homeTeam->id)->first();
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
            $awayTeam = $this->awayTeamMatch($match);
            $awayTeamScore = $awayTeam->pivot->teamScore;

            if ($awayTeamScore == 0) {
                $homeTeamCleanSheet = $homeTeam->pivot->cleanSheets + 1;
                $match->teams()->updateExistingPivot($homeTeam->id, ['cleanSheets' => $homeTeamCleanSheet]);
                $match->coachMatchStats()->updateExistingPivot($homeTeam->id, ['cleanSheets' => $homeTeamCleanSheet]);
            }
            if ($homeTeamScore == 0) {
                $awayTeamCleanSheet = $awayTeam->pivot->cleanSheets + 1;
                $match->teams()->updateExistingPivot($awayTeam->id, ['cleanSheets' => $awayTeamCleanSheet]);
                $match->coachMatchStats()->updateExistingPivot($awayTeam->id, ['cleanSheets' => $awayTeamCleanSheet]);
            }

            $homeTeamWon = 0;
            $homeTeamDraw = 0;
            $homeTeamLost = 0;
            $awayTeamWon = 0;
            $awayTeamDraw = 0;
            $awayTeamLost = 0;

            if ($homeTeamScore > $awayTeamScore) {
                $match->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Win']);
                $match->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Win']);
                $match->update(['winnerTeamId' => $homeTeam->id]);
                $homeTeamWon = 1;
                $match->teams()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Lose']);
                $match->coachMatchStats()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Lose']);
                $awayTeamLost = 1;
            } elseif ($homeTeamScore < $awayTeamScore) {
                $match->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Lose']);
                $match->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Lose']);
                $homeTeamLost = 1;
                $match->teams()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Win']);
                $match->coachMatchStats()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Win']);
                $match->update(['winnerTeamId' => $awayTeam->id]);
                $awayTeamWon = 1;
            } elseif ($homeTeamScore == $awayTeamScore) {
                $match->teams()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Draw']);
                $match->coachMatchStats()->updateExistingPivot($homeTeam->id, ['resultStatus' => 'Draw']);
                $homeTeamDraw = 1;
                $match->teams()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Draw']);
                $match->coachMatchStats()->updateExistingPivot($awayTeam->id, ['resultStatus' => 'Draw']);
                $awayTeamDraw = 1;
            }

            if ($match->competition->type == 'league') {
                if (count($this->leagueStandingRepository->getAll($match->competition, $homeTeam)) == 0) { //check if home team is not added in league standing, then add team into league standing
                    $homeData['teams'] = $homeTeam->id;
                    $this->leagueStandingRepository->create($homeData, $match->competition);
                }
                if (count($this->leagueStandingRepository->getAll($match->competition, $awayTeam)) == 0) { //check if away team is not added in league standing, then add team into league standing
                    $awayData['teams'] = $awayTeam->id;
                    $this->leagueStandingRepository->create($awayData, $match->competition);
                }
                $homeTeamLeagueStanding = $match->competition->standings()->where('teamId', $homeTeam->id)->first();
                $awayTeamLeagueStanding = $match->competition->standings()->where('teamId', $awayTeam->id)->first();

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
        return $this->setStatus($match, 'completed');
    }


    public function getPlayerAttendance(MatchModel $match, Player $player)
    {
        return $match->players()->find($player->id);
    }
    public function getCoachAttendance(MatchModel $match, Coach $coach)
    {
        return $match->coaches()->find($coach->id);
    }

    public function updatePlayerAttendanceStatus($data, MatchModel $match, Player $player){
        $match->players()->updateExistingPivot($player->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
        $player->user->notify(new MatchScheduleAttendance($match, $data['attendanceStatus']));
        return $match;
    }
    public function updateCoachAttendanceStatus($data, MatchModel $match, Coach $coach){
        $match->coaches()->updateExistingPivot($coach->id, ['attendanceStatus'=> $data['attendanceStatus'], 'note' => $data['note']]);
        $coach->user->notify(new MatchScheduleAttendance($match, $data['attendanceStatus']));
        return $match;
    }

    private function sendNoteNotification(MatchModel $match, string $status)
    {
        $homeTeamParticipants = $this->userRepository->allTeamsParticipant($match->homeTeam);
        Notification::send($homeTeamParticipants, new MatchNoteNotification($match, $status));

        if ($match->matchtType == 'Internal Match') {
            $awayTeamParticipants = $this->userRepository->allTeamsParticipant($match->awayTeam, admins: false);
            Notification::send($awayTeamParticipants, new MatchNoteNotification($match, $status));
        }
    }
    public function createNote($data, MatchModel $match){
        $note = $this->matchRepository->createRelation($match, $data, 'notes');
        $this->sendNoteNotification($match, 'created');
        return $note;
    }
    public function updateNote($data, MatchModel $match, MatchNote $note){
        $note->update($data);
        $this->sendNoteNotification($match, 'updated');
        return $note;
    }
    public function destroyNote(MatchModel $match, MatchNote $note)
    {
        $note->delete();
        $this->sendNoteNotification($match, 'deleted');
        return $note;
    }

    public function storeMatchScorer($data, MatchModel $match, $awayTeam = false)
    {
        $data['isOwnGoal'] = '0';
        $scorer = $this->matchRepository->createRelation($match, $data, 'matchScores');

        $player = $this->matchRepository->getRelationData($match, 'playerMatchStats', teamId: $data['teamId'], playerId: $data['playerId']);
        $assistPlayer = $this->matchRepository->getRelationData($match, 'playerMatchStats', teamId: $data['teamId'], playerId: $data['assistPlayerId']);

        $playerGoal = $player->pivot->goals + 1;
        $playerAssist = $assistPlayer->pivot->assists + 1;

        $homeTeamData = $this->homeTeamMatch($match);

        if ($match->matchType == 'External Match') {
            $teamGoalScored = $homeTeamData->pivot->goalScored + 1;
            $teamScore = $teamGoalScored + $match->externalTeam->teamOwnGoal;

            $match->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $teamScore, 'goalScored' => $teamGoalScored]);
            $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' => $teamScore, 'goalScored' => $teamGoalScored]);
            $match->externalTeam()->update(['goalConceded' => $match->externalTeam->goalConceded + 1]);
        } else {
            $awayTeamData = $this->awayTeamMatch($match);

            if ($awayTeam) {
                $wayTeamGoalScored = $awayTeamData->pivot->goalScored + 1;
                $homeTeamGoalConceded = $homeTeamData->pivot->goalConceded + 1;
                $awayTeamScore = $wayTeamGoalScored + $homeTeamData->pivot->teamOwnGoal;

                $match->teams()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore, 'goalScored' => $wayTeamGoalScored]);
                $match->teams()->updateExistingPivot($homeTeamData->id, ['goalConceded' => $homeTeamGoalConceded]);

                $match->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore, 'goalScored' => $wayTeamGoalScored]);
                $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['goalConceded' => $homeTeamGoalConceded]);
            } else {
                $homeTeamGoalScored = $homeTeamData->pivot->goalScored + 1;
                $awayTeamGoalConceded = $awayTeamData->pivot->goalConceded + 1;
                $homeTeamScore = $homeTeamGoalScored + $awayTeamData->pivot->teamOwnGoal;

                $match->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore, 'goalScored' => $homeTeamGoalScored]);
                $match->teams()->updateExistingPivot($awayTeamData->id, ['goalConceded' => $awayTeamGoalConceded]);

                $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore, 'goalScored' => $homeTeamGoalScored]);
                $match->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['goalConceded' => $awayTeamGoalConceded]);
            }
        }

        $match->playerMatchStats()->updateExistingPivot($data['playerId'], ['goals' => $playerGoal]);
        $match->playerMatchStats()->updateExistingPivot($data['assistPlayerId'], ['assists' => $playerAssist]);

        return $scorer;
    }
    public function destroyMatchScorer(MatchModel $match, MatchScore $scorer, $awayTeam = false)
    {
        $player = $this->matchRepository->getRelationData($match, 'playerMatchStats', teamId: $scorer->teamId, playerId: $scorer->playerId);
        $assistPlayer = $this->matchRepository->getRelationData($match, 'playerMatchStats', teamId: $scorer->teamId, playerId: $scorer->assistPlayerId);

        $playerGoal = $player->pivot->goals - 1;
        $playerAssist = $assistPlayer->pivot->assists - 1;

        $homeTeamData = $this->homeTeamMatch($match);

        if ($match->matchType == 'External Match') {
            $teamGoalScored = $homeTeamData->pivot->goalScored - 1;
            $teamScore = $teamGoalScored + $match->externalTeam->teamOwnGoal;

            $match->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $teamScore, 'goalScored' => $teamGoalScored]);
            $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' => $teamScore, 'goalScored' => $teamGoalScored]);
            $match->externalTeam()->update(['teamScore' => $match->externalTeam->goalConceded + 1]);
        } else {
            $awayTeamData = $this->awayTeamMatch($match);

            if ($awayTeam) {
                $awayTeamGoalScored = $awayTeamData->pivot->goalScored - 1;
                $homeTeamGoalConceded = $homeTeamData->pivot->goalConceded - 1;
                $awayTeamScore = $awayTeamGoalScored + $homeTeamData->pivot->teamOwnGoal;

                $match->teams()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore, 'goalScored' => $awayTeamGoalScored]);
                $match->teams()->updateExistingPivot($homeTeamData->id, ['goalConceded' => $homeTeamGoalConceded]);

                $match->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore, 'goalScored' => $awayTeamGoalScored]);
                $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['goalConceded' => $homeTeamGoalConceded]);
            } else {
                $homeTeamGoalScored = $homeTeamData->pivot->goalScored - 1;
                $awayTeamGoalConceded = $awayTeamData->pivot->goalConceded - 1;
                $homeTeamScore = $homeTeamGoalScored + $awayTeamData->pivot->teamOwnGoal;

                $match->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore, 'goalScored' => $homeTeamGoalScored]);
                $match->teams()->updateExistingPivot($awayTeamData->id, ['goalConceded' => $awayTeamGoalConceded]);

                $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore, 'goalScored' => $homeTeamGoalScored]);
                $match->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['goalConceded' => $awayTeamGoalConceded]);
            }
        }
        $match->playerMatchStats()->updateExistingPivot($scorer->playerId, ['goals' => $playerGoal]);
        $match->playerMatchStats()->updateExistingPivot($scorer->assistPlayerId, ['assists' => $playerAssist]);
        return $scorer->delete();
    }

    public function storeOwnGoal($data, MatchModel $match, $awayTeam = false)
    {
        $data['isOwnGoal'] = '1';
        $scorer = $this->matchRepository->createRelation($match, $data, 'matchScores');
        $player = $this->matchRepository->getRelationData($match, 'playerMatchStats', teamId: $data['teamId'], playerId: $data['playerId']);

        $playerOwnGoal = $player->pivot->ownGoal + 1;

        $homeTeamData = $this->homeTeamMatch($match);

        if ($match->matchType == 'External Match') {
            $teamOwnGoal = $homeTeamData->pivot->ownGoal + 1;
            $teamGoalConceded = $homeTeamData->pivot->goalConceded + 1;
            $externalTeamScore = $teamOwnGoal + $match->externalTeam->goalScored;

            $match->teams()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $teamOwnGoal, 'goalConceded' => $teamGoalConceded]);
            $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $teamOwnGoal, 'goalConceded' => $teamGoalConceded]);
            $match->externalTeam()->update(['teamScore' => $externalTeamScore]);
        } else {
            $awayTeamData = $this->awayTeamMatch($match);

            if ($awayTeam) {
                $awayTeamOwnGoal = $awayTeamData->pivot->ownGoal + 1;
                $awayTeamGoalConceded = $awayTeamData->pivot->goalConceded + 1;
                $homeTeamScore = $awayTeamOwnGoal + $homeTeamData->pivot->goalScored;

                $match->teams()->updateExistingPivot($awayTeamData->id, ['teamOwnGoal' => $awayTeamOwnGoal, 'goalConceded' => $awayTeamGoalConceded]);
                $match->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore]);

                $match->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamOwnGoal' =>$awayTeamOwnGoal, 'goalConceded' => $awayTeamGoalConceded]);
                $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' =>$homeTeamScore]);
            } else {
                $homeTeamOwnGoal = $homeTeamData->pivot->ownGoal + 1;
                $homeTeamGoalConceded = $homeTeamData->pivot->goalConceded + 1;
                $awayTeamScore = $homeTeamOwnGoal + $awayTeamData->pivot->goalScored;

                $match->teams()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $homeTeamOwnGoal, 'goalConceded' => $homeTeamGoalConceded]);
                $match->teams()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore]);

                $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $homeTeamOwnGoal, 'goalConceded' => $homeTeamGoalConceded]);
                $match->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore]);
            }
        }

        $match->playerMatchStats()->updateExistingPivot($data['playerId'], ['ownGoal' => $playerOwnGoal]);
        return $scorer;
    }

    public function destroyOwnGoal(MatchModel $match, MatchScore $scorer, $awayTeam = false)
    {
        $player = $this->matchRepository->getRelationData($match, 'playerMatchStats', teamId: $scorer->teamId, playerId: $scorer->playerId);
        $playerOwnGoal = $player->pivot->ownGoal - 1;

        $homeTeamData = $this->homeTeamMatch($match);

        if ($match->matchType == 'External Match') {
            $teamOwnGoal = $homeTeamData->pivot->teamOwnGoal - 1;
            $teamGoalConceded = $homeTeamData->pivot->goalConceded - 1;
            $externalTeamScore = $teamOwnGoal + $match->externalTeam->goalScored;

            $match->teams()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $teamOwnGoal, 'goalConceded' => $teamGoalConceded]);
            $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $teamOwnGoal, 'goalConceded' => $teamGoalConceded]);
            $match->externalTeam()->update(['teamScore' => $externalTeamScore]);
        } else {
            $awayTeamData = $this->awayTeamMatch($match);

            if ($awayTeam) {
                $awayTeamOwnGoal = $awayTeamData->pivot->teamOwnGoal - 1;
                $awayTeamGoalConceded = $awayTeamData->pivot->goalConceded - 1;
                $homeTeamScore = $awayTeamOwnGoal + $homeTeamData->pivot->goalScored;

                $match->teams()->updateExistingPivot($awayTeamData->id, ['teamOwnGoal' => $awayTeamOwnGoal, 'goalConceded' => $awayTeamGoalConceded]);
                $match->teams()->updateExistingPivot($homeTeamData->id, ['teamScore' => $homeTeamScore]);

                $match->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamOwnGoal' => $awayTeamOwnGoal, 'goalConceded' => $awayTeamGoalConceded]);
                $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamScore' =>$homeTeamScore]);
            } else {
                $homeTeamOwnGoal = $homeTeamData->pivot->teamOwnGoal - 1;
                $homeTeamGoalConceded = $homeTeamData->pivot->goalConceded - 1;
                $awayTeamScore = $homeTeamOwnGoal + $awayTeamData->pivot->goalScored;

                $match->teams()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $homeTeamOwnGoal, 'goalConceded' => $homeTeamGoalConceded]);
                $match->teams()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore]);

                $match->coachMatchStats()->updateExistingPivot($homeTeamData->id, ['teamOwnGoal' => $homeTeamOwnGoal, 'goalConceded' => $homeTeamGoalConceded]);
                $match->coachMatchStats()->updateExistingPivot($awayTeamData->id, ['teamScore' => $awayTeamScore]);
            }
        }

        $match->playerMatchStats()->updateExistingPivot($scorer->playerId, ['ownGoal' => $playerOwnGoal]);
        return $scorer->delete();
    }

    public function updateMatchStats(array $data, MatchModel $match)
    {
        if ($match->matchType === 'Internal Match' || $data['teamSide'] === 'homeTeam') {
            $this->matchRepository->updateTeamMatchStats($match, $data);
        } else {
            $this->matchRepository->updateExternalTeamMatchStats($match, $data);
        }
        return $match;
    }

    public function updateExternalTeamScore(array $data, MatchModel $match)
    {
        $homeTeam = $this->homeTeamMatch($match);

        $data['goalConceded'] = $match->externalTeam->goalConceded + $data['teamOwnGoal'];
        $data['teamScore'] = $data['goalScored'] + $homeTeam->pivot->teamOwnGoal;
        $homeTeamScore = $homeTeam->pivot->goalScored + $data['teamOwnGoal'];

        $match->teams()->updateExistingPivot($homeTeam->id, ['teamScore' => $homeTeamScore, 'goalConceded' => $data['goalScored']]);
        $match->coachMatchStats()->updateExistingPivot($homeTeam->id, ['teamScore' => $homeTeamScore, 'goalConceded' => $data['goalScored']]);
        $this->matchRepository->updateExternalTeamMatchStats($match, $data);
        return $match;
    }

    public function getPlayerStats(MatchModel $match, Player $player)
    {
        $data = $match->playerMatchStats()->find($player->id);
        $playerData = $data->user;
        $statsData = $data->pivot;
        return compact('playerData', 'statsData');
    }
    public function updatePlayerStats(array $data, MatchModel $match, Player $player)
    {
        $match->playerMatchStats()->updateExistingPivot($player->id, $data);
        $player->user->notify(new MatchStatsPlayer($match));
        return $match;
    }

    public function destroy(MatchModel $match)
    {
        $homeTeam = $match->homeTeam;
        $homeTeamParticipants = $this->userRepository->allTeamsParticipant($homeTeam);
        Notification::send($homeTeamParticipants, new  MatchSchedule($match, 'delete'));

        if ($match->matchType == 'Internal Match') {
            $awayTeam = $match->awayTeam;
            $awayTeamParticipants = $this->userRepository->allTeamsParticipant($awayTeam, admins: false);
            Notification::send($awayTeamParticipants, new  MatchSchedule($match, 'delete'));
        }
        return $match->delete();
    }
}
