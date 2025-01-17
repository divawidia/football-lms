<?php

namespace App\Http\Controllers\Coach;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkillAssessmentRequest;
use App\Models\Match;
use App\Models\Player;
use App\Models\PlayerSkillStats;
use App\Services\Coach\SkillAssessmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillAssessmentController extends Controller
{
    private SkillAssessmentService $skillAssessmentService;

    public function __construct(SkillAssessmentService $skillAssessmentService)
    {
        $this->skillAssessmentService = $skillAssessmentService;
    }

    public function index()
    {
        if (\request()->ajax()){
            return $this->skillAssessmentService->index($this->getLoggedCoachUser());
        }
        return view('pages.academies.skill-assessments.index');
    }

    public function indexAllPlayerInEvent(Request $request, Match $schedule)
    {
        $teamId = $request->input('teamId');
        return $this->skillAssessmentService->indexAllPlayerInEvent($schedule, $teamId);
    }

    public function create(Player $player){
        return view('pages.academies.skill-assessments.create', [
            'data' => $player,
        ]);
    }

    public function edit(PlayerSkillStats $skillStats): JsonResponse
    {
        return response()->json([
            'status' => '200',
            'data' => $skillStats,
            'message' => 'Success'
        ]);
    }

    public function store(SkillAssessmentRequest $request,Player $player)
    {
        $data = $request->validated();
        $coach = $this->getLoggedCoachUser();

        $this->skillAssessmentService->store($data, $player, $coach);

        $message = "Player ".$this->getUserFullName($player->user)."'s skills successfully updated.";
        return ApiResponse::success(message:  $message);
    }

    public function update(SkillAssessmentRequest $request, PlayerSkillStats $skillStats)
    {
        $data = $request->validated();
        $coachId = $this->getLoggedCoachUser();

        $this->skillAssessmentService->update($data, $skillStats,$coachId);

        $message = "Player ".$this->getUserFullName($$skillStats->player->user)."'s skills successfully updated.";
        return ApiResponse::success(message:  $message);
    }

    public function destroy(PlayerSkillStats $skillStats)
    {
        $this->skillAssessmentService->destroy($skillStats);
        $message = "Player ".$this->getUserFullName($$skillStats->player->user)."'s skills successfully deleted.";
        return ApiResponse::success(message:  $message);
    }
}
