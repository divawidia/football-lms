<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use App\Models\User;
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
    public function index()
    {
        if (request()->ajax()) {
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
                          <button class="btn btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('admin-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Admin</a>
                            <a class="dropdown-item" href="' . route('admin-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Admin</a>
                            '. $statusButton .'
                            <a class="dropdown-item" href="' . route('admin-managements.change-password-page', $item->userId) . '"><span class="material-icons">lock</span> Change Admin Password</a>
                            <form action="' . route('admin-managements.destroy', $item->userId) . '" method="POST">
                                '.method_field("DELETE").'
                                '.csrf_field().'
                                <button type="submit" class="dropdown-item">
                                    <span class="material-icons">delete</span> Delete Admin
                                </button>
                            </form>
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
                                        <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->position . '</small>
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
                    $dob = new DateTime($item->user->dob);
                    $today   = new DateTime('today');
                    $age = $dob->diff($today)->y;
                    return $age;
                })
                ->rawColumns(['action', 'name','status', 'age'])
                ->make();
        }

        return view('pages.admins.managements.admins.index');
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
        return view('pages.admins.managements.admins.create', [
            'countries' => $countries
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
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
        $user->assignRole('admin');

        Admin::create([
            'position' => $data['position'],
            'hireDate' => $data['hireDate'],
            'userId' => $user->id,
        ]);

        $text = $data['firstName'].' account successfully added!';

        Alert::success($text);
        return redirect()->route('admin-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('country', 'state', 'city', 'admin')->findOrFail($id);
        $fullName = $user->firstName . ' ' . $user->lastName;

        return view('pages.admins.managements.admins.detail', [
            'user' => $user,
            'fullName' => $fullName
        ]);
    }

    public function changePasswordPage(string $id){
        $user = User::findOrFail($id);
        $fullName = $user->firstName . ' ' . $user->lastName;

        return view('pages.admins.managements.admins.change-password',[
            'user' => $user,
            'fullName' => $fullName
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

        if (File::exists($user->foto) && $user->foto != 'assets/user-profile/avatar.png'){
            File::delete($user->foto);
        }

        $user->admin->delete();
        $user->delete();
        $user->roles()->detach();

        Alert::success($user->firstName.' account successfully deleted!');
        return redirect()->route('admin-managements.index');
    }
}
