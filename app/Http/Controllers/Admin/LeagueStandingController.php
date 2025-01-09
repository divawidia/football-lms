<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompetitionMatchRequest;
use App\Http\Requests\CompetitionRequest;
use App\Http\Requests\PlayerTeamRequest;
use App\Http\Requests\UpdateCompetitionRequest;
use App\Http\Requests\UpdateTeamStandingRequest;
use App\Models\Competition;
use App\Models\LeagueStanding;
use App\Services\CompetitionService;
use App\Services\LeagueStandingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class LeagueStandingController extends Controller
{
    private LeagueStandingService $leagueStandingService;
    public function __construct(LeagueStandingService $leagueStandingService)
    {
        $this->leagueStandingService = $leagueStandingService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Competition $competition)
    {
        return $this->leagueStandingService->index($competition);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(PlayerTeamRequest $request, Competition $competition)
    {
        $data = $request->validated();
        $result = $this->leagueStandingService->store($data, $competition);
        return ApiResponse::success($result, 'Team successfully added to league standings!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Competition $competition,LeagueStanding $leagueStanding)
    {
        return ApiResponse::success($leagueStanding);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamStandingRequest $request, Competition $competition, LeagueStanding $leagueStanding)
    {
        $data = $request->validated();
        $this->leagueStandingService->update($data, $leagueStanding);

        $text = $leagueStanding->team->teamName.' league standings successfully updated!';
        return ApiResponse::success(true, $text);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competition $competition, LeagueStanding $leagueStanding)
    {
        $this->leagueStandingService->destroy($leagueStanding);
        $message = $leagueStanding->team->teamName.' successfully removed from league standings!';
        return ApiResponse::success(true, $message);
    }
}
