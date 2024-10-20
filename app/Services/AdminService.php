<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Nnjeim\World\World;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class AdminService extends Service
{
    public function index(): JsonResponse
    {
        $query = Admin::with('user')->get();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if ($item->user->status == '1'){
                    $statusButton = '<form action="' . route('deactivate-admin', $item->userId) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">block</span> Deactivate Admin</a>
                                            </button>
                                        </form>';
                }else{
                    $statusButton = '<form action="' . route('activate-admin', $item->userId) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">check_circle</span> Activate Admin</a>
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
                            <a class="dropdown-item" href="' . route('admin-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Admin</a>
                            <a class="dropdown-item" href="' . route('admin-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Admin</a>
                            '. $statusButton .'
                            <a class="dropdown-item" href="' . route('admin-managements.change-password-page', $item->userId) . '"><span class="material-icons">lock</span> Change Admin Password</a>
                            <button type="submit" class="dropdown-item deleteAdmin" id="'.$item->userId.'">
                                <span class="material-icons">delete</span> Delete Admin
                            </button>
                          </div>
                        </div>';
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
                                        <a href="'.route('admin-managements.show', $item->id).'">
                                            <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        </a>
                                        <small class="js-lists-values-email text-50">' . $item->position . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->editColumn('status', function ($item){
                $badge = '';
                if ($item->user->status == '1') {
                    $badge = '<span class="badge badge-pill badge-success">Active</span>';
                }elseif ($item->user->status == '0'){
                    $badge = '<span class="badge badge-pill badge-danger">Non Active</span>';
                }
                return $badge;
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->rawColumns(['action', 'name','status', 'age'])
            ->make();
    }

    public  function store(array $data, $academyId)
    {
        $data['foto'] = $this->storeImage($data, 'foto', 'assets/user-profile', 'images/undefined-user.png');
        $data['password'] = bcrypt($data['password']);
        $data['status'] = '1';
        $data['academyId'] = $academyId;

        $user = User::create($data);
        $user->assignRole('admin');

        $data['userId'] = $user->id;

        $admin = Admin::create($data);
        return $admin;
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

    public function changePassword($data, Admin $admin){
        $admin->user()->update([
            'password' => bcrypt($data)
        ]);
        return $admin;
    }

    public function destroy(User $user): User
    {
        $this->deleteImage($user->foto);
        $user->player->delete();
        $user->delete();
        return $user;
    }
}
