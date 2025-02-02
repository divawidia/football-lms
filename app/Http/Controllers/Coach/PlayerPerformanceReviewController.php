<?php

namespace App\Http\Controllers\Coach;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerPerformanceReviewRequest;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerPerformanceReview;
use App\Models\PlayerSkillStats;
use App\Models\Training;
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
        return view('pages.academies.skill-assessments.index');
    }

    public function playerPerformancePage(Player $player)
    {
        return view('pages.managements.players.performance-reviews', ['data' => $player]);
    }
    public function indexPlayer(Player $player)
    {
        return $this->performanceReviewService->index($player);
    }

    public function indexAllPlayerInMatch(Request $request, MatchModel $match)
    {
        $teamId  =$request->input('teamId');
        return $this->performanceReviewService->indexAllPlayerInMatch($match, $teamId);
    }
    public function indexAllPlayerInTraining(Training $training): JsonResponse
    {
        return $this->performanceReviewService->indexAllPlayerInTraining($training);
    }

    public function edit(PlayerPerformanceReview $review): JsonResponse
    {
        $data = [
            'review' => $review,
            'player' => $review->player->user,
        ];
        return ApiResponse::success($data);
    }

    public function store(PlayerPerformanceReviewRequest $request, Player $player)
    {
        $data = $request->validated();
        $response = $this->performanceReviewService->store($data, $player, $this->getLoggedCoachUser());
        $message = $this->getUserFullName($player->user)."'s performance review successfully created.";
        return ApiResponse::success($response, $message);
    }

    public function update(PlayerPerformanceReviewRequest $request, PlayerPerformanceReview $review)
    {
        $data = $request->validated();
        $response = $this->performanceReviewService->update($data, $review, $this->getLoggedCoachUser());
        $message = $this->getUserFullName($review->player->user)."'s performance review successfully updated.";
        return ApiResponse::success($response, $message);
    }

    public function destroy(PlayerPerformanceReview $review)
    {
        $response = $this->performanceReviewService->destroy($review, $this->getLoggedCoachUser());
        $message = $this->getUserFullName($review->player->user)."'s performance review successfully deleted.";
        return ApiResponse::success($response, $message);
    }
}
