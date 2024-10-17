<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Services\Coach\SkillAssessmentService;
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
        return view('pages.coach.academies.skill-assessment.index');
    }
}
