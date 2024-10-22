<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CoachRequest;
use App\Http\Requests\PlayerTeamRequest;
use App\Http\Requests\UpdateCoachRequest;
use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use App\Services\CoachService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nnjeim\World\World;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

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
            return $this->coachService->index();
        }
        return view('pages.admins.managements.coaches.index');
    }

    public function coachTeams(Coach $coach)
    {
            return $this->coachService->coachTeams($coach);

    }

    public function updateTeams(PlayerTeamRequest $request, Coach $coach)
    {
        $data = $request->validated();
        $coach = $this->coachService->updateTeams($data, $coach);
        return response()->json($coach, 204);
    }

    public function removeTeam(Coach $coach, Team $team)
    {
        return $this->coachService->removeTeam($coach, $team);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->coachService->create();
        return view('pages.admins.managements.coaches.create', [
            'countries' => $this->coachService->getCountryData(),
            'certifications' => $data['certifications'],
            'specializations' => $data['specializations'],
            'teams' => $data['teams'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CoachRequest $request)
    {
        $data = $request->validated();
        $this->coachService->store($data, $this->getAcademyId());
        $this->successAlertAddUser($data, 'added');
        return redirect()->route('coach-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coach $coach)
    {
        $data = $this->coachService->show($coach);

        return view('pages.admins.managements.coaches.detail', [
            'data' => $coach,
            'fullName' => $data['fullName'],
            'age' => $data['age'],
            'teams' => $data['teams'],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coach $coach)
    {
        $data = $this->coachService->edit();
        return view('pages.admins.managements.coaches.edit',[
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

    public function deactivate(Coach $coach){
        $this->coachService->deactivate($coach);
        $this->successAlertStatusUser($coach->user, 'deactivated');
        return redirect()->route('coach-managements.show', $coach->id);
    }

    public function activate(Coach $coach){
        $this->coachService->activate($coach);
        $this->successAlertStatusUser($coach->user, 'activated');
        return redirect()->route('coach-managements.show', $coach->id);
    }

    public function changePassword(ChangePasswordRequest $request, Coach $coach){
        $data = $request->validated();
        $result = $this->coachService->changePassword($data, $coach);
        return response()->json([
            'status' => 200,
            'data' => $result,
            'message' => 'Successfully change password'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coach $coach)
    {
        $result = $this->coachService->destroy($coach);
        return response()->json([
            'status' => 200,
            'data' => $result,
            'message' => 'Successfully delete admin'
        ]);
    }
}
