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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Coach::with('user', 'teams')->get();
            return Datatables::of($query)->addColumn('action', function ($item) {
                    if ($item->user->status == '1') {
                        $statusButton = '<form action="' . route('deactivate-coach', $item->userId) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">block</span> Deactivate Coach
                                                </button>
                                            </form>';
                    } else {
                        $statusButton = '<form action="' . route('activate-coach', $item->userId) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">check_circle</span> Activate Coach
                                                </button>
                                            </form>';
                    }
                    return '
                            <div class="dropdown">
                              <button class="btn btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . route('coach-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Coach Profile</a>
                                <a class="dropdown-item" href="' . route('coach-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Coach</a>
                                ' . $statusButton . '
                                <a class="dropdown-item" href="' . route('coach-managements.change-password-page', $item->userId) . '"><span class="material-icons">lock</span> Change Coach Password</a>
                                <button type="button" class="dropdown-item delete-user" id="' . $item->userId . '">
                                    <span class="material-icons">delete</span> Delete Coach
                                </button>
                              </div>
                            </div>';
                })
                ->editColumn('teams', function ($item) {
                    if (count($item->teams) === 0) {
                        $team = 'No Team';
                    } else {
                        $team = '';
                        foreach ($item->teams as $team){
                            $team =+ '<span class="badge badge-pill badge-success">'.$team->name.'</span>';
                        }
                    }
                    return $team;
                })
                ->editColumn('name', function ($item) {
                    return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' ' . $item->user->lastName . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->specializations->name . ' - '.$item->certification->name.'</small>
                                        </div>
                                    </div>

                                </div>
                            </div>';
                })
                ->editColumn('status', function ($item) {
                    if ($item->user->status == '1') {
                        return '<span class="badge badge-pill badge-success">Aktif</span>';
                    } elseif ($item->user->status == '0') {
                        return '<span class="badge badge-pill badge-danger">Non Aktif</span>';
                    }
                })
                ->editColumn('age', function ($item) {
                    return $this->getAge($item->user->dob);
                })
                ->rawColumns(['action', 'name', 'status', 'age', 'teams'])
                ->make();
        }
        return view('pages.admins.managements.coaches.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action =  World::countries();
        if ($action->success) {
            $countries = $action->data;
        }

        $certifications = CoachCertification::all();
        $specializations = CoachSpecialization::all();
        $teams = Team::all();

        return view('pages.admins.managements.coaches.create', [
            'countries' => $countries,
            'certifications' => $certifications,
            'specializations' => $specializations,
            'teams' => $teams
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CoachRequest $request)
    {
        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);

        if ($request->hasFile('foto')){
            $data['foto'] = $request->file('foto')->store('assets/user-profile', 'public');
        }else{
            $data['foto'] = 'images/undefined-user.png';
        }

        $data['status'] = '1';
        $data['academyId'] = Auth::user()->academyId;

        $user = User::create($data);
        $user->assignRole('coach');

        $data['userId'] = $user->id;

        Coach::create($data);

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
        $positions = PlayerPosition::all();
        $action =  World::countries();
        if ($action->success) {
            $countries = $action->data;
        }
        return view('pages.admins.managements.coaches.edit',[
            'coach' => $coach,
            'fullname' => $fullname,
            'positions' => $positions,
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
        $coach->player->update($data);

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
        if (File::exists($coach->foto) && $coach->foto != 'assets/user-profile/avatar.png'){
            File::delete($coach->foto);
        }

        $coach->coach->delete();
        $coach->delete();

        return response()->json(['success' => true]);
    }
}
