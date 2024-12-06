<?php

namespace App\Http\Controllers\Coach;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerPerformanceReviewRequest;
use App\Http\Requests\SkillAssessmentRequest;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;
use App\Models\PlayerSkillStats;
use App\Services\Coach\SkillAssessmentService;
use App\Services\PlayerPerformanceReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerPerformanceReviewController extends Controller
{
    private PlayerPerformanceReviewService $performanceReviewService;

    public function __construct(PlayerPerformanceReviewService $performanceReviewService)
    {
        $this->performanceReviewService = $performanceReviewService;
    }

    public function index()
    {
        $player = $this->getLoggedPLayerUser();
        return view('pages.coaches.academies.skill-assessments.index');
    }

    public function playerPerformancePage(Player $player)
    {
        return view('pages.managements.players.performance-reviews', ['data' => $player]);
    }
    public function indexPlayer(Player $player)
    {
        return $this->performanceReviewService->index($player);
    }

    public function indexAllPlayerInEvent(Request $request, EventSchedule $schedule)
    {
        $teamId  =$request->input('teamId');
        return $this->performanceReviewService->indexAllPlayerInEvent($schedule, $teamId);
    }

    public function edit(PlayerPerformanceReview $review): JsonResponse
    {
        return response()->json([
            'status' => '200',
            'data' => $review,
            'message' => 'Success'
        ]);
    }

    public function store(PlayerPerformanceReviewRequest $request, Player $player)
    {
        $data = $request->validated();
        $coach = $this->getLoggedCoachUser();
        $response = $this->performanceReviewService->store($data, $player, $coach);
        $message = $this->getUserFullName($player->user)."'s performance review successfully created.";
        return ApiResponse::success($response, $message);
    }

    public function update(PlayerPerformanceReviewRequest $request, PlayerPerformanceReview $review)
    {
        $data = $request->validated();
        $response = $this->performanceReviewService->update($data, $review);
        $message = $this->getUserFullName($review->player->user)."'s performance review successfully updated.";
        return ApiResponse::success($response, $message);
    }

    public function destroy(PlayerPerformanceReview $review)
    {
        $response = $this->performanceReviewService->destroy($review);
        $message = $this->getUserFullName($review->player->user)."'s performance review successfully deleted.";
        return ApiResponse::success($response, $message);
    }
}
