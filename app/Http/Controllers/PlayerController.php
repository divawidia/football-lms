<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\PlayerRequest;
use App\Http\Requests\PlayerTeamRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Models\Player;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use App\Services\PlayerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nnjeim\World\World;
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
        if (\request()->ajax()) {
            return $this->playerService->index();
        }
        return view('pages.managements.players.index');
    }

    public function coachIndex()
    {
        if (request()->ajax()) {
            return $this->playerService->coachPlayerIndex($this->getLoggedCoachUser());
        }
        return view('pages.managements.players.index');
    }

    public function playerTeams(Player $player)
    {
        return $this->playerService->playerTeams($player);
    }

    public function removeTeam(Player $player, Team $team)
    {
        return $this->playerService->removeTeam($player, $team);
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
        $this->playerService->store($data, $this->getAcademyId());

        $text = $data['firstName'].' '.$data['lastName'].' account successfully added!';
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

        return view('pages.managements.players.detail', [
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


        return view('pages.managements.players.skill-detail', [
            'data' => $player,
            'skillStats' => $skillStats,
            'skillStatsHistory' => $skillStatsHistory,
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

        $this->playerService->update($data, $player);

        $text =  $data['firstName'].' '.$data['lastName'].' successfully updated!';
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
        $this->playerService->deactivate($player);
        Alert::success($player->user->firstName . ' '. $player->user->lastName . ' account status successfully deactivated!');
        return redirect()->route('player-managements.show', $player->id);
    }

    public function activate(Player $player)
    {
        $this->playerService->activate($player);
        Alert::success($player->user->firstName . ' '. $player->user->lastName . ' account status successfully activated!');
        return redirect()->route('player-managements.show', $player->id);
    }

    public function changePassword(ChangePasswordRequest $request, Player $player)
    {
        $data = $request->validated();
        $result = $this->playerService->changePassword($data, $player);

        return response()->json([
            'status' => 200,
            'data' => $result,
            'message' => 'Successfully change password'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        $result = $this->playerService->destroy($player);
        return response()->json([
            'status' => 200,
            'data' => $result,
            'message' => 'Successfully delete player'
        ]);
    }
}
