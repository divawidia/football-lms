<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            return Datatables::of($query)
                ->addColumn('action', function ($item) {
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
                                <a class="dropdown-item" href="' . route('coach-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Coach</a>
                                <a class="dropdown-item" href="' . route('coach-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Coach</a>
                                ' . $statusButton . '
                                <a class="dropdown-item" href="' . route('coach-managements.change-password-page', $item->userId) . '"><span class="material-icons">lock</span> Change Coach Password</a>
                                <button type="button" class="dropdown-item delete-user" id="' . $item->userId . '">
                                    <span class="material-icons">delete</span> Delete Coach
                                </button>
                              </div>
                            </div>';
                })
                ->editColumn('teams.name', function ($item) {
                    if (count($item->teams) === 0) {
                        $team = 'No Team';
                    } else {
                        $team = $item->teams->name;
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
                                            <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
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
                ->rawColumns(['action', 'name', 'status', 'age', 'teams.name'])
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

        return view('pages.admins.managements.coaches.create', [
            'countries' => $countries,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

        $text = 'Coach '.$data['firstName'].' '.$data['lastName'].' account successfully added!';
        Alert::success($text);
        return redirect()->route('coach-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $coach_management)
    {
        $fullName = $coach_management->firstName . ' ' . $coach_management->lastName;
        $age = $this->getAge($coach_management->dob);

        if(count($coach_management->coach->teams) == 0){
            $team = 'No Team';
        }else{
            $team = $coach_management->coach->teams->name;
        }

        return view('pages.admins.managements.coaches.detail', [
            'user' => $coach_management,
            'fullName' => $fullName,
            'age' => $age,
            'team' => $team
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $coach_management)
    {
        $fullname = $coach_management->firstName . ' ' . $coach_management->lastName;
        $positions = PlayerPosition::all();
        $action =  World::countries();
        if ($action->success) {
            $countries = $action->data;
        }
        return view('pages.admins.managements.coaches.edit',[
            'coach' => $coach_management,
            'fullname' => $fullname,
            'positions' => $positions,
            'countries' => $countries
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $coach_management)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')){
            $data['foto'] = $request->file('foto')->store('assets/user-profile', 'public');
        }else{
            $data['foto'] = $coach_management->foto;
        }

        $coach_management->update($data);
        $coach_management->player->update($data);

        $text = 'Coach '.$coach_management->firstName.' '.$coach_management->lastName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('coach-managements.show', $coach_management->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
