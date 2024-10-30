<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;
use App\Models\PlayerSkillStats;
use App\Repository\PlayerPerformanceReviewRepository;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PlayerPerformanceReviewService extends Service
{
    private PlayerPerformanceReviewRepository $performanceReviewRepository;
    public function __construct(PlayerPerformanceReviewRepository $performanceReviewRepository)
    {
        $this->performanceReviewRepository = $performanceReviewRepository;
    }

    public function index(Player $player)
    {
        $data = $this->performanceReviewRepository->getByPlayer($player);
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($player){
                if ($item->event->eventType == 'Training'){
                    $route = route('training-schedule.show', ['schedule'=>$item->event->id]);
                    $btnText = 'View Training Detail';
                } elseif ($item->event->eventType == 'Match'){
                    $route = route('match-schedule.show', ['schedule'=>$item->event->id]);
                    $btnText = 'View Match Detail';
                }

                if (isAllAdmin()){
                    $button = '<a class="btn btn-sm btn-outline-secondary" href="' . $route . '" data-toggle="tooltip" data-placement="bottom" title="'.$btnText.'">
                                <span class="material-icons">visibility</span>
                           </a>';
                } elseif(isCoach()){
                    $button = '<div class="dropdown">
                                  <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="material-icons">
                                        more_vert
                                    </span>
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="' . $route . '"><span class="material-icons">visibility</span> '.$btnText.'</a>
                                        <a class="dropdown-item editPerformanceReview" id="'.$player->id.'"  data-reviewId="'.$item->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>
                                  </div>
                            </div>';
                }
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
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="' . route('player-managements.skill-stats', ['player'=>$item->id]) . '"><span class="material-icons">visibility</span> View All Player Performance Review</a>
                                            '.$reviewBtn.'
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
        return $this->performanceReviewRepository->create($data);
    }

    public function update(array $data, PlayerPerformanceReview $review)
    {
        return $review->update($data);
    }

    public function destroy(PlayerPerformanceReview $review)
    {
        return $review->delete();
    }
}
