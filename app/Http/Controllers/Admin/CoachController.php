<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CoachRequest;
use App\Http\Requests\PlayerTeamRequest;
use App\Http\Requests\UpdateCoachRequest;
use App\Models\Coach;
use App\Models\Team;
use App\Services\CoachService;
use App\Services\TeamService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class CoachController extends Controller
{
    private CoachService $coachService;
    private TeamService $teamService;

    public function __construct(CoachService $coachService, TeamService $teamService)
    {
        $this->coachService = $coachService;
        $this->teamService = $teamService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.managements.coaches.index', [
            'certifications' => $this->coachService->getCoachCert(),
            'specializations' => $this->coachService->getCoachSpecializations(),
            'teams' => $this->teamService->allTeams(),
        ]);
    }

    public function indexTables(): JsonResponse
    {
        $certification = request()->input('certification');
        $specializations = request()->input('specializations');
        $team = request()->input('team');
        $status = request()->input('status');

        return $this->coachService->index($certification, $specializations, $team, $status);
    }

    public function coachTeams(Coach $coach): JsonResponse
    {
        return $this->coachService->coachTeams($coach);
    }

    public function updateTeams(PlayerTeamRequest $request, Coach $coach): JsonResponse
    {
        $data = $request->validated();
        $coach = $this->coachService->updateTeams($data, $coach, $this->getLoggedUser());
        $message = "Coach ".$this->getUserFullName($coach->user)." successfully added to a new team";
        return ApiResponse::success($coach, $message);
    }

    public function removeTeam(Coach $coach, Team $team): JsonResponse
    {
        $result = $this->coachService->removeTeam($coach, $team, $this->getLoggedUser());
        $message = "Coach ".$this->getUserFullName($coach->user)." successfully removed from team ".$team->teamName.".";
        return ApiResponse::success($result, $message);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.managements.coaches.create', [
            'countries' => $this->coachService->getCountryData(),
            'certifications' => $this->coachService->getCoachCert(),
            'specializations' => $this->coachService->getCoachSpecializations(),
            'teams' => $this->teamService->allTeams(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CoachRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $loggedUser = $this->getLoggedUser();
        $this->coachService->store($data, $this->getAcademyId(), $loggedUser);
        $this->successAlertAddUser($data, 'added');
        return redirect()->route('coach-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coach $coach)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();
        return view('pages.managements.coaches.detail', [
            'data' => $coach,
            'teams' => $this->coachService->getTeamsHaventJoinedByCoach($coach),
            'matchPlayed' => $this->coachService->totalMatchPlayed($coach),
            'matchPlayedThisMonth' => $this->coachService->totalMatchPlayed($coach, $startDate, $endDate),
            'goals' => $this->coachService->totalGoals($coach),
            'goalsThisMonth' => $this->coachService->totalGoals($coach, $startDate, $endDate),
            'goalConceded' => $this->coachService->goalConceded($coach),
            'goalConcededThisMonth' => $this->coachService->goalConceded($coach, $startDate, $endDate),
            'winRate' => $this->coachService->winRate($coach),
            'winRateThisMonth' => $this->coachService->winRate($coach, $startDate, $endDate),
            'wins' => $this->coachService->wins($coach),
            'winsThisMonth' => $this->coachService->wins($coach, $startDate, $endDate),
            'lose' => $this->coachService->lose($coach),
            'loseThisMonth' => $this->coachService->lose($coach, $startDate, $endDate),
            'draw' => $this->coachService->draw($coach),
            'drawThisMonth' => $this->coachService->draw($coach, $startDate, $endDate),
            'goalsDifference' => $this->coachService->goalsDifference($coach),
            'goalsDifferenceThisMonth' => $this->coachService->goalsDifference($coach, $startDate, $endDate),
            'cleanSheets' => $this->coachService->goalsDifference($coach),
            'cleanSheetsThisMonth' => $this->coachService->goalsDifference($coach, $startDate, $endDate),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coach $coach)
    {
        return view('pages.managements.coaches.edit',[
            'data' => $coach,
            'certifications' => $this->coachService->getCoachCert(),
            'specializations' => $this->coachService->getCoachSpecializations(),
            'countries' => $this->coachService->getCountryData(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCoachRequest $request, Coach $coach): RedirectResponse
    {
        $data = $request->validated();
        $this->coachService->update($data, $coach, $this->getLoggedUser());
        $this->successAlertAddUser($data, 'updated');
        return redirect()->route('coach-managements.show', $coach->id);
    }

    public function deactivate(Coach $coach): JsonResponse
    {
        try {
            $data = $this->coachService->setStatus($coach, '0', $this->getLoggedUser());
            $message = "Coach ".$this->getUserFullName($coach->user)."'s account status successfully set to deactivated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating player ".$this->getUserFullName($coach->user)."'s account status to deactivate: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function activate(Coach $coach): JsonResponse
    {
        try {
            $data = $this->coachService->setStatus($coach, '1', $this->getLoggedUser());
            $message = "Coach ".$this->getUserFullName($coach->user)."'s account status successfully set to activated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating player ".$this->getUserFullName($coach->user)."'s account status to activated: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function changePassword(ChangePasswordRequest $request, Coach $coach): JsonResponse
    {
        $data = $request->validated();
        $result = $this->coachService->changePassword($data, $coach, $this->getLoggedUser());
        $message = "Coach ".$this->getUserFullName($coach->user)."'s account password successfully updated!";
        return ApiResponse::success($result, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coach $coach): JsonResponse
    {
        try {
            $message = "Coach ".$this->getUserFullName($coach->user)."'s account successfully deleted.";
            $data = $this->coachService->destroy($coach, $this->getLoggedUser());
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while deleting coach ".$this->getUserFullName($coach->user)."'s account: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
}
