<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;
use App\Models\Training;
use App\Notifications\PerformanceReview\ReviewCreatedInMatchNotification;
use App\Notifications\PerformanceReview\ReviewCreatedInTrainingNotification;
use App\Notifications\PerformanceReview\ReviewDeletedInMatchNotification;
use App\Notifications\PerformanceReview\ReviewDeletedInTrainingNotification;
use App\Notifications\PerformanceReview\ReviewUpdatedInMatchNotification;
use App\Notifications\PerformanceReview\ReviewUpdatedInTrainingNotification;
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
                if ($item->training != null){
                    $route = route('training-schedules.show', ['training'=>$item->training->hash]);
                    $btnText = 'View Training session';
                    $actionBtn = $this->datatablesHelper->buttonTooltips($route, $btnText, 'visibility');
                } elseif ($item->match != null){
                    $route = route('match-schedules.show', ['match'=>$item->match->hash]);
                    $btnText = 'View Match session';
                    $actionBtn = $this->datatablesHelper->buttonTooltips($route, $btnText, 'visibility');
                }
                return $actionBtn;
            })
            ->editColumn('event', function ($item) {
                $event = "Not in event";
                if ($item->training != null) {
                    $event = "{$item->training->topic} Training";
                } elseif ($item->match != null) {
                    $awayTeamName = ($item->match->matchType == 'Internal Match') ? $item->match->awayTeam->teamName : $item->match->externalTeam->teamName;
                    $event = "{$item->match->homeTeam->teamName} Vs. {$awayTeamName} Match";
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
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make();
    }


    public function indexAllPlayerInMatch(MatchModel $match, $teamId = null)
    {
        $data = $match->players();
        if ($teamId) {
            $data->where('teamId', $teamId);
        }
        $data = $data->get();

        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($match){
                $review = $this->getPlayerPerformanceMatch($item, $match);
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('player-managements.performance-reviews.index-page', $item->hash), icon: 'visibility', btnText: 'View All Player Performance Review');
                if (isCoach() && $match->status == 'Ongoing' || isCoach() && $match->status == 'Completed') {
                    ($review)
                        ? $dropdownItem .= '<a class="dropdown-item editPerformanceReview" id="'.$item->hash.'" data-trainingId="'.null.'" data-matchId="'.$match->id.'" data-statsId="'.$review->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>'
                        : $dropdownItem .= '<a class="dropdown-item addPerformanceReview" id="'.$item->hash.'" data-trainingId="'.null.'" data-matchId="'.$match->id.'"><span class="material-icons">add</span> Add Player Performance Review</a>';
                }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('performance_review', function ($item) use ($match){
                $review = $this->getPlayerPerformanceMatch($item, $match);
                return ($review) ? $review->performanceReview : 'Performance review still not added yet';
            })
            ->editColumn('performance_review_created', function ($item) use ($match){
                $review = $this->getPlayerPerformanceMatch($item, $match);
                return ($review) ? $this->convertToDatetime($item->created_at) : '-';
            })
            ->editColumn('performance_review_last_updated', function ($item) use ($match){
                $review = $this->getPlayerPerformanceMatch($item, $match);
                return ($review) ? $this->convertToDatetime($item->updated_at) : '-';
            })
            ->rawColumns(['action','name'])
            ->addIndexColumn()
            ->make();
    }
    private function getPlayerPerformanceMatch(Player $player, MatchModel $match)
    {
        return $this->performanceReviewRepository->getByPlayer($player, match:  $match)->first();
    }
    private function getPlayerPerformanceTraining(Player $player, Training $training)
    {
        return $this->performanceReviewRepository->getByPlayer($player, training:  $training)->first();
    }
    public function indexAllPlayerInTraining(Training $training): JsonResponse
    {
        return Datatables::of($training->players)
            ->addColumn('action', function ($item) use ($training){
                $review = $this->getPlayerPerformanceTraining($item, $training);
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('player-managements.performance-reviews.index-page', $item->hash), icon: 'visibility', btnText: 'View All Player Performance Review');
                if (isCoach() && $training->status == 'Ongoing' || isCoach() && $training->status == 'Completed') {
                    ($review)
                        ? $dropdownItem .= '<a class="dropdown-item editPerformanceReview" id="'.$item->hash.'" data-trainingId="'.$training->id.'" data-matchId="'.null.'" data-reviewId="'.$review->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>'
                        : $dropdownItem .= '<a class="dropdown-item addPerformanceReview" id="'.$item->hash.'" data-trainingId="'.$training->id.'" data-matchId="'.null.'"><span class="material-icons">add</span> Add Player Performance Review</a>';
                }
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->editColumn('performance_review', function ($item) use ($training){
                $review = $this->getPlayerPerformanceTraining($item, $training);
                return ($review) ? $review->performanceReview : 'Performance review still not added yet';
            })
            ->editColumn('performance_review_created', function ($item) use ($training){
                $review = $this->getPlayerPerformanceTraining($item, $training);
                return ($review) ? $this->convertToDatetime($item->created_at) : '-';
            })
            ->editColumn('performance_review_last_updated', function ($item) use ($training){
                $review = $this->getPlayerPerformanceTraining($item, $training);
                return ($review) ? $this->convertToDatetime($item->updated_at) : '-';
            })
            ->rawColumns(['action','name'])
            ->addIndexColumn()
            ->make();
    }

    public function store(array $data, Player $player, Coach $coach)
    {
        $data['coachId'] = $coach->id;
        if ($data['matchId'] != null) {
            $event = $this->matchRepository->find($data['matchId']);
            $player->user->notify(new ReviewCreatedInMatchNotification($coach, $event));
        } elseif ($data['trainingId'] != null) {
            $event = $this->trainingRepository->find($data['trainingId']);
            $player->user->notify(new ReviewCreatedInTrainingNotification($coach, $event));
        }
        return $player->playerPerformanceReview()->create($data);
    }

    public function update(array $data, PlayerPerformanceReview $review, Coach $coach)
    {
        if ($review->training != null) {
            $review->player->user->notify(new ReviewUpdatedInTrainingNotification($coach, $review->training));
        } elseif ($review->match != null) {
            $review->player->user->notify(new ReviewUpdatedInMatchNotification($coach, $review->match));
        }
        return $review->update($data);
    }

    public function destroy(PlayerPerformanceReview $review, Coach $coach)
    {
        if ($review->training != null) {
            $review->player->user->notify(new ReviewDeletedInTrainingNotification($coach, $review->training));
        } elseif ($review->match != null) {
            $review->player->user->notify(new ReviewDeletedInMatchNotification($coach, $review->match));
        }
        return $review->delete();
    }
}
