<?php

namespace App\Services\Coach;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerSkillStats;
use App\Models\Training;
use App\Notifications\SkillAssessment\PlayerAssessedInMatchNotification;
use App\Notifications\SkillAssessment\PlayerAssessedInTrainingNotification;
use App\Notifications\SkillAssessment\PlayerAssessedNotification;
use App\Notifications\SkillAssessment\SkillStatsDeletedNotification;
use App\Notifications\SkillAssessment\SkillStatsUpdatedNotification;
use App\Repository\Interface\TrainingRepositoryInterface;
use App\Repository\MatchRepository;
use App\Repository\PlayerRepository;
use App\Repository\PlayerSkillStatsRepository;
use App\Services\Service;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class SkillAssessmentService extends Service
{
    private PlayerRepository $playerRepository;
    private PlayerSkillStatsRepository $playerSkillStatsRepository;
    private MatchRepository $matchRepository;
    private TrainingRepositoryInterface $trainingRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(
        PlayerRepository           $playerRepository,
        PlayerSkillStatsRepository $playerSkillStatsRepository,
        MatchRepository            $matchRepository,
        TrainingRepositoryInterface $trainingRepository,
        DatatablesHelper           $datatablesHelper)
    {
        $this->playerRepository = $playerRepository;
        $this->playerSkillStatsRepository = $playerSkillStatsRepository;
        $this->matchRepository = $matchRepository;
        $this->trainingRepository = $trainingRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    // retrieve player data based on coach managed teams
    public function index($coach): JsonResponse
    {
        $teams = $coach->teams;

        // query player data that included in teams that managed by logged in coach
        $query = $this->playerRepository->getAll($teams);

        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('player-managements.skill-stats', $item->hash), icon: 'visibility', btnText: 'View Player Skill Stats');
                if (isCoach()) {
                    $dropdownItem .= '<a class="dropdown-item addSkills" id="'.$item->hash.'" data-trainingId="'.null.'" data-matchId="'.null.'"><span class="material-icons">edit</span> Evaluate Player Skills Stats</a>';
                    $dropdownItem .= '<a class="dropdown-item addPerformanceReview" id="'.$item->hash.'" data-trainingId="'.null.'" data-matchId="'.null.'"><span class="material-icons">edit</span> Evaluate Player Performance Review</a>';
               }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('teams.name', function ($item) {
                return $this->datatablesHelper->usersTeams($item);
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('skill-assessments.skill-stats', $item->hash));
            })
            ->editColumn('age', function ($item) {
                return $this->getAge($item->user->dob);
            })
            ->editColumn('lastUpdated', function ($item) {
                $data = $item->playerSkillStats()->latest()->first();
                return ($data) ? $this->convertToDatetime($data->created_at) : "Haven't assessed yet";
            })
            ->rawColumns(['action', 'name', 'teams.name'])
            ->addIndexColumn()
            ->make();
    }

    public function indexAllPlayerInMatch(MatchModel $match, $teamId = null)
    {
        $data = ($teamId) ? $match->players()->where('teamId', $teamId)->get() : $match->players;

        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($match){
                $stats = $this->getPlayerSkillStatsMatch($item, $match);
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('player-managements.skill-stats', $item->hash), icon: 'visibility', btnText: 'View Player Skill Stats');
                if (isCoach() && $match->status == 'Ongoing' || isCoach() && $match->status == 'Completed') {
                    (!$stats)
                        ? $dropdownItem .= '<a class="dropdown-item addSkills" id="'.$item->hash.'" data-trainingId="'.null.'" data-matchId="'.$match->id.'"><span class="material-icons">edit</span> Evaluate Player Skills Stats</a>'
                        : $dropdownItem .= '<a class="dropdown-item editSkills" id="'.$item->hash.'" data-trainingId="'.null.'" data-matchId="'.$match->id.'" data-statsId="'.$stats->id.'"><span class="material-icons">edit</span> Edit Player Skills Stats</a>';
                }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('stats_status', function ($item) use ($match){
                $stats = $this->getPlayerSkillStatsMatch($item, $match);
                return ($stats) ? 'Skill stats have been added' : 'Skill stats still not added yet';
            })
            ->editColumn('stats_created', function ($item) use ($match){
                $stats = $this->getPlayerSkillStatsMatch($item, $match);
                return ($stats) ? $this->convertToDatetime($stats->created_at): '-';
            })
            ->editColumn('stats_updated', function ($item) use ($match){
                $stats = $this->getPlayerSkillStatsMatch($item, $match);
                return ($stats) ? $this->convertToDatetime($stats->updated_at): '-';
            })
            ->rawColumns(['action','name'])
            ->addIndexColumn()
            ->make();
    }
    private function getPlayerSkillStatsMatch(Player $player, MatchModel $match)
    {
        return $this->playerSkillStatsRepository->getByPlayer($player, match:  $match)->first();
    }
    private function getPlayerSkillStatsTraining(Player $player, Training $training)
    {
        return $this->playerSkillStatsRepository->getByPlayer($player, training:  $training)->first();
    }
    public function indexAllPlayerInTraining(Training $training)
    {
        return Datatables::of($training->players)
            ->addColumn('action', function ($item) use ($training){
                $stats = $this->getPlayerSkillStatsTraining($item, $training);
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('player-managements.skill-stats', $item->hash), icon: 'visibility', btnText: 'View Player Skill Stats');
                if (isCoach() && $training->status == 'Ongoing' || isCoach() && $training->status == 'Completed') {
                    (!$stats)
                        ? $dropdownItem .= '<a class="dropdown-item addSkills" id="'.$item->hash.'" data-trainingId="'.$training->id.'" data-matchId="'.null.'"><span class="material-icons">edit</span> Evaluate Player Skills Stats</a>'
                        : $dropdownItem .= '<a class="dropdown-item editSkills" id="'.$item->hash.'" data-trainingId="'.$training->id.'" data-matchId="'.null.'" data-statsId="'.$stats->id.'"><span class="material-icons">edit</span> Edit Player Skills Stats</a>';
                }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('stats_status', function ($item) use ($training){
                $stats = $this->getPlayerSkillStatsTraining($item, $training);
                return ($stats) ? 'Skill stats have been added' : 'Skill stats still not added yet';
            })
            ->editColumn('stats_created', function ($item) use ($training){
                $stats = $this->getPlayerSkillStatsTraining($item, $training);
                return ($stats) ? $this->convertToDatetime($stats->created_at): '-';
            })
            ->editColumn('stats_updated', function ($item) use ($training){
                $stats = $this->getPlayerSkillStatsTraining($item, $training);
                return ($stats) ? $this->convertToDatetime($stats->updated_at): '-';
            })
            ->rawColumns(['action','name'])
            ->addIndexColumn()
            ->make();
    }

    public function convertInputData(array $data)
    {
        $conversionMap = [
            'Poor' => 0,
            'Needs Work' => 25,
            'Average Fair' => 50,
            'Good' => 75,
            'Excellent' => 100,
            0 => 'Poor',
            25 => 'Needs Work',
            50 => 'Average Fair',
            75 => 'Good',
            100 => 'Excellent',
        ];

        foreach ($data as $key => $value){
            if (array_key_exists($value, $conversionMap)) {
                $data[$key] = $conversionMap[$value];
            }
        }
        return $data;
    }

    public function store(array $data, Player $player, Coach $coach)
    {
        $data = $this->convertInputData($data);
        $data['playerId'] = $player->id;
        $data['coachId'] = $coach->id;

        if ($data['trainingId'] != null) {
            $event = $this->trainingRepository->find($data['trainingId']);
            $player->user->notify(new PlayerAssessedInTrainingNotification($coach, $event));
        } elseif ($data['matchId'] != null) {
            $event = $this->matchRepository->find($data['matchId']);
            $player->user->notify(new PlayerAssessedInMatchNotification($coach, $event));
        } else {
            $player->user->notify(new PlayerAssessedNotification($coach));
        }
        return PlayerSkillStats::create($data);
    }

    public function update(array $data, PlayerSkillStats $playerSkillStats, Coach $coach)
    {
        $data = $this->convertInputData($data);
        $data['coachId'] = $coach->id;

        $playerSkillStats->player->user->notify(new SkillStatsUpdatedNotification($coach));

        return $playerSkillStats->update($data);
    }

    public function destroy(PlayerSkillStats $playerSkillStats, Coach $coach)
    {
        $playerSkillStats->player->user->notify(new SkillStatsDeletedNotification($coach));
        return $playerSkillStats->delete();
    }
}
