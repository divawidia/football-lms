<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Notifications\PlayerParent\Admin\ParentCreatedForAdminNotification;
use App\Notifications\PlayerParent\Admin\ParentDeletedForAdminNotification;
use App\Notifications\PlayerParent\Admin\ParentUpdatedForAdmin;
use App\Notifications\PlayerParent\Player\ParentCreatedForPlayerNotification;
use App\Notifications\PlayerParent\Player\ParentDeletedForPlayer;
use App\Notifications\PlayerParent\Player\ParentUpdatedForPlayer;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class PlayerParentService extends Service
{
    private UserRepository $userRepository;
    private DatatablesHelper $datatablesHelper;
    public function __construct(UserRepository $userRepository, DatatablesHelper $datatablesHelper)
    {
        $this->userRepository = $userRepository;
        $this->datatablesHelper = $datatablesHelper;
    }
    public function makeDatatables($data, Player $player)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($player) {
                $dropdownItem = $this->datatablesHelper->linkDropdownItem(route: route('player-managements.player-parents.edit', ['player'=>$player->hash,'parent'=>$item->hash]), icon: 'visibility', btnText: 'Edit player parent/guardian');
                $dropdownItem .= $this->datatablesHelper->buttonDropdownItem('delete-parent', $player->hash, 'danger', icon: 'delete', btnText: 'Delete Player parent/guardian');
                return $this->datatablesHelper->dropdown(function () use ($dropdownItem) {
                    return $dropdownItem;
                });
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make();
    }
    public function index(Player $player): JsonResponse
    {
        $query = $player->parents;
        return $this->makeDatatables($query, $player);
    }

    public  function store(array $data, Player $player, $loggedUser){
        $parent = $player->parents()->create($data);

        Notification::send($this->userRepository->getAllAdminUsers(),new ParentCreatedForAdminNotification($loggedUser, $player));
        $player->user->notify(new ParentCreatedForPlayerNotification());

        return $parent;
    }
    public function update(array $data, PlayerParrent $parent, $loggedUser)
    {
        $parent->update($data);
        $player = $parent->player;

        Notification::send($this->userRepository->getAllAdminUsers(),new ParentUpdatedForAdmin($loggedUser, $player));
        $player->user->notify(new ParentUpdatedForPlayer());
        return $parent;
    }
    public function destroy(PlayerParrent $parent, $loggedUser)
    {
        $player = $parent->player;
        $parent->delete();

        Notification::send($this->userRepository->getAllAdminUsers(),new ParentDeletedForAdminNotification($loggedUser, $player));
        $player->user->notify(new ParentDeletedForPlayer());
        return $parent;
    }
}
