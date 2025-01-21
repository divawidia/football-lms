<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Admin;
use App\Notifications\AdminManagement;
use App\Repository\Interface\AdminRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class AdminService extends Service
{
    private AdminRepositoryInterface $adminRepository;
    private UserRepositoryInterface $userRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(
        AdminRepositoryInterface $adminRepository,
        UserRepositoryInterface $userRepository,
        DatatablesHelper $datatablesHelper
    )
    {
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
        $this->datatablesHelper = $datatablesHelper;
    }
    public function index(): JsonResponse
    {
        $query = $this->adminRepository->getAll(withRelation: ['user:id,firstName,lastName,status,foto,dob,email,phoneNumber,gender']);
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if (isSuperAdmin()){
                    $dropdownItem = '';
                    if (getLoggedUser()->id != $item->user->id) {
                        $dropdownItem .=  $this->datatablesHelper->linkDropdownItem(route: route('admin-managements.edit', $item->hash), icon: 'edit', btnText: 'Edit Admin Profile');
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('deleteAdmin', $item->hash, 'danger', icon: 'delete', btnText: 'Delete Admin Profile');
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('changePassword', $item->hash, icon: 'lock', btnText: 'Change Admin Account Password');
                    }

                    if ($item->user->status == '1' && getLoggedUser()->id != $item->user->id){
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setDeactivate', $item->hash, 'danger', icon: 'check_circle', btnText: 'Deactivate Admin Account');
                    }
                    elseif($item->user->status == '0' && getLoggedUser()->id != $item->user->id) {
                        $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('setActivate', $item->hash, 'success', icon: 'check_circle', btnText: 'Activate Admin Account');
                    }
                    return $this->datatablesHelper->dropdown(function () use ($dropdownItem, $item) {
                        return $this->datatablesHelper->linkDropdownItem(route: route('admin-managements.show', $item->hash), icon: 'visibility', btnText: 'View Admin Profile') .$dropdownItem;
                    });
                }
                else {
                    return $this->datatablesHelper->buttonTooltips(route('admin-managements.show', $item->hash), 'View Admin Profile', 'visibility');
                }
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position, route('admin-managements.show', $item->hash));
            })
            ->editColumn('status', function ($item){
                return $this->datatablesHelper->activeNonactiveStatus($item->user->status);
            })
            ->editColumn('age', function ($item){
                return $this->getAge($item->user->dob);
            })
            ->rawColumns(['action', 'name','status'])
            ->addIndexColumn()
            ->make();
    }

    public function countAllAdmin()
    {
        return $this->adminRepository->getAll(retrievalMethod: 'count');
    }
    public function countNewAdminThisMonth()
    {
        return $this->adminRepository->getAll(thisMonth: true, retrievalMethod: 'count');
    }

    public  function store(array $data, $academyId, Admin $loggedAdmin)
    {
        $data['foto'] = $this->storeImage($data, 'foto', 'assets/user-profile', 'images/undefined-user.png');
        $data['password'] = bcrypt($data['password']);
        $data['status'] = '1';
        $data['academyId'] = $academyId;

        $user = $this->userRepository->createUserWithRole($data, 'admin');

        $data['userId'] = $user->id;
        $admin = $this->adminRepository->create($data);

        Notification::send($this->userRepository->getAllAdminUsers(),new AdminManagement($loggedAdmin, 'created', $admin));
        return $admin;
    }

    public function update(array $data, Admin $admin, Admin $loggedAdmin): Admin
    {
        $data['foto'] = $this->updateImage($data, 'foto', 'assets/user-profile', $admin->user->foto);
        $this->adminRepository->update($data, $admin);
        Notification::send($this->userRepository->getAllAdminUsers(),new AdminManagement($loggedAdmin, 'updated', $admin));
        return $admin;
    }

    public function setStatus(Admin $admin, $status, Admin $loggedAdmin)
    {
        ($status == '1') ?  $statusNotification = 'actived' : $statusNotification = 'deactivated';
        Notification::send($this->userRepository->getAllAdminUsers(),new AdminManagement($loggedAdmin, $statusNotification, $admin));
        return $this->userRepository->updateUserStatus($admin, $status);
    }

    public function changePassword($data, Admin $admin, Admin $loggedAdmin)
    {
        Notification::send($this->userRepository->getAllAdminUsers(),new AdminManagement($loggedAdmin, 'password', $admin));
        return $this->userRepository->changePassword($data, $admin);
    }

    public function destroy(Admin $admin, Admin $loggedAdmin)
    {
        $this->deleteImage($admin->user->foto);
        Notification::send($this->userRepository->getAllAdminUsers(),new AdminManagement($loggedAdmin, 'deleted', $admin));
        return $this->userRepository->delete($admin);
    }
}
