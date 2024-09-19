<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\OpponentTeam;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Nnjeim\World\World;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class CoachService extends Service
{
    public function index(): JsonResponse
    {
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
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                $playerTeam = '';
                if(count($item->teams) === 0){
                    $playerTeam = 'No Team';
                }else{
                    foreach ($item->teams as $team){
                        $playerTeam .= '<span class="badge badge-pill badge-danger">'.$team->teamName.'</span>';
                    }
                }
                return $playerTeam;
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
                                        <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->specializations->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->editColumn('status', function ($item){
                if ($item->user->status == '1') {
                    return '<span class="badge badge-pill badge-success">Aktif</span>';
                }elseif ($item->user->status == '0'){
                    return '<span class="badge badge-pill badge-danger">Non Aktif</span>';
                }
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->rawColumns(['action', 'name','status', 'age', 'teams'])
            ->make();
    }

    public function coachTeams(Coach $coach): JsonResponse
    {
        return Datatables::of($coach->teams)
            ->addColumn('action', function ($item) {
                return '
                        <div class="dropdown">
                          <button class="btn btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="'.route('coach-managements.edit', $item->id).'"><span class="material-icons">edit</span> Edit Team</a>
                            <a class="dropdown-item" href="'.route('coach-managements.show', $item->id).'"><span class="material-icons">visibility</span> View Team</a>
                            <button type="button" class="dropdown-item delete-team" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Remove coach from Team
                            </button>
                          </div>
                        </div>';
            })
            ->editColumn('name', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->division . ' - '.$item->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('date', function ($item){
                return date('M d, Y', strtotime($item->pivot->created_at));
            })
            ->rawColumns(['action', 'name','date'])
            ->make();
    }

    public function removeTeam(Coach $coach, Team $team)
    {
        $coach->teams()->detach($team->id);
        return $coach;
    }

    public function updateTeams($teamData, Coach $coach)
    {
        $coach->teams()->sync($teamData);
        return $coach;
    }

    public function create()
    {
        $action =  World::countries();
        if ($action->success) {
            $countries = $action->data;
        }

        $certifications = CoachCertification::all();
        $specializations = CoachSpecialization::all();
        $teams = Team::where('teamSide', 'Academy Team')->get();

        return compact('countries', 'certifications', 'specializations', 'teams');
    }

    public  function store(array $data, $academyId){

        $data['password'] = bcrypt($data['password']);

        if (array_key_exists('foto', $data)){
            $data['foto'] = $data['foto']->store('assets/user-profile', 'public');
        }else{
            $data['foto'] = 'images/undefined-user.png';
        }

        $data['status'] = '1';
        $data['academyId'] = $academyId;

        $user = User::create($data);
        $user->assignRole('coach');

        $data['userId'] = $user->id;

        $coach = Coach::create($data);
        $coach->teams()->attach($data['team']);
        return $coach;
    }
    public function show(User $user)
    {
        $fullName = $this->getUserFullName($user);
        $age = $this->getAge($user->dob);
        $teams = $this->getAcademyTeams();

        $team_id = [];

        foreach ($user->coach->teams as $team){
            $team_id[] = $team->id;
        }

        return compact('fullName', 'age', 'teams', 'team_id');
    }

    public function edit(User $coach){
        $action =  World::countries();
        if ($action->success) {
            $countries = $action->data;
        }

        $certifications = CoachCertification::all();
        $specializations = CoachSpecialization::all();
        $fullname = $coach->firstName . ' ' . $coach->lastName;

        return compact('countries', 'certifications', 'specializations', 'fullname', 'coach');
    }

    public function update(array $playerData, User $user): User
    {
        if (array_key_exists('foto', $playerData)){
            $this->deleteImage($user->foto);
            $playerData['foto'] = $playerData['foto']->store('assets/player-logo', 'public');
        }else{
            $playerData['foto'] = $user->foto;
        }

        $user->update($playerData);
        $user->player->update($playerData);

        return $user;
    }
    public function activate(User $user): User
    {
        $user->update(['status' => '1']);
        return $user;
    }

    public function deactivate(User $user): User
    {
        $user->update(['status' => '0']);
        return $user;
    }

    public function changePassword($data, User $user){
        $user->update([
            'password' => bcrypt($data)
        ]);
        return $user;
    }

    public function destroy(User $user): User
    {
        $this->deleteImage($user->foto);
        $user->player->delete();
        $user->delete();
        return $user;
    }
}
