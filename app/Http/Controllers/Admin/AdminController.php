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
                    return '
                        <div class="dropdown">
                          <button class="btn btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#"><span class="material-icons">edit</span> Edit Admin</a>
                            <a class="dropdown-item" href="' . route('admin-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Admin</a>
                            <a class="dropdown-item" href="#"><span class="material-icons">block</span> Deactivate Admin</a>
                            <a class="dropdown-item" href="#"><span class="material-icons">lock</span> Change Admin Password</a>
                            <form action="' . route('admin-managements.destroy', $item->id) . '" method="POST"
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

        $text = 'Data admin berhasil ditambahkan!';

        Alert::success($text);
        return redirect()->route('admin-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::with('user.country', 'user.state', 'user.city')->findOrFail($id);
        $fullName = $admin->user->firstName . ' ' . $admin->user->lastName;

        dd($admin->user->country);

        return view('pages.admins.managements.admins.detail', [
            'admin' => $admin,
            'fullName' => $fullName
        ]);
    }

    public function changePassword(string $id){
        $admin = Admin::with('user')->findOrFail($id);

        return view('pages.admin.kelola-pengguna.instruktur.edit-password',[
            'admin' => $admin
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $admin = Admin::with('user')->findOrFail($id);

        return view('pages.admin.kelola-pengguna.instruktur.edit',[
            'admin' => $admin
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, Admin $admin)
    {
        $data = $request->validated();
        $user = User::findOrFail($admin->user_id);
        $admin = Admin::findOrFail($admin->id);

        if ($request->password != null){
            $data['password'] = bcrypt($data['password']);
        }else{
            $data['password'] = $user->password;
        }

        if ($request->status == null){
            $data['status'] = '0';
        }

        if ($request->hasFile('foto')){
            $data['foto'] = $data['foto']->store('assets/user-profile', 'public');
        }

        $user->update([
            'name' => $data['nama'],
            'email' => $data['email'],
            'status' => $data['status'],
            'password' => $data['password']
        ]);

        $admin->update([
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'position' => $data['position'],
            'status' => $data['status'],
            'user_id' => $user->id,
        ]);

        Alert::success('Data instruktur berhasil diupdate!');
        return redirect()->route('admin-managements.index')->with('status', 'Data instruktur berhasil diupdate!');
    }

    public function deactivate(string $id){
        $admin = Admin::with('user')->findOrFail($id);
        $user = User::findOrFail($admin->user_id);
        $user->update([
            'status' => '0'
        ]);
        Alert::success('Admin status successfully deactivated!');
        return redirect()->route('admin-managements.index');
    }

    public function activate(string $id){
        $admin = Admin::with('user')->findOrFail($id);
        $user = User::findOrFail($admin->user_id);
        $user->update([
            'status' => '1'
        ]);
        Alert::success('Admin status successfully activated!');
        return redirect()->route('admin-managements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $admin = Admin::with('user')->findOrFail($admin->id);

        if (File::exists($admin->user->foto) && $admin->user->foto != 'assets/user-profile/avatar.png'){
            File::delete($admin->user->foto);
        }

        $admin->delete();
        $admin->user->roles()->detach();
        $admin->user->delete();

        Alert::success('Data instruktur berhasil dihapus!');
        return redirect()->route('admin-managements.index')->with('status', 'Data instruktur berhasil dihapus!');
    }
}
