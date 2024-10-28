<?php

namespace App\Services;

use App\Models\Coach;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LeaderboardService extends Service
{
    private PlayerRepository $playerRepository;
    private TeamRepository $teamRepository;
    public function __construct(PlayerRepository $playerRepository, TeamRepository $teamRepository){
        $this->playerRepository = $playerRepository;
        $this->teamRepository = $teamRepository;
    }

    public function playerLeaderboardDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                    $btn ='<a class="btn btn-sm btn-outline-secondary" href="' . route('player-managements.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View player">
                            <span class="material-icons">visibility</span>
                        </a>';
                return $btn;
            })
            ->editColumn('teams', function ($item) {
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
                                        <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->addColumn('apps', function ($item){
                return $item->playerMatchStats()->where('minutesPlayed', '>', '0')->count();
            })
            ->addColumn('goals', function ($item){
                return $item->playerMatchStats()->sum('goals');
            })
            ->addColumn('assists', function ($item){
                return $item->playerMatchStats()->sum('assists');
            })
            ->addColumn('ownGoals', function ($item){
                return $item->playerMatchStats()->sum('ownGoal');
            })
            ->addColumn('shots', function ($item){
                return $item->playerMatchStats()->sum('shots');
            })
            ->addColumn('passes', function ($item){
                return $item->playerMatchStats()->sum('passes');
            })
            ->addColumn('fouls', function ($item){
                return $item->playerMatchStats()->sum('fouls');
            })
            ->addColumn('yellowCards', function ($item){
                return $item->playerMatchStats()->sum('yellowCards');
            })
            ->addColumn('redCards', function ($item){
                return $item->playerMatchStats()->sum('redCards');
            })
            ->addColumn('saves', function ($item){
                return $item->playerMatchStats()->sum('saves');
            })
            ->rawColumns([
                'action',
                'teams',
                'name',
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
    public function playerLeaderboard(){
        $query = $this->playerRepository->getAll();
        return $this->playerLeaderboardDatatables($query);
    }
    public function coachPLayerLeaderboard(Coach $coach)
    {
        $teams = $coach->teams()->get();
        $query = $this->playerRepository->getCoachsPLayers($teams);
        return $this->playerLeaderboardDatatables($query);
    }

    public function teamLeaderboardsDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                    $btn ='<a class="btn btn-sm btn-outline-secondary" href="' . route('team-managements.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View team">
                            <span class="material-icons">visibility</span>
                        </a>';

                return $btn;
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
                                        <p class="mb-0"><strong class="js-lists-values-lead">'. $item->teamName .'</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->ageGroup . '</small>
                                    </div>
                                </div>
                            </div>
                        </div>';
            })
            ->addColumn('match', function ($item){
                return $item->schedules()
                    ->where('status', '0')
                    ->where('eventType', 'Match')
                    ->count();
            })
            ->addColumn('won', function ($item){
                return $item->matches()->where('resultStatus', 'Win')->count();
            })
            ->addColumn('drawn', function ($item){
                return $item->matches()->where('resultStatus', 'Draw')->count();
            })
            ->addColumn('lost', function ($item){
                return $item->matches()->where('resultStatus', 'Lose')->count();
            })
            ->addColumn('goals', function ($item){
                return $item->matches()->sum('teamScore');
            })
            ->addColumn('goalsConceded', function ($item){
                return $item->matches()->sum('goalConceded');
            })
            ->addColumn('cleanSheets', function ($item){
                return $item->matches()->sum('cleanSheets');
            })
            ->addColumn('ownGoals', function ($item){
                return $item->matches()->sum('teamOwnGoal');
            })
            ->rawColumns([
                'action',
                'name',
                'match',
                'won',
                'drawn',
                'lost',
                'goals',
                'goalsConceded',
                'cleanSheets',
                'ownGoals',
            ])
            ->addIndexColumn()
            ->make();
    }
    public function teamLeaderboard()
    {
//        $query = Team::with('matches')->where('teamSide', 'Academy Team')->get();
        $query = $this->teamRepository->getAcademyTeams();
        return $this->teamLeaderboardsDatatables($query);
    }
    public function coachsTeamLeaderboards($coach)
    {
        $query = $coach->teams();
        return $this->teamLeaderboardsDatatables($query);
    }
}
