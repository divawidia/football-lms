<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use App\Services\AdminService;
use App\Services\DatatablesService;
use Exception;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
class AdminController extends Controller
{
    private AdminService $adminService;
    public function __construct(AdminRepository $adminRepository, UserRepository $userRepository, DatatablesService $datatablesService)
    {
        $this->middleware(function ($request, $next) use ($adminRepository, $userRepository, $datatablesService){
            $this->adminService = new AdminService($adminRepository, $userRepository, $this->getLoggedUser(), $datatablesService);
            return $next($request);
        });
    }
    public function index()
    {
        if (request()->ajax()) {
            return $this->adminService->index();
        }

        return view('pages.admins.managements.admins.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admins.managements.admins.create', [
            'countries' => $this->adminService->getCountryData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        $data = $request->validated();
        $admin = $this->adminService->store($data, $this->getAcademyId());

        $text = "Admin ".$this->getUserFullName($admin->user)."'s account successfully added!";
        Alert::success($text);
        return redirect()->route('admin-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return view('pages.admins.managements.admins.detail', [
            'data' => $admin,
            'fullName' => $this->adminService->getUserFullName($admin->user),
            'age' => $this->adminService->getAge($admin->user->dob)
        ]);
    }

    public function changePassword(ChangePasswordRequest $request, Admin $admin)
    {
        $data = $request->validated();
        $result = $this->adminService->changePassword($data, $admin);

        $message = "Admin ".$this->getUserFullName($admin->user)."'s account password successfully updated!";
        return ApiResponse::success($result, $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        return view('pages.admins.managements.admins.edit',[
            'data' => $admin,
            'fullName' => $this->adminService->getUserFullName($admin->user),
            'countries' => $this->adminService->getCountryData()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $data = $request->validated();
        $this->adminService->update($data, $admin);

        $text = "Admin ".$this->getUserFullName($admin->user)."'s account successfully updated!";
        Alert::success($text);
        return redirect()->route('admin-managements.index');
    }

    public function deactivate(Admin $admin)
    {
        try {
            $data = $this->adminService->setStatus($admin, '0');
            $message = "Admin ".$this->getUserFullName($admin->user)."'s account status successfully set to deactivated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = 'Error while updating admin '.$this->getUserFullName($admin->user).' account status to deactivate: ' . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    public function activate(Admin $admin){
        try {
            $data = $this->adminService->setStatus($admin, '1');
            $message = "Admin ".$this->getUserFullName($admin->user)."'s account status successfully set to activated.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = 'Error while updating admin '.$this->getUserFullName($admin->user).' account status to activate: ' . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        try {
            $data = $this->adminService->destroy($admin);
            $message = "Admin ".$this->getUserFullName($admin->user)."'s account successfully deleted.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = 'Error while deleting admin '.$this->getUserFullName($admin->user).' account: ' . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
}
