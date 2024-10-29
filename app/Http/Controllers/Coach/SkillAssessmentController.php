<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkillAssessmentRequest;
use App\Models\EventSchedule;
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
        return view('pages.coaches.academies.skill-assessments.index');
    }

    public function indexAllPlayerInEvent(EventSchedule $schedule)
    {
        return $this->skillAssessmentService->indexAllPlayerInEvent($schedule);
    }

    public function create(Player $player){
        return view('pages.coaches.academies.skill-assessments.create', [
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

        $response = $this->skillAssessmentService->store($data, $player, $coach);

        return response()->json($response);
    }

    public function update(SkillAssessmentRequest $request, PlayerSkillStats $skillStats)
    {
        $data = $request->validated();
        $coachId = $this->getLoggedCoachUser();

        $response = $this->skillAssessmentService->update($data, $skillStats,$coachId);

        return response()->json($response);
    }

    public function destroy(PlayerSkillStats $skillStats)
    {
        $response = $this->skillAssessmentService->destroy($skillStats);

        return response()->json($response);
    }
}
