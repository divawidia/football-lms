<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\EventSchedule;
use App\Models\User;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use App\Services\AdminService;
use App\Services\DatatablesService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Nnjeim\World\World;

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
        $this->adminService->store($data, $this->getAcademyId());

        $text = $data['firstName'].' '.$data['lastName'].' account successfully added!';
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

        return response()->json([
            'status' => 200,
            'data' => $result,
            'message' => 'Successfully change password'
        ]);
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

        $text = $data['firstName'].' '.$data['lastName'].' account successfully updated!';
        Alert::success($text);
        return redirect()->route('admin-managements.index');
    }

    public function deactivate(Admin $admin)
    {
        try {
            $data = $this->adminService->setStatus($admin, '0');
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Admin '.$this->getUserFullName($admin->user).' account status successfully set to deactivated.',
                'data' => $data,
            ]);

        } catch (Exception $e){
            $message = 'Error while updating admin '.$this->getUserFullName($admin->user).' account status to deactivate: ' . $e->getMessage();
            Log::error($message);
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => $message,
                'data' => $data,
            ], 500);
        }
    }

    public function activate(Admin $admin){
        try {
            $data = $this->adminService->setStatus($admin, '1');
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Admin '.$this->getUserFullName($admin->user).' account status successfully set to activated.',
                'data' => $data,
            ]);

        } catch (Exception $e){
            $message = 'Error while updating admin '.$this->getUserFullName($admin->user).' account status to activate: ' . $e->getMessage();
            Log::error($message);
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => $message,
                'data' => $data,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $result = $this->adminService->destroy($admin);
        return response()->json([
            'status' => 200,
            'data' => $result,
            'message' => 'Successfully delete admin'
        ]);
    }
}
