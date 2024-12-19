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
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\String\s;

class CoachController extends Controller
{
    private CoachService $coachService;

    public function __construct(CoachService $coachService)
    {
        $this->coachService = $coachService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $certification = request()->input('certification');
            $specializations = request()->input('specializations');
            $team = request()->input('team');
            $status = request()->input('status');

            return $this->coachService->index($certification, $specializations, $team, $status);
        }
        return view('pages.managements.coaches.index', [
            'certifications' => $this->coachService->getCoachCert(),
            'specializations' => $this->coachService->getCoachSpecializations(),
            'teams' => $this->coachService->getTeams(),
        ]);
    }

    public function coachTeams(Coach $coach): JsonResponse
    {
        return $this->coachService->coachTeams($coach);
    }

    public function updateTeams(PlayerTeamRequest $request, Coach $coach): JsonResponse
    {
        $data = $request->validated();
        $coach = $this->coachService->updateTeams($data, $coach);
        $message = "Coach ".$this->getUserFullName($coach->user)." successfully added to a new team";
        return ApiResponse::success($coach, $message);
    }

    public function removeTeam(Coach $coach, Team $team): JsonResponse
    {
        $result = $this->coachService->removeTeam($coach, $team);
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
            'teams' => $this->coachService->getTeams(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CoachRequest $request)
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
        $data = $this->coachService->show($coach);

        return view('pages.managements.coaches.detail', [
            'data' => $coach,
            'fullName' => $data['fullName'],
            'age' => $data['age'],
            'teams' => $data['teams'],
            'dataOverview' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coach $coach)
    {
        $data = $this->coachService->edit();
        return view('pages.managements.coaches.edit',[
            'data' => $coach,
            'fullName' => $this->coachService->getUserFullName($coach->user),
            'certifications' => $data['certifications'],
            'specializations' => $data['specializations'],
            'countries' => $this->coachService->getCountryData(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCoachRequest $request, Coach $coach)
    {
        $data = $request->validated();
        $this->coachService->update($data, $coach);
        $this->successAlertAddUser($data, 'updated');
        return redirect()->route('coach-managements.show', $coach->id);
    }

    public function deactivate(Coach $coach)
    {
        try {
            $data = $this->coachService->setStatus($coach, '0');
            $message = "Coach ".$this->getUserFullName($coach->user)."'s account status successfully set to deactivated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating player ".$this->getUserFullName($coach->user)."'s account status to deactivate: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function activate(Coach $coach)
    {
        try {
            $data = $this->coachService->setStatus($coach, '1');
            $message = "Coach ".$this->getUserFullName($coach->user)."'s account status successfully set to activated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while updating player ".$this->getUserFullName($coach->user)."'s account status to activated: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function changePassword(ChangePasswordRequest $request, Coach $coach)
    {
        $data = $request->validated();
        $result = $this->coachService->changePassword($data, $coach);
        $message = "Coach ".$this->getUserFullName($coach->user)."'s account password successfully updated!";
        return ApiResponse::success($result, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coach $coach)
    {
        $loggedUser = $this->getLoggedUser();
        try {
            $data = $this->coachService->destroy($coach, $loggedUser);
            $message = "Coach ".$this->getUserFullName($coach->user)."'s account successfully deleted.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while deleting coach ".$this->getUserFullName($coach->user)."'s account: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
}
