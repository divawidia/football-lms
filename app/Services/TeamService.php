<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamMatch;
use App\Notifications\PlayerCoachAddToTeam;
use App\Notifications\PlayerCoachRemoveToTeam;
use App\Notifications\TeamsManagements\OpponentTeamUpdated;
use App\Notifications\TeamsManagements\TeamCreatedDeleted;
use App\Notifications\TeamsManagements\TeamUpdated;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TeamService extends Service
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;

    public function __construct(
        TeamRepository $teamRepository,
        UserRepository $userRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
    }
    public function indexDatatables($teamsData)
    {
        return Datatables::of($teamsData)
            ->addColumn('action', function ($item) {
                $actionButton = '';
                if (isCoach() || isPlayer()){
                    $actionButton =  '
                          <a class="btn btn-sm btn-outline-secondary" href="' . route('team-managements.show', $item->id) . '" data-toggle="tooltips" data-placement="bottom" title="View Team">
                            <span class="material-icons">
                                visibility
                            </span>
                          </a>';
                } elseif (isAllAdmin()){
                    if ($item->status == '1') {
                        $statusButton = '<form action="' . route('deactivate-team', $item->id) . '" method="POST">
                                            ' . method_field("PATCH") . '
                                            ' . csrf_field() . '
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">block</span> Deactivate Team
                                            </button>
                                        </form>';
                    } else {
                        $statusButton = '<form action="' . route('activate-team', $item->id) . '" method="POST">
                                            ' . method_field("PATCH") . '
                                            ' . csrf_field() . '
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">check_circle</span> Activate Team
                                            </button>
                                        </form>';
                    }
                    $actionButton =  '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('team-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Team</a>
                            <a class="dropdown-item" href="' . route('team-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Team</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete-team" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Team
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
                $teamName = '<a href="' . route('team-managements.show', $item->id) . '">
                                    <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teamName . '</strong></p>
                                </a>';
                return '
                        <div class="media flex-nowrap align-items-center"
                             style="white-space: nowrap;">
                            <div class="avatar avatar-sm mr-8pt">
                                <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->logo) . '" alt="profile-pic"/>
                            </div>
                            <div class="media-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex d-flex flex-column">
                                        '.$teamName.'
                                        <small class="js-lists-values-email text-50">'.$item->ageGroup.'</small>
                                    </div>
                                </div>
                            </div>
                        </div>';
            })
            ->editColumn('status', function ($item) {
                $badge = '';
                if ($item->status == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    $badge = '<span class="badge badge-pill badge-danger">Non-Active</span>';
                }
                return $badge;
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

    public function coachTeamsIndex($coach)
    {
        return $this->indexDatatables($coach->teams);
    }
    public function playerTeamsIndex($player)
    {
        return $this->indexDatatables($player->teams);
    }

    public function teamPlayers(Team $team){
        $query = $team->players()->get();

        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                $actionButton = '';
                if (isCoach()){
                    $actionButton =  '
                          <a class="btn btn-sm btn-outline-secondary" href="' . route('player-managements.show', $item->id) . '" data-toggle="tooltips" data-placement="bottom" title="View Player">
                            <span class="material-icons">
                                visibility
                            </span>
                          </a>';
                } elseif (isAllAdmin()){
                    $actionButton =  '
                                <div class="dropdown">
                                  <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="material-icons">
                                        more_vert
                                    </span>
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="' . route('player-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Player</a>
                                    <a class="dropdown-item" href="' . route('player-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Player</a>
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
                    $playerName = '<a href="' . route('player-managements.show', $item->id) . '">
                                        <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' ' . $item->user->lastName . '</strong></p>
                                    </a>';
                } else {
                    $playerName = '<p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' ' . $item->user->lastName . '</strong></p>';
                }
                return '<div class="media flex-nowrap align-items-center"
                             style="white-space: nowrap;">
                            <div class="avatar avatar-sm mr-8pt">
                                <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                            </div>
                            <div class="media-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex d-flex flex-column">
                                        '.$playerName.'
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>
                            </div>
                        </div>';
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
        $query = $team->coaches()->get();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                $actionButton = '';
                if (isCoach()){
                    $actionButton =  '
                        <a class="btn btn-sm btn-outline-secondary" href="' . route('coach-managements.show', $item->id) . '" data-toggle="tooltips" data-placement="bottom" title="View Player">
                            <span class="material-icons">
                                visibility
                            </span>
                        </a>';
                } elseif (isAllAdmin()){
                    $actionButton =  '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('coach-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Coach</a>
                            <a class="dropdown-item" href="' . route('coach-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Coach</a>
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
                return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' '.$item->user->lastName.'</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->specializations->name . ' - '.$item->certification->name.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('joinedDate', function ($item) {
                return date('l, M d, Y. h:i A', strtotime($item->pivot->created_at));
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
                    $actionButton =  '
                          <a class="btn btn-sm btn-outline-secondary" href="' . route('competition-managements.show', $item->competitionId) . '" data-toggle="tooltips" data-placement="bottom" title="View Competition">
                            <span class="material-icons">
                                visibility
                            </span>
                          </a>';
                } elseif (isAllAdmin()){
                    if ($item->status == '1') {
                        $statusButton = '<form action="' . route('deactivate-competition', $item->competitionId) . '" method="POST">
                                            ' . method_field("PATCH") . '
                                            ' . csrf_field() . '
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">block</span> Deactivate Competition
                                            </button>
                                        </form>';
                    } else {
                        $statusButton = '<form action="' . route('activate-competition', $item->competitionId) . '" method="POST">
                                            ' . method_field("PATCH") . '
                                            ' . csrf_field() . '
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">check_circle</span> Activate Competition
                                            </button>
                                        </form>';
                    }
                    $actionButton =  '
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . route('competition-managements.edit', $item->competitionId) . '"><span class="material-icons">edit</span> Edit Competition</a>
                                <a class="dropdown-item" href="' . route('competition-managements.show', $item->competitionId) . '"><span class="material-icons">visibility</span> View Competition</a>
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
                return '
                        <div class="media flex-nowrap align-items-center"
                             style="white-space: nowrap;">
                            <div class="avatar avatar-sm mr-8pt">
                                <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->competition->logo) . '" alt="profile-pic"/>
                            </div>
                            <div class="media-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex d-flex flex-column">
                                        <p class="mb-0"><strong class="js-lists-values-lead">' . $item->competition->name . '</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->competition->type . '</small>
                                    </div>
                                </div>
                            </div>
                        </div>';
            })
            ->editColumn('date', function ($item) {
                $startDate = $this->convertToDate($item->competition->startDate);
                $endDate = $this->convertToDate($item->competition->endDate);
                return $startDate.' - '.$endDate;
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
                $badge = '';
                if ($item->competition->status == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->competition->status == '0') {
                    $badge = '<span class="badge badge-pill badge-danger">Ended</span>';
                }
                return $badge;
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
        $matchPlayed = EventSchedule::whereHas('teams', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->where('status', '0')
            ->where('eventType', 'Match')
            ->count();
        $thisMonthMatchPlayed = EventSchedule::whereHas('teams', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->where('status', '0')
            ->where('eventType', 'Match')
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->count();

        $goals = TeamMatch::whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })->sum('teamScore');
        $thisMonthGoals = TeamMatch::whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->sum('teamScore');

        $goalsConceded = TeamMatch::whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Opponent Team');
                $q->where('teamId', $team->id);
            })
            ->sum('teamScore');
        $thisMonthGoalsConceded = TeamMatch::whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Opponent Team');
                $q->where('teamId', $team->id);
            })
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->sum('teamScore');

        $goalsDifference = $goals - $goalsConceded;
        $thisMonthGoalDifference = $thisMonthGoals - $thisMonthGoalsConceded;

        $cleanSheets = TeamMatch::whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->sum('cleanSheets');
        $thisMonthCleanSheets = TeamMatch::whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->sum('cleanSheets');

        $ownGoals = TeamMatch::whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->sum('teamOwnGoal');
        $thisMonthOwnGoals = TeamMatch::whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->sum('teamOwnGoal');

        $wins = TeamMatch::where('resultStatus', 'Win')
            ->whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })->count();
        $thisMonthWins = TeamMatch::where('resultStatus', 'Win')
            ->whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->count();

        $losses = TeamMatch::where('resultStatus', 'Lose')
            ->whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->count();
        $thisMonthLosses = TeamMatch::where('resultStatus', 'Lose')
            ->whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->count();

        $draws = TeamMatch::where('resultStatus', 'Draw')
            ->whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })->count();
        $thisMonthDraws = TeamMatch::where('resultStatus', 'Draw')
            ->whereHas('team', function($q) use ($team) {
                $q->where('teamSide', 'Academy Team');
                $q->where('teamId', $team->id);
            })
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->count();

        return compact(
            'matchPlayed',
            'thisMonthMatchPlayed',
            'goals',
            'thisMonthGoals',
            'goalsConceded',
            'thisMonthGoalsConceded',
            'goalsDifference',
            'thisMonthGoalDifference',
            'cleanSheets',
            'thisMonthCleanSheets',
            'ownGoals',
            'thisMonthOwnGoals',
            'wins',
            'thisMonthWins',
            'losses',
            'thisMonthLosses',
            'draws',
            'thisMonthDraws'
        );
    }

    public function teamLatestMatch(Team $team)
    {
        return EventSchedule::with('teams', 'competition')
            ->whereHas('teams', function($q) use ($team){
                $q->where('teamId', $team->id);
            })
            ->where('eventType', 'Match')
            ->where('status', '0')
            ->latest('date')
            ->take(2)
            ->get();
    }

    public function teamUpcomingMatch(Team $team)
    {
        return EventSchedule::with('teams', 'competition')
            ->whereHas('teams', function($q) use ($team){
                $q->where('teamId', $team->id);
            })
            ->where('eventType', 'Match')
            ->where('status', '1')
            ->latest('date')
            ->take(2)
            ->get();
    }

    public function teamUpcomingTraining(Team $team)
    {
        return EventSchedule::with('teams', 'competition')
            ->whereHas('teams', function($q) use ($team){
                $q->where('teamId', $team->id);
            })
            ->where('eventType', 'Training')
            ->where('status', '1')
            ->latest('date')
            ->take(2)
            ->get();
    }

    public function teamTrainingHistories(Team $team){
        $data = $team->schedules()
            ->where('eventType', 'Training')
            ->where('status', '0')
            ->latest('date')
            ->get();

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return '<a class="btn btn-sm btn-outline-secondary" href="' . route('training-schedules.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View training detail">
                            <span class="material-icons">visibility</span>
                        </a>';
            })
            ->editColumn('date', function ($item) {
                $date = date('M d, Y', strtotime($item->date));
                $startTime = date('h:i A', strtotime($item->startTime));
                $endTime = date('h:i A', strtotime($item->endTime));
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                $badge = '';
                if ($item->status == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    $badge = '<span class="badge badge-pill badge-danger">Ended</span>';
                }
                return $badge;
            })
            ->editColumn('note', function ($item) {
                if ($item->pivot->note == null) {
                    return 'No note added';
                } else {
                    return $item->pivot->note;
                }
            })
            ->editColumn('last_updated', function ($item) {
                return date('M d, Y ~ h:i A', strtotime($item->pivot->updated_at));
            })
            ->rawColumns(['action','date','status', 'last_updated', 'note'])
            ->addIndexColumn()
            ->make();
    }


    public function teamMatchHistories(Team $team){
        $data = $team->schedules()
            ->where('eventType', 'Match')
            ->where('status', '0')
            ->latest('date')
            ->get();

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return '<a class="btn btn-sm btn-outline-secondary" href="' . route('match-schedules.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View match detail">
                            <span class="material-icons">visibility</span>
                        </a>';
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
                $badge = '';
                if ($item->status == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    $badge = '<span class="badge badge-pill badge-danger">Ended</span>';
                }
                return $badge;
            })
            ->editColumn('teamScore', function ($item) {
                return $item->teams[0]->pivot->teamScore;
            })
            ->editColumn('opponentTeamScore', function ($item) {
                return $item->teams[1]->pivot->teamScore;
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
                return date('M d, Y ~ h:i A', strtotime($item->pivot->updated_at));
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
        Notification::send($players, new PlayerCoachAddToTeam($team));
        return $team;
    }

    public function updateCoachTeam(array $teamData, Team $team): Team
    {
        $team->coaches()->attach($teamData['coaches']);
        $coaches = $this->userRepository->getInArray('coach', $teamData['coaches']);
        Notification::send($coaches, new PlayerCoachAddToTeam($team));
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

    public function activate(Team $team, $loggedUser): Team
    {
        $team->update(['status' => '1']);

        $loggedAdminName = $this->getUserFullName($loggedUser);
        $admins = $this->userRepository->getAllAdminUsers();

        if ($team->teamSide == 'Academy Team'){
            $coaches = $this->teamsCoaches($team);
            $players = $this->teamsPlayers($team);
            Notification::send($coaches,new TeamUpdated($loggedAdminName, $team, 'activated'));
            Notification::send($players,new TeamUpdated($loggedAdminName, $team, 'activated'));
            Notification::send($admins,new TeamUpdated($loggedAdminName, $team, 'activated'));
        } elseif ($team->teamSide == 'Opponent Team'){
            Notification::send($admins,new OpponentTeamUpdated($loggedAdminName, $team, 'activated'));
        }

        return $team;
    }

    public function deactivate(Team $team, $loggedUser): Team
    {
        $team->update(['status' => '0']);

        $loggedAdminName = $this->getUserFullName($loggedUser);
        $admins = $this->userRepository->getAllAdminUsers();

        if ($team->teamSide == 'Academy Team'){
            $coaches = $this->teamsCoaches($team);
            $players = $this->teamsPlayers($team);
            Notification::send($coaches,new TeamUpdated($loggedAdminName, $team, 'deactivated'));
            Notification::send($players,new TeamUpdated($loggedAdminName, $team, 'deactivated'));
            Notification::send($admins,new TeamUpdated($loggedAdminName, $team, 'deactivated'));
        } elseif ($team->teamSide == 'Opponent Team'){
            Notification::send($admins,new OpponentTeamUpdated($loggedAdminName, $team, 'deactivated'));
        }
        return $team;
    }

    public function destroy(Team $team, $loggedUser): Team
    {
        $this->deleteImage($team->logo);
        $team->coaches()->detach();
        $team->players()->detach();
        $team->delete();

        $loggedAdminName = $this->getUserFullName($loggedUser);
        $admins = $this->userRepository->getAllAdminUsers();
        $coaches = $this->teamsCoaches($team);
        $players = $this->teamsPlayers($team);
        Notification::send($coaches,new TeamUpdated($loggedAdminName, $team, 'deleted'));
        Notification::send($players,new TeamUpdated($loggedAdminName, $team, 'deleted'));
        Notification::send($admins,new TeamUpdated($loggedAdminName, $team, 'deleted'));

        return $team;
    }
}
