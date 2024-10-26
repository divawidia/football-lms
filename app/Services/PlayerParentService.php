<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use Yajra\DataTables\Facades\DataTables;

class PlayerParentService extends Service
{
    public function makeDatatables($data, Player $player)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($player) {
                if (isAllAdmin()){
                    return '
                        <div class="dropdown">
                          <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('player-parents.edit', ['player'=>$player->id,'parent'=>$item->id]) . '"><span class="material-icons">edit</span> Edit Parent/Guardian</a>
                            <button type="button" class="dropdown-item delete-parent" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Parent/Guardian
                            </button>
                          </div>
                        </div>';
                }
            })
            ->rawColumns(['action'])
            ->make();
    }
    public function index(Player $player): JsonResponse
    {
        $query = PlayerParrent::where('playerId', $player->id)->get();
        return $this->makeDatatables($query, $player);
    }

    public  function store(array $data, $playerId){
        $data['playerId'] = $playerId;
        return PlayerParrent::create($data);
    }
    public function update(array $data, PlayerParrent $parent)
    {
        return $parent->update($data);
    }
    public function destroy(PlayerParrent $parent)
    {
        return $parent->delete();
    }
}
