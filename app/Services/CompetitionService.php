<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\Competition;
use App\Models\GroupDivision;
use App\Models\Player;
use App\Models\Team;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\CompetitionRepository;
use App\Repository\EventScheduleRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CompetitionService extends Service
{
    private CompetitionRepository $competitionRepository;
    public function __construct(CompetitionRepository $competitionRepository){
        $this->competitionRepository = $competitionRepository;
    }
    public function index(){
        return $this->competitionRepository->getAll();
    }
    public function getActiveCompetition()
    {
        return $this->competitionRepository->getAll(status: '1');
    }

    public function modelTeamsCompetition($model, $status = null){
        $teams = $model->teams;
        if ($status){
            $data = $this->competitionRepository->getAll($teams, $status);
        }else{
            $data = $this->competitionRepository->getAll($teams);
        }
        return $data;
    }
    public function datatables(): JsonResponse
    {
        $query = $this->index();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-competition', $item->id) . '" method="POST">
                                                    ' . method_field("PATCH") . '
                                                    ' . csrf_field() . '
                                                    <button type="submit" class="dropdown-item">
                                                        <span class="material-icons">block</span> Deactivate Competition
                                                    </button>
                                                </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-competition', $item->id) . '" method="POST">
                                                    ' . method_field("PATCH") . '
                                                    ' . csrf_field() . '
                                                    <button type="submit" class="dropdown-item">
                                                        <span class="material-icons">check_circle</span> Activate Competition
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
                                <a class="dropdown-item" href="' . route('competition-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Competition</a>
                                <a class="dropdown-item" href="' . route('competition-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Competition</a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                    <span class="material-icons">delete</span> Delete Competition
                                </button>
                              </div>
                            </div>';
            })
            ->editColumn('divisions', function ($item) {
                $divisions = '';
                if (count($item->groups) == 0){
                    $divisions = 'No division added in this competition';
                }else{
                    foreach ($item->groups as $group) {
                        $divisions .= '<span class="badge badge-pill badge-danger">'.$group->groupName.'</span>';
                    }
                }
                return $divisions;
            })
            ->editColumn('teams', function ($item) {
                $teams = '';
                foreach ($item->groups as $group){
                    if (count($group->teams) > 0) {
                        $team = $group->teams->where('teamSide', 'Academy Team')->first();
                        $teams .= '<span class="badge badge-pill badge-danger">' . $team->teamName . '</span>';
                    }
                }
                return $teams;
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
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->name . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->type . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('date', function ($item) {
                $startDate = date('M d, Y', strtotime($item->startDate));
                $endDate = date('M d, Y', strtotime($item->endDate));
                return $startDate.' - '.$endDate;
            })
            ->editColumn('contact', function ($item) {
                if ($item->contactName != null && $item->contactPhone != null){
                    $contact = $item->contactName. ' ~ '.$item->contactPhone;
                }else{
                    $contact = 'No cantact added';
                }
                return $contact;
            })
            ->editColumn('status', function ($item) {
                $status = '';
                if ($item->status == '1') {
                    $status = '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    $status = '<span class="badge badge-pill badge-danger">Ended</span>';
                }
                return $status;
            })
            ->rawColumns(['action', 'name', 'teams', 'divisions', 'date', 'contact', 'status'])
            ->make();
    }

    public function overviewStats(Competition $competition)
    {
        $groupDivisions = $competition->groups;
        $totalGroups = $groupDivisions->count();
        $totalMatch = $competition->matches()->count();
        $totalTeams = 0;
        $ourTeamsWins = 0;
        $ourTeamsDraws = 0;
        $ourTeamsLosses = 0;
        foreach ($groupDivisions as $group){
            $totalTeams += $group->teams()->count();

            $ourTeamsWins += $group->teams()->where('teamSide', 'Academy Team')->sum('competition_team.won');
            $ourTeamsDraws += $group->teams()->where('teamSide', 'Academy Team')->sum('competition_team.drawn');
            $ourTeamsLosses += $group->teams()->where('teamSide', 'Academy Team')->sum('competition_team.lost');
        }

        return compact('totalTeams', 'ourTeamsWins', 'ourTeamsDraws', 'ourTeamsLosses', 'totalGroups', 'totalMatch');

    }
    public function competitionMatches(Competition $competition)
    {
        $data = $competition->matches;
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if (isAllAdmin()){
                    $actionBtn ='
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('match-schedules.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Match</a>
                            <a class="dropdown-item" href="' . route('match-schedules.show', $item->id) . '"><span class="material-icons">visibility</span> View Match</a>
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Match
                            </button>
                          </div>
                        </div>';
                } elseif (isCoach() || isPlayer()){
                    $actionBtn = '<a class="btn btn-sm btn-outline-secondary" href="' . route('match-schedules.show', $item->id) . '" data-toggle="tooltips" data-placement="bottom" title="View Match Detail">
                                        <span class="material-icons">visibility</span>
                                  </a>';
                }
                return $actionBtn;
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
            ->editColumn('score', function ($item){
                return '<p class="mb-0"><strong class="js-lists-values-lead">' .$item->teams[0]->pivot->teamScore . ' - ' . $item->teams[1]->pivot->teamScore.'</strong></p>';
            })
            ->editColumn('date', function ($item) {
                $date = $this->convertToDate($item->date);
                $startTime = $this->convertToTime($item->startTime);
                $endTime = $this->convertToTime($item->endTime);
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                $status = '';
                if ($item->status == '1') {
                    $status = '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    $status = '<span class="badge badge-pill badge-danger">Ended</span>';
                }
                return $status;
            })
            ->rawColumns(['action','team', 'score', 'status','opponentTeam','date'])
            ->make();
    }
    public  function store(array $competitionData){

        if (array_key_exists('logo', $competitionData)){
            $competitionData['logo'] =$competitionData['logo']->store('assets/competition-logo', 'public');
        }else{
            $competitionData['logo'] = 'images/undefined-user.png';
        }
        $competitionData['status'] = '1';

        $competition = Competition::create($competitionData);

        $division = GroupDivision::create([
            'groupName' => $competitionData['groupName'],
            'competitionId' => $competition->id
        ]);

        if (array_key_exists('opponentTeams', $competitionData)){
            $division->teams()->attach($competitionData['opponentTeams']);
        }
        if (array_key_exists('teams', $competitionData)){
            $division->teams()->attach($competitionData['teams']);
        }
        return $competition;
    }

    public function update(array $competitionData, Competition $competition): Competition
    {
        if (array_key_exists('logo', $competitionData)){
            $this->deleteImage($competitionData['logo']);
            $competitionData['logo'] = $competitionData['logo']->store('assets/competition-logo', 'public');
        }else{
            $competitionData['logo'] = $competition->logo;
        }

        $competition->update($competitionData);
        return $competition;
    }

    public function activate(Competition $competition): Competition
    {
        $competition->update(['status' => '1']);
        return $competition;
    }

    public function deactivate(Competition $competition): Competition
    {
        $competition->update(['status' => '0']);
        return $competition;
    }

    public function getTeams(){
        return Team::where('teamSide', 'Academy Team')->get();
    }

    public function getOpponentTeams(){
        return Team::where('teamSide', 'Opponent Team')->get();
    }

    public function destroy(Competition $competition): Competition
    {
        $this->deleteImage($competition->logo);
        $competition->delete();
        return $competition;
    }
}
