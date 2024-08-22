<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

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
                          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons-outlined">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#"><span class="material-icons-outlined">edit</span> Edit Admin</a>
                            <form action="' . route('admin.destroy', $item->id) . '" method="POST"
                                <button type="submit" class="dropdown-item">
                                    <span class="material-icons-outlined">delete</span> Delete Admin
                                </button>
                            </form>
                          </div>
                        </div>';
                })
                ->editColumn('foto', function ($item) {
                    return $item->user->foto ? '<img class="rounded-circle header-profile-user" src="' . Storage::url($item->user->foto) . '"/>' : '';
                })
                ->editColumn('status', function ($item){
                    if ($item->user->status == '1') {
                        return '<span class="badge bg-success">Aktif</span>';
                    }elseif ($item->user->status == '0'){
                        return '<span class="badge bg-danger">Non Aktif</span>';
                    }
                })
                ->rawColumns(['action', 'foto','status'])
                ->make();
        }
        return view('pages.admin.kelola-pengguna.instruktur.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.kelola-pengguna.instruktur.create');
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
            $data['foto'] = 'assets/user-profile/avatar.png';
        }
        $data['status'] = '1';

        $user = User::create($data);
        $user->assignRole('admin');

        Admin::create([
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'position' => $data['position'],
            'status' => $data['status'],
            'user_id' => $user->id,
        ]);

        $text = 'Data admin berhasil ditambahkan!';

        Alert::success($text);
        return redirect()->route('admin.index')->with('status', $text);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $instruktur = Admin::findOrFail($id);

        return view('pages.admin.kelola-pengguna.instruktur.edit',[
            'instruktur' => $instruktur
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
        return redirect()->route('instruktur.index')->with('status', 'Data instruktur berhasil diupdate!');
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
        return redirect()->route('instruktur.index')->with('status', 'Data instruktur berhasil dihapus!');
    }
}
