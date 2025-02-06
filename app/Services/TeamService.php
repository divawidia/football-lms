<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
use App\Notifications\TeamsManagements\Admin\AddCoachToTeamForAdminNotification;
use App\Notifications\TeamsManagements\Admin\AddPlayerToTeamForAdminNotification;
use App\Notifications\TeamsManagements\Admin\RemoveCoachFromTeamForAdminNotification;
use App\Notifications\TeamsManagements\Admin\RemovePlayerFromTeamForAdminNotification;
use App\Notifications\TeamsManagements\Admin\TeamActivatedNotification;
use App\Notifications\TeamsManagements\Admin\TeamCreatedNotification;
use App\Notifications\TeamsManagements\Admin\TeamDeactivatedNotification;
use App\Notifications\TeamsManagements\Admin\TeamDeletedNotification;
use App\Notifications\TeamsManagements\Admin\TeamUpdatedNotification;
use App\Notifications\TeamsManagements\PlayerCoach\AddToTeamForPlayerCoachNotification;
use App\Notifications\TeamsManagements\PlayerCoach\RemoveFromTeamForPlayerCoachNotification;
use App\Repository\CoachRepository;
use App\Repository\Interface\PlayerRepositoryInterface;
use App\Repository\Interface\TrainingRepositoryInterface;
use App\Repository\MatchRepository;
use App\Repository\TeamMatchRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class TeamService extends Service
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private MatchRepository $matchRepository;
    private TeamMatchRepository $teamMatchRepository;
    private DatatablesHelper $datatablesHelper;
    private CoachRepository $coachRepository;
    private PlayerRepositoryInterface $playerRepository;
    private TrainingRepositoryInterface $trainingRepository;
    private MatchService $matchService;

    public function __construct(
        TeamRepository      $teamRepository,
        UserRepository      $userRepository,
        MatchRepository     $matchRepository,
        TeamMatchRepository $teamMatchRepository,
        CoachRepository     $coachRepository,
        PlayerRepositoryInterface $playerRepository,
        TrainingRepositoryInterface $trainingRepository,
        DatatablesHelper    $datatablesHelper,
        MatchService       $matchService
    )
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->matchRepository = $matchRepository;
        $this->teamMatchRepository = $teamMatchRepository;
        $this->playerRepository = $playerRepository;
        $this->coachRepository = $coachRepository;
        $this->trainingRepository = $trainingRepository;
        $this->datatablesHelper = $datatablesHelper;
        $this->matchService = $matchService;
    }
    public function indexDatatables($teamsData): JsonResponse
    {
        return Datatables::of($teamsData)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('team-managements.show', $item->hash), icon: 'visibility', btnText: 'View team detail');
                if (isAllAdmin()) {
                    $dropdownItem .= $this->datatablesHelper->linkDropdownItem(route: route('team-managements.edit', $item->hash), icon: 'edit', btnText: 'Edit team profile');
                    ($item->status == '1')
                        ? $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setDeactivate', $item->hash, 'danger', icon: 'block', btnText: 'Deactivate Team')
                        : $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setActivate', $item->hash, 'success', icon: 'check_circle', btnText: 'Activate team');
                    $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete-team', $item->hash, iconColor: 'danger', icon: 'delete', btnText: 'Delete team');
                }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('players', function ($item) {
                return $item->players()->count().' Player(s)';
            })
            ->editColumn('coaches', function ($item) {
                return $item->coaches()->count().' Coach(es)';
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->logo, $item->teamName, $item->ageGroup, route('team-managements.show', $item->hash));
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->activeNonactiveStatus($item->status);
            })
            ->rawColumns(['action', 'name', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function index(): JsonResponse
    {
        $query = $this->teamRepository->getAll(['players', 'coaches']);
        return $this->indexDatatables($query);
    }

    public function allTeams($exceptTeamId = null): Collection|array
    {
        return $this->teamRepository->getAll(exceptTeamId:  $exceptTeamId);
    }

    public function coachTeamsIndex($coach): JsonResponse
    {
        return $this->indexDatatables($coach->teams);
    }
    public function playerTeamsIndex($player): JsonResponse
    {
        return $this->indexDatatables($player->teams);
    }

    public function teamPlayers(Team $team): JsonResponse
    {
        $query = $team->players;

        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if (!isPlayer()) {
                    $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('player-managements.show', $item->hash), icon: 'visibility', btnText: 'View player');
                    if (isAllAdmin()) {
                        $dropdownItem .= $this->datatablesHelper->linkDropdownItem(route: route('player-managements.edit', $item->hash), icon: 'edit', btnText: 'edit player');
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('remove-player', $item->hash, iconColor: 'danger', icon: 'delete', btnText: 'Remove Player From Team');
                    }
                    return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                        return $dropdownItem;
                    });
                }
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->editColumn('name', function ($item) {
                return (isAllAdmin() || isCoach())
                    ? $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash))
                    : $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name);
            })
            ->addColumn('minutesPlayed', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'minutesPlayed', team: $team);
            })
            ->addColumn('apps', function ($item) use ($team){
                return $this->playerRepository->countMatchPlayed($item, team: $team);
            })
            ->addColumn('goals', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'goals', team: $team);
            })
            ->addColumn('assists', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'assists', team: $team);
            })
            ->addColumn('ownGoals', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'ownGoal', team: $team);
            })
            ->addColumn('shots', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'shots', team: $team);
            })
            ->addColumn('passes', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'passes', team: $team);
            })
            ->addColumn('fouls', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'fouls', team: $team);
            })
            ->addColumn('yellowCards', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'yellowCards', team: $team);
            })
            ->addColumn('redCards', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'redCards', team: $team);
            })
            ->addColumn('saves', function ($item) use ($team){
                return $this->playerRepository->playerMatchStatsSum($item, 'saves', team: $team);
            })
            ->rawColumns(['action', 'name'])
            ->addIndexColumn()
            ->make();
    }

    public function teamCoaches(Team $team): JsonResponse
    {
        $query = $team->coaches;
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if (!isPlayer()) {
                    $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('coach-managements.show', $item->hash), icon: 'visibility', btnText: 'View coach');
                    if (isAllAdmin()) {
                        $dropdownItem .= $this->datatablesHelper->linkDropdownItem(route: route('coach-managements.edit', $item->hash), icon: 'edit', btnText: 'edit coach');
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('remove-coach', $item->hash, iconColor: 'danger', icon: 'delete', btnText: 'Remove coach From Team');
                    }
                    return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                        return $dropdownItem;
                    });
                }
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->editColumn('name', function ($item) {
                return (isAllAdmin() || isCoach())
                    ? $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->specialization->name. ' - '.$item->certification->name, route('coach-managements.show', $item->hash))
                    : $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->specialization->name. ' - '.$item->certification->name);
            })
            ->editColumn('joinedDate', function ($item) {
                return $this->datatablesHelper->convertToDatetime($item->pivot->created_at);
            })
            ->editColumn('gender', function ($item) {
                return $item->user->gender;
            })
            ->rawColumns(['action', 'name'])
            ->addIndexColumn()
            ->make();
    }

    public function teamScore(Team $team, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'goalScored');
    }
    public function cleanSheets(Team $team, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'cleanSheets');
    }
    public function teamOwnGoal(Team $team, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'teamOwnGoal');
    }
    public function goalsConceded(Team $team, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, stats: 'goalConceded');
    }
    public function goalsDifference(Team $team, $startDate = null, $endDate = null)
    {
        return $this->teamScore($team, $startDate, $endDate) - $this->goalsConceded($team, $startDate, $endDate);
    }
    public function matchPlayed(Team $team, $startDate = null, $endDate = null)
    {
        return $this->matchRepository->getByRelation($team, status: ['Completed'],startDate: $startDate, endDate:  $endDate)->count();
    }
    public function wins(Team $team, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, results: 'Win');
    }
    public function draws(Team $team, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, results: 'Draw');
    }
    public function losses(Team $team, $startDate = null, $endDate = null)
    {
        return $this->teamMatchRepository->getTeamsStats($team, $startDate, $endDate, results: 'Lose');
    }
    public function winRate(Team $team, $startDate = null, $endDate = null)
    {
        $totalMatch = $this->matchPlayed($team, $startDate, $endDate);
        $wins = $this->wins($team, $startDate, $endDate);

        ($totalMatch > 0) ? $winRate = ( $wins /$totalMatch) * 100 : $winRate = 0; // check if totalMatch is 0 then set win rate to 0
        return round($winRate, 2);
    }

    public function teamLatestMatch(Team $team)
    {
        return $this->matchRepository->getByRelation($team, status: ['Completed'], take: 2, orderDirection: 'desc');
    }

    public function teamUpcomingMatch(Team $team)
    {
        return $this->matchRepository->getByRelation($team, status:['Scheduled', 'Ongoing'], take: 2);
    }

    public function teamUpcomingTraining(Team $team)
    {
        return $this->trainingRepository->getByRelation($team, status:['Scheduled', 'Ongoing'], take: 2);
    }

    public function playersNotJoinTheTeam(Team $team)
    {
        return $this->playerRepository->getPlayerNotJoinSpecificTeam($team);
    }
    public function coachesNotJoinTheTeam(Team $team)
    {
        return $this->coachRepository->getCoachNotJoinSpecificTeam($team);
    }

    public function teamsAllParticipants(Team $team)
    {
        return $this->userRepository->allTeamsParticipant($team);
    }
    public function teamsCoachesAdmins(Team $team)
    {
        return $this->userRepository->allTeamsParticipant($team, players: false);
    }
    public function teamsPlayers(Team $team)
    {
        return $this->userRepository->allTeamsParticipant($team, admins: false, coaches: false);
    }
    public function teamsCoaches(Team $team)
    {
        return $this->userRepository->allTeamsParticipant($team, admins: false, players: false);
    }

    public function teamTrainingHistories(Team $team){
        $data = $this->trainingRepository->getByRelation($team, status:['Completed'], orderDirection: 'desc');

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('training-schedules.show', $item->hash), 'View training session', 'visibility');
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesHelper->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->editColumn('last_updated', function ($item) {
                return $this->datatablesHelper->convertToDatetime($item->updated_at);
            })
            ->rawColumns(['action','status'])
            ->addIndexColumn()
            ->make();
    }

    public function teamMatchHistories(Team $team): JsonResponse
    {
        $data = $this->matchRepository->getByRelation($team, ['homeTeam', 'awayTeam', 'externalTeam', 'competition'], status:['Completed'], orderDirection: 'desc');

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('match-schedules.show', $item->hash), 'View match session', 'visibility');
            })
            ->editColumn('homeTeam', function ($item) use ($team){
                return ($item->homeTeam) ? $this->datatablesHelper->name($item->homeTeam->logo, $item->homeTeam->teamName, $item->homeTeam->ageGroup, route('team-managements.show', $item->homeTeam->hash)) : 'No Team';
            })
            ->editColumn('awayTeam', function ($item) use ($team){
                if ($item->matchType == 'Internal Match' && $item->awayTeam) {
                    return $this->datatablesHelper->name($item->awayTeam->logo, $item->awayTeam->teamName, $item->awayTeam->ageGroup, route('team-managements.show', $item->awayTeam->hash));
                }
                else {
                    return $item->externalTeam->teamName;
                }
            })
            ->editColumn('competition', function ($item) {
                return ($item->competition) ? $this->datatablesHelper->name($item->competition->logo, $item->competition->teamName, $item->competition->type, route('competition-managements.show', $item->competition->hash))
                    : 'No Competition';
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesHelper->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->editColumn('teamScore', function ($item) use ($team) {
                $awayTeam = '';
                if ($item->matchType == 'Internal Match'  && $item->awayTeam) {
                    $awayTeam = $this->matchService->awayTeamMatch($item)->pivot->teamScore;
                }
                elseif ($item->externalTeam) {
                    $awayTeam = $item->externalTeam->teamScore;
                }
                return '<p class="mb-0"><strong class="js-lists-values-lead">' .$this->matchService->homeTeamMatch($item)->pivot->teamScore . ' - ' . $awayTeam.'</strong></p>';
            })
            ->editColumn('note', function ($item) {
                return ($item->pivot->note == null) ? 'No note added' : $item->pivot->note;
            })
            ->editColumn('last_updated', function ($item) {
                return $this->convertToDatetime($item->pivot->updated_at);
            })
            ->rawColumns(['action', 'competition', 'homeTeam','awayTeam','status', 'teamScore',])
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

        Notification::send($this->userRepository->getAllAdminUsers(),new TeamCreatedNotification($loggedUser, $team));

        if (array_key_exists('players', $teamData)){
            $this->updatePlayerTeam($teamData, $team, $loggedUser);
        }
        if (array_key_exists('coaches', $teamData)){
            $this->updateCoachTeam($teamData, $team, $loggedUser);
        }
        return $team;
    }

    public function update(array $teamData, Team $team, $loggedUser)
    {
        $teamData['logo'] = $this->updateImage($teamData, 'logo', 'team-logo', $team->logo);

        Notification::send($this->userRepository->getAllAdminUsers(),new TeamUpdatedNotification($loggedUser, $team));

        return $team->update($teamData);
    }

    public function updatePlayerTeam(array $teamData, Team $team, $loggedUser): Team
    {
        $team->players()->attach($teamData['players']);
        $players = $this->userRepository->getInArray('player', $teamData['players']);
        $coachAdmin = $this->userRepository->allTeamsParticipant($team, players: false);

        Notification::send($players, new AddToTeamForPlayerCoachNotification($team));
        Notification::send($coachAdmin, new AddPlayerToTeamForAdminNotification($loggedUser, $team));

        return $team;
    }

    public function updateCoachTeam(array $teamData, Team $team, $loggedUser): Team
    {
        $team->coaches()->attach($teamData['coaches']);
        $coaches = $this->userRepository->getInArray('coach', $teamData['coaches']);
        $coachAdmin = $this->userRepository->allTeamsParticipant($team, players: false);


        Notification::send($coaches, new AddToTeamForPlayerCoachNotification($team));
        Notification::send($coachAdmin, new AddCoachToTeamForAdminNotification($loggedUser, $team));

        return $team;
    }

    public function removePlayer(Team $team, Player $player, $loggedUser): Team
    {
        $team->players()->detach($player);
        $teamsCoachAdmin = $this->userRepository->allTeamsParticipant($team, players: false);

        $player->user->notify(new RemoveFromTeamForPlayerCoachNotification($team));
        Notification::send($teamsCoachAdmin, new RemovePlayerFromTeamForAdminNotification($loggedUser, $team, $player));

        return $team;
    }

    public function removeCoach(Team $team, Coach $coach, $loggedUser): Team
    {
        $team->coaches()->detach($coach);
        $teamsCoachAdmin = $this->userRepository->allTeamsParticipant($team, players: false);

        $coach->user->notify(new RemoveFromTeamForPlayerCoachNotification($team));
        Notification::send($teamsCoachAdmin, new RemoveCoachFromTeamForAdminNotification($loggedUser, $team, $coach));

        return $team;
    }

    public function setStatus(Team $team, $status, $loggedUser)
    {
        ($status == '1')
            ? Notification::send($this->userRepository->getAllAdminUsers(), new TeamActivatedNotification($loggedUser, $team))
            : Notification::send($this->userRepository->getAllAdminUsers(), new TeamDeactivatedNotification($loggedUser, $team));

        return $team->update(['status' => $status]);
    }

    public function destroy(Team $team, $loggedUser)
    {
        $this->deleteImage($team->logo);

        Notification::send($this->teamsCoaches($team),new RemoveFromTeamForPlayerCoachNotification($team));
        Notification::send($this->teamsPlayers($team),new RemoveFromTeamForPlayerCoachNotification($team));
        Notification::send($this->userRepository->getAllAdminUsers(), new TeamDeletedNotification($loggedUser, $team));

        return $team->delete();
    }
}
