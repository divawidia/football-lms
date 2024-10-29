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
        return $this->performanceReviewRepository->getByPlayer($player);
    }

    public function getByEvent(Player $player, EventSchedule $schedule)
    {
        return $this->performanceReviewRepository->getByPlayer($player, $schedule);
    }

    public function indexAllPlayerInEvent(EventSchedule $schedule)
    {
        $data = $schedule->players;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($schedule){
                $review = $this->performanceReviewRepository->getByPlayer($item, $schedule);
                if (isAllAdmin()){
                    $button = '<a class="btn btn-sm btn-outline-secondary" href="' . route('coach.player-managements.performance-reviews', ['player'=>$item->id]) . '" data-toggle="tooltip" data-placement="bottom" title="View All PLayer Performance Review">
                                    <span class="material-icons">visibility</span>
                               </a>';
                } elseif(isCoach()){
                    if (!$review){
                        $reviewBtn = '<a class="dropdown-item addPerformanceReview" id="'.$item->id.'" data-eventId="'.$schedule->id.'"><span class="material-icons">add</span> Add Player Performance Review</a>';
                    } else {
                        $reviewBtn = '<a class="dropdown-item editPerformanceReview" id="'.$item->id.'" data-eventId="'.$schedule->id.'"  data-reviewId="'.$review->id.'"><span class="material-icons">edit</span> Edit Player Performance Review</a>';
                    }

                    $button = '<div class="dropdown">
                                      <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="' . route('player-managements.skill-stats', ['player'=>$item->id]) . '"><span class="material-icons">visibility</span> View PLayer Performance Review</a>
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
                $review = $this->performanceReviewRepository->getByPlayer($item, $schedule);
                if ($review){
                    $text = $review->performanceReview;
                } else{
                    $text = 'Performance review still not added yet';
                }
                return $text;
            })
            ->editColumn('performance_review_created', function ($item) use ($schedule){
                $review = $this->performanceReviewRepository->getByPlayer($item, $schedule);
                if ($review){
                    $text = date('M d, Y h:i A', strtotime($review->created_at));
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->editColumn('performance_review_last_updated', function ($item) use ($schedule){
                $review = $this->performanceReviewRepository->getByPlayer($item, $schedule);
                if ($review){
                    $text = date('M d, Y h:i A', strtotime($review->updated_at));
                } else{
                    $text = '-';
                }
                return $text;
            })
            ->rawColumns(['action','name', 'performance_review', 'performance_review_created','performance_review_last_updated'])
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
