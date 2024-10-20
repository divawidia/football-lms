<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use App\Models\User;
use App\Services\AdminService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
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

    public function changePassword(Request $request, string $id){
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::findOrFail($id);

        $user->update([
            'password' => bcrypt($validator->getData()['password'])
        ]);
        Alert::success($user->firstName.' account password successfully updated!');
        return redirect()->route('admin-managements.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('admin')->findOrFail($id);
        $fullName = $user->firstName . ' ' . $user->lastName;
        $action =  World::countries();
        if ($action->success) {
            $countries = $action->data;
        }

        return view('pages.admins.managements.admins.edit',[
            'user' => $user,
            'fullName' => $fullName,
            'countries' => $countries
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, User $admin_management)
    {
        $data = $request->validated();
        $user = User::findOrFail($admin_management->id);
        $admin = Admin::findOrFail($admin_management->admin->id);

        if ($request->hasFile('foto')){
            $data['foto'] = $data['foto']->store('assets/user-profile', 'public');
        }

        $user->update($data);

        $admin->update($data);
        $text = $user->firstName.' account successfully updated!';

        Alert::success($text);
        return redirect()->route('admin-managements.index');
    }

    public function deactivate(string $id){
        $user = User::findOrFail($id);
        $user->update([
            'status' => '0'
        ]);
        Alert::success($user->firstName.' account status successfully deactivated!');
        return redirect()->route('admin-managements.index');
    }

    public function activate(string $id){
        $user = User::findOrFail($id);
        $user->update([
            'status' => '1'
        ]);
        Alert::success($user->firstName.' account status successfully activated!');
        return redirect()->route('admin-managements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::with('admin')->findOrFail($id);

        if (File::exists($user->foto) && $user->foto != 'images/undefined-user.png'){
            File::delete($user->foto);
        }

        $user->admin->delete();
        $user->delete();
        $user->roles()->detach();

        Alert::success($user->firstName.' account successfully deleted!');
        return redirect()->route('admin-managements.index');
    }
}
