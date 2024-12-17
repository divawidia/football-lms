<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\Player;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Yajra\DataTables\Facades\DataTables;

class LeaderboardService extends Service
{
    private PlayerRepository $playerRepository;
    private TeamRepository $teamRepository;
    private DatatablesHelper $datatablesService;
    public function __construct(PlayerRepository $playerRepository, TeamRepository $teamRepository, DatatablesHelper $datatablesService){
        $this->playerRepository = $playerRepository;
        $this->teamRepository = $teamRepository;
        $this->datatablesService = $datatablesService;
    }

    public function playerLeaderboardDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                    $btn ='<a class="btn btn-sm btn-outline-secondary" href="' . route('player-managements.show', $item->hash) . '" data-toggle="tooltip" data-placement="bottom" title="View player">
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
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
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
        $query = $this->playerRepository->getPLayersByTeams($teams);
        return $this->playerLeaderboardDatatables($query);
    }
    public function playersTeammateLeaderboard(Player $player)
    {
        $teams = $player->teams;
        $query = $this->playerRepository->getPLayersByTeams($teams);
        return $this->playerLeaderboardDatatables($query);
    }

    public function teamLeaderboardsDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                    $btn ='<a class="btn btn-sm btn-outline-secondary" href="' . route('team-managements.show', $item->hash) . '" data-toggle="tooltip" data-placement="bottom" title="View team">
                            <span class="material-icons">visibility</span>
                        </a>';

                return $btn;
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->logo, $item->teamName, $item->ageGroup, route('team-managements.show', $item->hash));
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
        $query = $this->teamRepository->getByTeamside('Academy Team');
        return $this->teamLeaderboardsDatatables($query);
    }
    public function modelsTeamsLeaderboards($model)
    {
        $query = $model->teams;
        return $this->teamLeaderboardsDatatables($query);
    }
}
