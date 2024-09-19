<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoachRequest;
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->coachService->create();

        return view('pages.admins.managements.coaches.create', [
            'countries' => $data['countries'],
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

        $text = 'Coach '.$data['firstName'].' '.$data['lastName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('coach-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $coach)
    {
        $fullName = $coach->firstName . ' ' . $coach->lastName;
        $age = $this->getAge($coach->dob);

        if(count($coach->coach->teams) == 0){
            $team = 'No Team';
        }else{
            $team = $coach->coach->teams->name;
        }

        return view('pages.admins.managements.coaches.detail', [
            'user' => $coach,
            'fullName' => $fullName,
            'age' => $age,
            'team' => $team
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $coach)
    {
        $fullname = $coach->firstName . ' ' . $coach->lastName;
        $action =  World::countries();
        $certifications = CoachCertification::all();
        $specializations = CoachSpecialization::all();
        if ($action->success) {
            $countries = $action->data;
        }
        return view('pages.admins.managements.coaches.edit',[
            'coach' => $coach,
            'fullname' => $fullname,
            'certifications' => $certifications,
            'specializations' => $specializations,
            'countries' => $countries
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CoachRequest $request, User $coach)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')){
            $data['foto'] = $request->file('foto')->store('assets/user-profile', 'public');
        }else{
            $data['foto'] = $coach->foto;
        }

        $coach->update($data);
        $coach->coach->update($data);

        $text = 'Coach '.$coach->firstName.' '.$coach->lastName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('coach-managements.show', $coach->id);
    }

    public function deactivate(User $coach){
        $coach->update([
            'status' => '0'
        ]);
        Alert::success('Coach '.$coach->firstName.' '.$coach->lastName.' status successfully deactivated!');
        return redirect()->route('coach-managements.index');
    }

    public function activate(User $coach){
        $coach->update([
            'status' => '1'
        ]);
        Alert::success('Coach '.$coach->firstName.' '.$coach->lastName.' status successfully activated!');
        return redirect()->route('coach-managements.index');
    }

    public function changePasswordPage(User $coach){
        $fullName = $coach->firstName . ' ' . $coach->lastName;

        return view('pages.admins.managements.coaches.change-password',[
            'user' => $coach,
            'fullName' => $fullName
        ]);
    }

    public function changePassword(Request $request, User $coach){
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $coach->update([
            'password' => bcrypt($validator->getData()['password'])
        ]);
        Alert::success('Coach '.$coach->firstName.' '.$coach->lastName.' password successfully updated!');
        return redirect()->route('coach-managements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $coach)
    {
        if (File::exists($coach->foto) && $coach->foto != 'images/undefined-user.png'){
            File::delete($coach->foto);
        }

        $coach->coach->delete();
        $coach->delete();

        return response()->json(['success' => true]);
    }
}
