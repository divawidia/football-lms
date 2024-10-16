<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\PlayerService;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    private PlayerService $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->playerService->coachPlayerIndex($this->getLoggedCoachUser());
        }
        return view('pages.coaches.managements.players.index');
    }

    public function show(Player $player)
    {
        $overview = $this->playerService->show($player);
        $performanceReviews = $player->playerPerformanceReview;
        $playerSkillStats =$this->playerService->skillStatsChart($player);

        return view('pages.coaches.managements.players.detail', [
            'data' => $player,
            'overview' => $overview,
            'performanceReviews' => $performanceReviews,
            'playerSkillStats' => $playerSkillStats
        ]);
    }

    public function skillStatsDetail(Player $player)
    {

        $skillStats =$this->playerService->skillStatsChart($player);
        $skillStatsHistory = $this->playerService->skillStatsHistoryChart($player);
        $allSkills = $this->playerService->getSkillStats($player)->first();


        return view('pages.coaches.managements.players.skill-detail', [
            'data' => $player,
            'skillStats' => $skillStats,
            'skillStatsHistory' => $skillStatsHistory,
            'allSkills' => $allSkills,
        ]);
    }

    public function upcomingMatches(Player $player){
        if (\request()->ajax()){
            return $this->playerService->playerUpcomingMatches($player);
        }

        return view('pages.coaches.managements.players.upcoming-matches', [
            'data' => $player,
            'matchCalendar' => $this->playerService->playerMatchCalendar($player)
        ]);
    }

    public function upcomingTrainings(Player $player){
        if (\request()->ajax()){
            return $this->playerService->playerUpcomingTraining($player);
        }

        return view('pages.coaches.managements.players.upcoming-trainings', [
            'data' => $player,
            'trainingCalendar' => $this->playerService->playerTrainingCalendar($player)
        ]);
    }
}
