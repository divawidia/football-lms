<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerParentRequest;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Services\PlayerParentService;
use Exception;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class PlayerParentController extends Controller
{
    private PlayerParentService $playerParentService;

    public function __construct(PlayerParentService $playerParentService)
    {
        $this->playerParentService = $playerParentService;
    }

    public function index(Player $player)
    {

        return $this->playerParentService->index($player);
    }

    public function create(Player $player)
    {
        return view('pages.managements.players.player-parents.create', [
            'data' => $player,
        ]);
    }

    public function store(PlayerParentRequest $request, Player $player)
    {
        $data = $request->validated();
        $this->playerParentService->store($data, $player, $this->getLoggedUser());

        $text = "Player ".$this->getUserFullName($player->user)."'s parent/guardian successfully added!";
        Alert::success($text);
        return redirect()->route('player-managements.show', $player->id);
    }

    public function edit(Player $player, PlayerParrent $parent)
    {
        return view('pages.managements.players.player-parents.edit', [
            'parent' => $parent,
            'player' => $player,
            'fullName' => $this->playerParentService->getUserFullName($player->user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlayerParentRequest $request, Player $player, PlayerParrent $parent)
    {
        $data = $request->validated();
        $this->playerParentService->update($data, $parent, $this->getLoggedUser());
        $text = "Player ".$this->getUserFullName($player->user)."'s parent/guardian successfully updated!";
        Alert::success($text);
        return redirect()->route('player-managements.show', $player->id);
    }

    public function destroy(Player $player, PlayerParrent $parent)
    {
        try {
            $data = $this->playerParentService->destroy($parent, $this->getLoggedUser());
            $message = "Player ".$this->getUserFullName($player->user)."'s parent/guardian successfully deleted.";
            return ApiResponse::success($data, $message);

        } catch (Exception $e){
            $message = "Error while deleting player ".$this->getUserFullName($player->user)."'s parent/guardian: " . $e->getMessage();
            Log::error($message);
            return ApiResponse::error($message, null, $e->getCode());
        }
    }
}
