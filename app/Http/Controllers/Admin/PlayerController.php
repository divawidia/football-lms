<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerRequest;
use App\Models\Admin;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use function PHPUnit\Framework\isEmpty;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Player::with('user', 'teams', 'position')->get();
            return Datatables::of($query)
                ->addColumn('action', function ($item) {
                    if ($item->user->status == '1'){
                        $statusButton = '<form action="' . route('deactivate-player', $item->userId) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">block</span> Deactivate Player</a>
                                            </button>
                                        </form>';
                    }else{
                        $statusButton = '<form action="' . route('activate-player', $item->userId) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">check_circle</span> Activate Player</a>
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
                            <a class="dropdown-item" href="' . route('player-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Player</a>
                            <a class="dropdown-item" href="' . route('player-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Player</a>
                            '. $statusButton .'
                            <a class="dropdown-item" href="' . route('player-managements.change-password-page', $item->userId) . '"><span class="material-icons">lock</span> Change Player Password</a>
                            <form action="' . route('admin-managements.destroy', $item->userId) . '" method="POST" id="delete-'.$item->userId.'">
                                '.method_field("DELETE").'
                                '.csrf_field().'
                                <button type="submit" class="dropdown-item delete-user" id="'.$item->userId.'">
                                    <span class="material-icons">delete</span> Delete Player
                                </button>
                            </form>
                          </div>
                        </div>';
                })
                ->editColumn('teams.name', function ($item) {
                        if(count($item->teams) === 0){
                            $team = 'No Team';
                        }else{
                            $team = $item->teams->name;
                        }
                        return $team;
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
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
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
                ->rawColumns(['action', 'name','status', 'age', 'teams.name'])
                ->make();
        }
        return view('pages.admins.managements.players.index');
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

        $positions = PlayerPosition::all();

        return view('pages.admins.managements.players.create', [
            'countries' => $countries,
            'positions' => $positions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlayerRequest $request)
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
        $user->assignRole('player');

        $data['userId'] = $user->id;

        Player::create($data);

        $text = $data['firstName'].' account successfully added!';

        Alert::success($text);
        return redirect()->route('player-managements.index');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
