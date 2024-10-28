<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerPerformanceReviewRequest;
use App\Http\Requests\SkillAssessmentRequest;
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

    public function indexPlayer(Player $player)
    {
        $reviews = $this->performanceReviewService->index($player);
        return view('pages.coaches.academies.skill-assessments.index');
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
        return response()->json($response);
    }

    public function update(PlayerPerformanceReviewRequest $request, PlayerPerformanceReview $review)
    {
        $data = $request->validated();
        $response = $this->performanceReviewService->update($data, $review);
        return response()->json($response);
    }

    public function destroy(PlayerPerformanceReview $review)
    {
        $response = $this->performanceReviewService->destroy($review);
        return response()->json($response);
    }
}
