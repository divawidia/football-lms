<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;
use App\Repository\Interface\TrainingRepositoryInterface;
use App\Repository\MatchRepository;
use App\Repository\PlayerPerformanceReviewRepository;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PlayerPerformanceReviewService extends Service
{
    private PlayerPerformanceReviewRepository $performanceReviewRepository;
    private MatchRepository $matchRepository;
    private TrainingRepositoryInterface $trainingRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(
        PlayerPerformanceReviewRepository $performanceReviewRepository,
        MatchRepository                   $matchRepository,
        TrainingRepositoryInterface         $trainingRepository,
        DatatablesHelper                  $datatablesHelper
    )
    {
        $this->performanceReviewRepository = $performanceReviewRepository;
        $this->matchRepository = $matchRepository;
        $this->trainingRepository = $trainingRepository;
        $this->datatablesHelper = $datatablesHelper;
    }

    public function index(Player $player): JsonResponse
    {
        $data = $this->performanceReviewRepository->getByPlayer($player);
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($player){
                $actionBtn = '-';
                if ($item->trainingId != null){
                    $route = route('training-schedules.show', ['schedule'=>$item->training->hash]);
                    $btnText = 'View Training Detail';
                    $actionBtn = $this->datatablesHelper->buttonTooltips($route, $btnText, 'visibility');
                } elseif ($item->matchId != null){
                    $route = route('match-schedules.show', ['schedule'=>$item->match->hash]);
                    $btnText = 'View Match Detail';
                    $actionBtn = $this->datatablesHelper->buttonTooltips($route, $btnText, 'visibility');
                }
                return $actionBtn;
            })
            ->editColumn('event', function ($item) {
                $event = "-";
                if ($item->trainingId != null) {
                    $event = $item->training->topic;
                } elseif ($item->matchId != null) {
                    $awayTeamName = ($item->match->matchType == 'Internal Match') ? $item->match->awayTeam->teamName : $item->match->externalTeam->teamName;
                    $event = "Match {$item->match->homeTeam->teamName} Vs. $awayTeamName";
                }
                return $event;
            })
            ->editColumn('performance_review', function ($item){
                return $item->performanceReview;
            })
            ->editColumn('performance_review_created', function ($item){
                return $this->convertToDatetime($item->created_at);
            })
            ->editColumn('performance_review_last_updated', function ($item){
                return $this->convertToDatetime($item->updated_at);
            })
            ->rawColumns(['action','event', 'performance_review',])
            ->addIndexColumn()
            ->make();
    }


    public function indexAllPlayerInEvent(MatchModel $match, $teamId = null)
    {
        $data = $match->players();
        if ($teamId) {
            $data->where('teamId', $teamId);
        }
        $data = $data->get();

        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($match){
                $review = $this->performanceReviewRepository->getByPlayer($item, $match)->first();
                if (isAllAdmin()){
                    $button = '<a class="btn btn-sm btn-outline-secondary" href="' . route('coach.player-managements.performance-reviews', ['player'=>$item->hash]) . '" data-toggle="tooltip" data-placement="bottom" title="View All Player Performance Review">
                                    <span class="material-icons">visibility</span>
                               </a>';
                } elseif(isCoach()){
                    if ($review){
                        $reviewBtn = '<a class="dropdown-item editPerformanceReview" id="'.$item->id.'" data-eventId="'.$match->id.'"  data-reviewId="'.$review->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>';
                    } else {
                        $reviewBtn = '<a class="dropdown-item addPerformanceReview" id="'.$item->id.'" data-eventId="'.$match->id.'"><span class="material-icons">add</span> Add Player Performance Review</a>';
                    }
                    $button = '<div class="dropdown">
                                      <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="' . route('player-managements.performance-reviews-page', ['player'=>$item->hash]) . '"><span class="material-icons">visibility</span> View All Player Performance Review</a>
                                            '.$reviewBtn.'
                                      </div>
                                </div>';
                }
                return $button;
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('performance_review', function ($item) use ($match){
                $review = $this->performanceReviewRepository->getByPlayer($item, $match)->first();
                if ($review){
                    $text = $review->performanceReview;
                } else{
                    $text = 'Performance review still not added yet';
                }
                return $text;
            })
            ->editColumn('performance_review_created', function ($item) use ($match){
                $review = $this->performanceReviewRepository->getByPlayer($item, $match)->first();
                if ($review){
                    $text = date('M d, Y h:i A', strtotime($review->created_at));
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->editColumn('performance_review_last_updated', function ($item) use ($match){
                $review = $this->performanceReviewRepository->getByPlayer($item, $match)->first();
                if ($review){
                    $text = date('M d, Y h:i A', strtotime($review->updated_at));
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->rawColumns(['action','name', 'performance_review', 'performance_review_created','performance_review_last_updated'])
            ->addIndexColumn()
            ->make();
    }

    public function store(array $data, Player $player, Coach $coach)
    {
        $data['playerId'] = $player->id;
        $data['coachId'] = $coach->id;
        $event = null;
        if (array_key_exists('eventId', $data)) {
            $event = $this->eventScheduleRepository->find($data['eventId']);
        }
        $review = $this->performanceReviewRepository->create($data);
        $player->user->notify(new \App\Notifications\PlayerPerformanceReview($coach, 'created', $event));
        return $review;
    }

    public function update(array $data, PlayerPerformanceReview $review)
    {
        $review->update($data);
        $review->player->user->notify(new \App\Notifications\PlayerPerformanceReview($review->coach, 'updated', $review->event));
        return $review;
    }

    public function destroy(PlayerPerformanceReview $review)
    {
        $review->player->user->notify(new \App\Notifications\PlayerPerformanceReview($review->coach, 'deleted', $review->event));
        return $review->delete();
    }
}
