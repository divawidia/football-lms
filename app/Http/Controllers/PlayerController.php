<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\PlayerRequest;
use App\Http\Requests\PlayerTeamRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Models\Player;
use App\Models\Team;
use App\Services\PlayerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

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
        return view('pages.managements.players.index');
    }
    public function adminIndex()
    {
        return $this->playerService->index();
    }
    public function coachIndex()
    {
        return $this->playerService->coachPlayerIndex($this->getLoggedCoachUser());
    }

    public function playerTeams(Player $player)
    {
        return $this->playerService->playerTeams($player);
    }

    public function removeTeam(Player $player, Team $team)
    {
        try {
            $data = $this->playerService->removeTeam($player, $team);
            $message = "Player ".$this->getUserFullName($player->user)." successfully removed from team ".$team->teamName.".";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while removing player ".$this->getUserFullName($player->user)." from team ".$team->teamName.": " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.managements.players.create', [
            'countries' => $this->playerService->getCountryData(),
            'positions' => $this->playerService->getPlayerPosition(),
            'teams' => $this->playerService->getAcademyTeams(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlayerRequest $request)
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        $player = $this->playerService->store($data, $this->getAcademyId(), $loggedUser);

        $text = "Player ".$this->getUserFullName($player->user)."'s account successfully added!";
        Alert::success($text);
        return redirect()->route('player-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
        $overview = $this->playerService->show($player);
        $performanceReviews = $player->playerPerformanceReview;
        $playerSkillStats = $this->playerService->skillStatsChart($player);
        $hasntJoinedTeams = $this->playerService->hasntJoinedTeams($player);
//        $skillStatsHistory = $this->playerService->skillStatsHistoryChart($player);
        $allSkills = $this->playerService->getSkillStats($player)->first();

        return view('pages.managements.players.detail', [
            'data' => $player,
            'overview' => $overview,
            'performanceReviews' => $performanceReviews,
            'playerSkillStats' => $playerSkillStats,
            'hasntJoinedTeams' => $hasntJoinedTeams,
//            'skillStatsHistory' => $skillStatsHistory,
            'allSkills' => $allSkills,
        ]);
    }

    public function skillStatsHistory(Request $request, Player $player)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $result = $this->playerService->skillStatsHistoryChart($player, $startDate, $endDate);

        return ApiResponse::success($result);
    }

    public function skillStatsDetail(Player $player)
    {

        $skillStats =$this->playerService->skillStatsChart($player);
        $allSkills = $this->playerService->getSkillStats($player)->first();


        return view('pages.managements.players.skill-detail', [
            'data' => $player,
            'skillStats' => $skillStats,
            'allSkills' => $allSkills,
        ]);
    }

    public function skillStatsDetailPlayer()
    {
        $player = $this->getLoggedPLayerUser();
        $skillStats =$this->playerService->skillStatsChart($player);
        $allSkills = $this->playerService->getSkillStats($player)->first();


        return view('pages.managements.players.skill-detail', [
            'data' => $player,
            'skillStats' => $skillStats,
            'allSkills' => $allSkills,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Player $player)
    {
        return view('pages.managements.players.edit', [
            'data' => $player,
            'fullName' => $this->playerService->getUserFullName($player->user),
            'positions' => $this->playerService->getPlayerPosition(),
            'teams' => $this->playerService->getAcademyTeams(),
            'countries' => $this->playerService->getCountryData()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlayerRequest $request, Player $player)
    {
        $data = $request->validated();

        $player = $this->playerService->update($data, $player);

        $text = "Player ".$this->getUserFullName($player->user)."'s account successfully updated!";
        Alert::success($text);
        return redirect()->route('player-managements.show', $player->id);
    }

    public function updateTeams(PlayerTeamRequest $request, Player $player)
    {
        $data = $request->validated();
        $player = $this->playerService->updateTeams($data, $player);
        return response()->json($player, 204);
    }

    public function upcomingMatches(Player $player){
        if (\request()->ajax()){
            return $this->playerService->playerUpcomingMatches($player);
        }

        return view('pages.managements.players.upcoming-matches', [
            'data' => $player,
            'matchCalendar' => $this->playerService->playerMatchCalendar($player)
        ]);
    }

    public function upcomingTrainings(Player $player){
        if (\request()->ajax()){
            return $this->playerService->playerUpcomingTraining($player);
        }

        return view('pages.managements.players.upcoming-trainings', [
            'data' => $player,
            'trainingCalendar' => $this->playerService->playerTrainingCalendar($player)
        ]);
    }

    public function deactivate(Player $player)
    {
        try {
            $data = $this->playerService->setStatus($player, '0');
            $message = "Player ".$this->getUserFullName($player->user)."'s account status successfully set to deactivated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating player ".$this->getUserFullName($player->user)."'s account status to deactivate: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function activate(Player $player)
    {
        try {
            $data = $this->playerService->setStatus($player, '1');
            $message = "Player ".$this->getUserFullName($player->user)."'s account status successfully set to activated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating player ".$this->getUserFullName($player->user)."'s account status to activated: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function changePassword(ChangePasswordRequest $request, Player $player)
    {
        $data = $request->validated();
        $result = $this->playerService->changePassword($data, $player);
        $message = "Player ".$this->getUserFullName($player->user)."'s account password successfully updated!";
        return ApiResponse::success($result, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        $loggedUser = $this->getLoggedUser();
        try {
            $data = $this->playerService->destroy($player, $loggedUser);
            $message = "Player ".$this->getUserFullName($player->user)."'s account successfully deleted.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while deleting player ".$this->getUserFullName($player->user)."'s account: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
}
