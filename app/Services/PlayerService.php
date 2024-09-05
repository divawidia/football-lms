<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\OpponentTeam;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class PlayerService extends Service
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $query = Player::with('user', 'teams', 'position')->get();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if ($item->user->status == '1'){
                    $statusButton = '<form action="' . route('deactivate-player', $item->userId) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">block</span> Deactivate Player
                                            </button>
                                        </form>';
                }else{
                    $statusButton = '<form action="' . route('activate-player', $item->userId) . '" method="POST">
                                            '.method_field("PATCH").'
                                            '.csrf_field().'
                                            <button type="submit" class="dropdown-item">
                                                <span class="material-icons">check_circle</span> Activate Player
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
                            <button type="button" class="dropdown-item delete-user" id="' . $item->userId . '">
                                <span class="material-icons">delete</span> Delete Player
                            </button>
                          </div>
                        </div>';
            })
            ->editColumn('teams.name', function ($item) {
                $playerTeam = '';
                if(count($item->teams) === 0){
                    $playerTeam = 'No Team';
                }else{
                    foreach ($item->teams as $team){
                        $playerTeam .= '<span class="badge badge-pill badge-danger">'.$team->teamName.'</span>';
                    }
                }
                return $playerTeam;
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
                return $this->getAge($item->user->dob);
            })
            ->rawColumns(['action', 'name','status', 'age', 'teams.name'])
            ->make();
    }

    public function playerTeams(Player $player): \Illuminate\Http\JsonResponse
    {
        $query = Player::with('teams')->findOrFail($player->id);
        dd($query);
        return Datatables::of($query->teams)
            ->addColumn('action', function ($item) {
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
                            <a class="dropdown-item" href="' . route('player-managements.change-password-page', $item->userId) . '"><span class="material-icons">lock</span> Change Player Password</a>
                            <button type="button" class="dropdown-item delete-user" id="' . $item->userId . '">
                                <span class="material-icons">delete</span> Delete Player
                            </button>
                          </div>
                        </div>';
            })
            ->editColumn('name', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->division . ' - '.$item->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('date', function ($item){
                return date('M d, Y', strtotime($item->pivot->created_at));
            })
            ->rawColumns(['action', 'name','date'])
            ->make();
    }

    public  function store(array $playerData, $academyId){

        $playerData['password'] = bcrypt($playerData['password']);

        if (array_key_exists('foto', $playerData)){
            $playerData['foto'] = $playerData['foto']->store('assets/user-profile', 'public');
        }else{
            $playerData['foto'] = 'images/undefined-user.png';
        }

        $playerData['status'] = '1';
        $playerData['academyId'] = $academyId;

        $user = User::create($playerData);
        $user->assignRole('player');

        $playerData['userId'] = $user->id;

        $player = Player::create($playerData);

        if ($playerData['team'] != null){
            $player->teams()->attach($playerData['team']);
        }

        PlayerParrent::create([
            'firstName' => $playerData['firstName'],
            'lastName' => $playerData['lastName'],
            'relations' => $playerData['relations'],
            'email' => $playerData['email'],
            'phoneNumber' => $playerData['phoneNumber'],
            'playerId' => $player->id,
        ]);
        return $player;
    }

    public function update(array $playerData, User $user): User
    {
        if (array_key_exists('foto', $playerData)){
            $this->deleteImage($user->foto);
            $playerData['foto'] = $playerData['foto']->store('assets/player-logo', 'public');
        }else{
            $playerData['foto'] = $user->foto;
        }

        $user->update($playerData);
        $user->player->update($playerData);

        return $user;
    }
    public function activate(User $user): User
    {
        $user->update(['status' => '1']);
        return $user;
    }

    public function deactivate(User $user): User
    {
        $user->update(['status' => '0']);
        return $user;
    }

    public function changePassword($data, User $user){
        $user->update([
            'password' => bcrypt($data)
        ]);
        return $user;
    }

    public function destroy(User $user): User
    {
        $this->deleteImage($user->foto);
        $user->player->delete();
        $user->delete();
        return $user;
    }
}
