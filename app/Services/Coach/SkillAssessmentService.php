<?php

namespace App\Services\Coach;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\PlayerSkillStats;
use App\Repository\PlayerRepository;
use App\Repository\PlayerSkillStatsRepository;
use App\Services\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SkillAssessmentService extends Service
{
    private PlayerRepository $playerRepository;
    private PlayerSkillStatsRepository $playerSkillStatsRepository;
    public function __construct(PlayerRepository $playerRepository, PlayerSkillStatsRepository $playerSkillStatsRepository)
    {
        $this->playerRepository = $playerRepository;
        $this->playerSkillStatsRepository = $playerSkillStatsRepository;
    }

    // retrieve player data based on coach managed teams
    public function index($coach): JsonResponse
    {
        $teams = $coach->teams;

        // query player data that included in teams that managed by logged in coach
        $query = $this->playerRepository->getPLayersByTeams($teams);

        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                return '
                      <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('skill-assessments.skill-stats', $item->id) . '"><span class="material-icons">edit</span> View Skill Player</a>
                            <a class="dropdown-item" href="' . route('player-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> Update Player Skill</a>
                            <button type="button" class="dropdown-item add-performance-review" id="' . $item->userId . '">
                                <span class="material-icons">add</span>
                                Add Performance Review
                            </button>
                          </div>
                        </div>';
            })
            ->editColumn('teams.name', function ($item) {
                $playerTeam = '';
                if (count($item->teams) === 0) {
                    $playerTeam = 'No Team';
                } else {
                    foreach ($item->teams as $team) {
                        $playerTeam .= '<span class="badge badge-pill badge-danger">' . $team->teamName . '</span>';
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
                                        <a href="' . route('skill-assessments.skill-stats', $item->id) . '">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' ' . $item->user->lastName . '</strong></p>
                                        </a>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->editColumn('age', function ($item) {
                return $this->getAge($item->user->dob);
            })
            ->editColumn('lastUpdated', function ($item) {
                $data = $item->playerSkillStats()->latest()->first();
                if ($data){
                    $date = $this->convertToDatetime($data->created_at);
                } else{
                    $date = "Haven't assessed yet";
                }
                return $date;
            })
            ->rawColumns(['action', 'name', 'lastUpdated', 'age', 'teams.name'])
            ->addIndexColumn()
            ->make();
    }

    public function indexAllPlayerInEvent(EventSchedule $schedule)
    {
        $data = $schedule->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($schedule){
                $stats = $this->playerSkillStatsRepository->getByPlayer($item, $schedule)->first();
                if (isAllAdmin()){
                    $button = '<a class="btn btn-sm btn-outline-secondary" href="' . route('player-managements.skill-stats', ['player'=>$item->id]) . '" data-toggle="tooltip" data-placement="bottom" title="View Player Skill Stats Detail">
                                    <span class="material-icons">visibility</span>
                               </a>';
                } elseif(isCoach()){
                    if (!$stats){
                        $statsBtn = '<a class="dropdown-item addSkills" id="'.$item->id.'" data-eventId="'.$schedule->id.'"><span class="material-icons">edit</span> Evaluate Player Skills Stats</a>';
                    } else {
                        $statsBtn = '<a class="dropdown-item editSkills" id="'.$item->id.'" data-eventId="'.$schedule->id.'" data-statsId="'.$stats->id.'"><span class="material-icons">edit</span> Edit Player Skills Stats</a>';
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
                $stats = $this->playerSkillStatsRepository->getByPlayer($item, $schedule)->first();
                if ($stats){
                    $date = 'Skill stats have been added';
                } else{
                    $date = 'Skill stats still not added yet';
                }
                return $date;
            })
            ->editColumn('stats_created', function ($item) use ($schedule){
                $stats = $this->playerSkillStatsRepository->getByPlayer($item, $schedule)->first();
                if ($stats){
                    $date = date('M d, Y h:i A', strtotime($stats->created_at));
                } else{
                    $date = '-';
                }
                return $date;
            })
            ->editColumn('stats_updated', function ($item) use ($schedule){
                $stats = $this->playerSkillStatsRepository->getByPlayer($item, $schedule)->first();
                if ($stats){
                    $date = date('M d, Y h:i A', strtotime($stats->updated_at));
                } else{
                    $date = '-';
                }
                return $date;
            })
            ->rawColumns(['action','name', 'stats_status', 'stats_created', 'stats_updated'])
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
        return PlayerSkillStats::create($data);
    }

    public function update(array $data, PlayerSkillStats $playerSkillStats, $coachId)
    {
        $data = $this->convertInputData($data);
        $data['coachId'] = $coachId->id;
        return $playerSkillStats->update($data);
    }

    public function destroy(PlayerSkillStats $playerSkillStats)
    {
        return $playerSkillStats->delete();
    }
}
