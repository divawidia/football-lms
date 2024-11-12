<?php

namespace App\Services;

use App\Models\Admin;
use App\Notifications\AdminManagements\AdminAccountCreatedDeleted;
use App\Notifications\AdminManagements\AdminAccountUpdated;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AdminService extends Service
{
    private AdminRepository $adminRepository;
    private UserRepository $userRepository;
    private $loggedUser;
    public function __construct(AdminRepository $adminRepository, UserRepository $userRepository, $loggedUser)
    {
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
        $this->loggedUser = $loggedUser;
    }
    public function index(): JsonResponse
    {
        $query = $this->adminRepository->getAll();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if (isSuperAdmin()){
                    if ($item->user->status == '1'){
                        $statusButton = '<form action="' . route('deactivate-admin', $item->id) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons mr-2 text-danger">block</span> Deactivate Admin</a>
                                            </button>
                                        </form>';
                    }else{
                        $statusButton = '<form action="' . route('activate-admin', $item->id) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons mr-2 text-success">check_circle</span> Activate Admin</a>
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
                            <a class="dropdown-item" href="' . route('admin-managements.edit', $item->id) . '"><span class="material-icons mr-2">edit</span> Edit Admin</a>
                            <a class="dropdown-item" href="' . route('admin-managements.show', $item->id) . '"><span class="material-icons mr-2">visibility</span> View Admin</a>
                            '. $statusButton .'
                            <a class="dropdown-item changePassword" id="'.$item->id.'"><span class="material-icons mr-2">lock</span> Change Admin Password</a>
                            <button type="submit" class="dropdown-item deleteAdmin" id="'.$item->id.'">
                                <span class="material-icons mr-2 text-danger">delete</span> Delete Admin
                            </button>
                          </div>
                        </div>';
                } elseif (isAdmin()){
                    return '<a class="btn btn-sm btn-outline-secondary" href="' . route('admin-managements.show', $item->id) . '"><span class="material-icons">visibility</span></a>';
                }
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
                    $badge = '<span class="badge badge-pill badge-danger">Non-Active</span>';
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

        $user = $this->userRepository->createUserWithRole($data, 'admin');

        $data['userId'] = $user->id;
        $admin = $this->adminRepository->create($data);
        $superAdminName = $this->getUserFullName($this->loggedUser);

        Notification::send($this->userRepository->getAllAdminUsers(),new AdminAccountCreatedDeleted($superAdminName, $admin, 'created'));

        return $admin;
    }

    public function update(array $data, Admin $admin): Admin
    {
        $data['foto'] = $this->updateImage($data, 'foto', 'assets/user-profile', $admin->user->foto);
        $admin->update([
            'position'=> $data['position'],
            'hireDate'=> $data['hireDate'],
        ]);
        $admin->user()->update([
            'firstName' => $data['firstName'],
            'lastName'=> $data['lastName'],
            'email'=> $data['email'],
            'foto'=> $data['foto'],
            'dob'=> $data['dob'],
            'gender'=> $data['gender'],
            'address'=> $data['address'],
            'state_id'=> $data['state_id'],
            'city_id'=> $data['city_id'],
            'country_id'=> $data['country_id'],
            'zipCode'=> $data['zipCode'],
            'phoneNumber'=> $data['phoneNumber'],
        ]);
        $superAdminName = $this->getUserFullName($this->loggedUser);
        $admin->user->notify(new AdminAccountUpdated($superAdminName, $admin, 'updated'));
        return $admin;
    }

    public function activate(Admin $admin)
    {
        $admin->user()->update(['status' => '1']);
        $superAdminName = $this->getUserFullName($this->loggedUser);
        $admin->user->notify(new AdminAccountUpdated($superAdminName, $admin, 'activated'));
        return $admin;
    }

    public function deactivate(Admin $admin)
    {
        $admin->user()->update(['status' => '0']);
        $superAdminName = $this->getUserFullName($this->loggedUser);
        $admin->user->notify(new AdminAccountUpdated($superAdminName, $admin, 'deactivated'));
        return $admin;
    }

    public function changePassword($data, Admin $admin)
    {
        $admin->user()->update([
            'password' => bcrypt($data['password'])
        ]);
        $superAdminName = $this->getUserFullName($this->loggedUser);
        $admin->user->notify(new AdminAccountUpdated($superAdminName, $admin, 'updated the password'));
        return $admin;
    }

    public function destroy(Admin $admin)
    {
        $this->deleteImage($admin->user->foto);

        $admin->user->roles()->detach();
        $admin->user()->delete();
        $superAdminName = $this->getUserFullName($this->loggedUser);
        $admin->user->notify(new AdminAccountCreatedDeleted($superAdminName, $admin, 'deleted'));

        $admin->delete();
        return $admin;
    }
}
