<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerParentRequest;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\User;
use App\Repository\UserRepository;
use App\Services\PlayerParentService;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class PlayerParentController extends Controller
{
    private PlayerParentService $playerParentService;

    public function __construct(UserRepository $userRepository)
    {
        $this->middleware(function ($request, $next) use ($userRepository){
            $this->playerParentService = new PlayerParentService($this->getLoggedUser(), $userRepository, );
            return $next($request);
        });
    }

    public function index(Player $player)
    {

        return $this->playerParentService->index($player);
    }

    public function create(Player $player)
    {
        return view('pages.managements.players.player-parents.create', [
            'data' => $player,
            'fullName' => $this->playerParentService->getUserFullName($player->user)
        ]);
    }

    public function store(PlayerParentRequest $request, Player $player)
    {
        $data = $request->validated();
        $this->playerParentService->store($data, $player);

        $text = "Player's parent/guardian successfully added!";
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
        $this->playerParentService->update($data, $parent);
        $text = $data['firstName'].' '.$data['lastName'].' successfully updated!';
        Alert::success($text);
        return redirect()->route('player-managements.show', $player->id);
    }

    public function destroy(Player $player, PlayerParrent $parent)
    {
        $result = $this->playerParentService->destroy($parent);

        return response()->json([
            'status' => 200,
            'data' => $result,
            'message' => 'Successfully deleted players parent'
        ]);
    }
}
