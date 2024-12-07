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
    private DatatablesService $datatablesService;
    private $loggedUser;
    public function __construct(AdminRepository $adminRepository, UserRepository $userRepository, $loggedUser, DatatablesService $datatablesService)
    {
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
        $this->loggedUser = $loggedUser;
        $this->datatablesService = $datatablesService;
    }
    public function index(): JsonResponse
    {
        $query = $this->adminRepository->getAll();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if (isSuperAdmin()){
                    $deleteUserBtn = '';
                    $changePassBtn = '';
                    $statusButton = '';
                    $editAccBtn = '';
                    if (getLoggedUser()->id != $item->user->id) {
                        $editAccBtn = '<a class="dropdown-item" href="' . route('admin-managements.edit', $item->hash) . '"><span class="material-icons mr-2">edit</span> Edit Admin</a>';
                        $deleteUserBtn = '
                            <button type="submit" class="dropdown-item deleteAdmin" id="'.$item->hash.'">
                                <span class="material-icons mr-2 text-danger">delete</span> Delete Admin
                            </button>';
                        $changePassBtn = '<a class="dropdown-item changePassword" id="'.$item->hash.'"><span class="material-icons mr-2">lock</span> Change Admin Password</a>';
                    }

                    if ($item->user->status == '1' && getLoggedUser()->id != $item->user->id){
                        $statusButton = '<button type="submit" class="dropdown-item setDeactivate" id="'.$item->hash.'">
                                                <span class="material-icons text-danger">check_circle</span>
                                                Deactivate Admin
                                        </button>';
                    }elseif($item->user->status == '0' && getLoggedUser()->id != $item->user->id) {
                        $statusButton = '<button type="submit" class="dropdown-item setActivate" id="'.$item->hash.'">
                                                <span class="material-icons text-success">check_circle</span>
                                                Activate Admin
                                        </button>';
                    }
                    return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('admin-managements.show', $item->hash) . '"><span class="material-icons mr-2">visibility</span> View Admin</a>
                            '.$editAccBtn.'
                            '. $statusButton .'
                            '.$changePassBtn.'
                            '.$deleteUserBtn.'
                          </div>
                        </div>';
                } elseif (isAdmin()){
                    return '<a class="btn btn-sm btn-outline-secondary" href="' . route('admin-managements.show', $item->hash) . '"><span class="material-icons">visibility</span></a>';
                }
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position, route('admin-managements.show', $item->hash));
            })
            ->editColumn('status', function ($item){
                return $this->datatablesService->activeNonactiveStatus($item->user->status);
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

    public function setStatus(Admin $admin, $status)
    {
        $admin->user()->update(['status' => $status]);
        $superAdminName = $this->getUserFullName($this->loggedUser);

        if ($status == '1') {
            $message = 'activated';
        } elseif ($status == '0') {
            $message = 'deactivated';
        }

        $admin->user->notify(new AdminAccountUpdated($superAdminName, $admin, $message));
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

        Notification::send($this->userRepository->getAllAdminUsers(),new AdminAccountCreatedDeleted($superAdminName, $admin, 'deleted'));

        $admin->delete();
        return $admin;
    }
}
