<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;
use App\Models\PlayerSkillStats;
use App\Repository\EventScheduleRepository;
use App\Repository\PlayerPerformanceReviewRepository;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PlayerPerformanceReviewService extends Service
{
    private PlayerPerformanceReviewRepository $performanceReviewRepository;
    private EventScheduleRepository $eventScheduleRepository;
    private DatatablesService $datatablesService;
    public function __construct(
        PlayerPerformanceReviewRepository $performanceReviewRepository,
        EventScheduleRepository $eventScheduleRepository,
        DatatablesService $datatablesService
    )
    {
        $this->performanceReviewRepository = $performanceReviewRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->datatablesService = $datatablesService;
    }

    public function index(Player $player)
    {
        $data = $this->performanceReviewRepository->getByPlayer($player);
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($player){
                if ($item->event->eventType == 'Training'){
                    $route = route('training-schedules.show', ['schedule'=>$item->event->id]);
                    $btnText = 'View Training Detail';
                } elseif ($item->event->eventType == 'Match'){
                    $route = route('match-schedules.show', ['schedule'=>$item->event->id]);
                    $btnText = 'View Match Detail';
                }

                    $button = '<a class="btn btn-sm btn-outline-secondary" href="' . $route . '" data-toggle="tooltip" data-placement="bottom" title="'.$btnText.'">
                                <span class="material-icons">visibility</span>
                           </a>';
                return $button;
            })
            ->editColumn('event', function ($item) {
                if ($item->event->eventType == 'Training'){
                    $text = $item->event->eventName;
                } elseif ($item->event->eventType == 'Match'){
                    $text = 'Match '.$item->event->teams[0]. ' Vs. '.$item->event->teams[1];
                }
                return $text;
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
            ->rawColumns(['action','event', 'performance_review', 'performance_review_created','performance_review_last_updated'])
            ->addIndexColumn()
            ->make();
    }


    public function indexAllPlayerInEvent(EventSchedule $schedule)
    {
        $data = $schedule->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($schedule){
                $review = $this->performanceReviewRepository->getByPlayer($item, $schedule)->first();
                if (isAllAdmin()){
                    $button = '<a class="btn btn-sm btn-outline-secondary" href="' . route('coach.player-managements.performance-reviews', ['player'=>$item->id]) . '" data-toggle="tooltip" data-placement="bottom" title="View All Player Performance Review">
                                    <span class="material-icons">visibility</span>
                               </a>';
                } elseif(isCoach()){
                    if ($review){
                        $reviewBtn = '<a class="dropdown-item editPerformanceReview" id="'.$item->id.'" data-eventId="'.$schedule->id.'"  data-reviewId="'.$review->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>';
                    } else {
                        $reviewBtn = '<a class="dropdown-item addPerformanceReview" id="'.$item->id.'" data-eventId="'.$schedule->id.'"><span class="material-icons">add</span> Add Player Performance Review</a>';
                    }
                    $button = '<div class="dropdown">
                                      <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="' . route('player-managements.performance-reviews-page', ['player'=>$item->id]) . '"><span class="material-icons">visibility</span> View All Player Performance Review</a>
                                            '.$reviewBtn.'
                                      </div>
                                </div>';
                }
                return $button;
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->id));
            })
            ->editColumn('performance_review', function ($item) use ($schedule){
                $review = $this->performanceReviewRepository->getByPlayer($item, $schedule)->first();
                if ($review){
                    $text = $review->performanceReview;
                } else{
                    $text = 'Performance review still not added yet';
                }
                return $text;
            })
            ->editColumn('performance_review_created', function ($item) use ($schedule){
                $review = $this->performanceReviewRepository->getByPlayer($item, $schedule)->first();
                if ($review){
                    $text = date('M d, Y h:i A', strtotime($review->created_at));
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->editColumn('performance_review_last_updated', function ($item) use ($schedule){
                $review = $this->performanceReviewRepository->getByPlayer($item, $schedule)->first();
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
