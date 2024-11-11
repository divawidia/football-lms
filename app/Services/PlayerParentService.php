<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use App\Notifications\PlayerManagements\PlayerParent;
use App\Notifications\PlayerManagements\PlayerParentAdmin;
use App\Repository\AdminRepository;
use App\Repository\PlayerRepository;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use Yajra\DataTables\Facades\DataTables;

class PlayerParentService extends Service
{
    private $loggedUser;
    private UserRepository $userRepository;
    public function __construct($loggedUser, UserRepository $userRepository)
    {
        $this->loggedUser = $loggedUser;
        $this->userRepository = $userRepository;
    }
    public function makeDatatables($data, Player $player)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($player) {
                    return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('player-parents.edit', ['player'=>$player->id,'parent'=>$item->id]) . '"><span class="material-icons">edit</span> Edit Parent/Guardian</a>
                            <button type="button" class="dropdown-item delete-parent" id=' . $item->id . '>
                                <span class="material-icons">delete</span> Delete Parent/Guardian
                            </button>
                          </div>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make();
    }
    public function index(Player $player): JsonResponse
    {
        $query = PlayerParrent::where('playerId', $player->id)->get();
        return $this->makeDatatables($query, $player);
    }

    public  function store(array $data, Player $player){
        $data['playerId'] = $player->id;
        $parent = PlayerParrent::create($data);

        $superAdminName = $this->getUserFullName($this->loggedUser);

        Notification::send($this->userRepository->getAllAdminUsers(),new PlayerParentAdmin($superAdminName, $player, 'created'));
        $player->user->notify(new PlayerParent($parent, 'added'));
        return $parent;
    }
    public function update(array $data, PlayerParrent $parent)
    {
        $parent->update($data);

        $superAdminName = $this->getUserFullName($this->loggedUser);
        $player = $parent->player;

        Notification::send($this->userRepository->getAllAdminUsers(),new PlayerParentAdmin($superAdminName, $player, 'updated'));
        $player->user->notify(new PlayerParent($parent, 'updated'));
        return $parent;
    }
    public function destroy(PlayerParrent $parent)
    {
        $superAdminName = $this->getUserFullName($this->loggedUser);
        $player = $parent->player;

        $parent->delete();

        Notification::send($this->userRepository->getAllAdminUsers(),new PlayerParentAdmin($superAdminName, $player, 'deleted'));
        $player->user->notify(new PlayerParent($parent, 'deleted'));
        return $parent;
    }
}
