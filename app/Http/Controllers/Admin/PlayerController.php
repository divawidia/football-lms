<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerRequest;
use App\Models\Admin;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use App\Services\PlayerService;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nnjeim\World\World;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use function PHPUnit\Framework\isEmpty;

class PlayerController extends Controller
{
    private PlayerService $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.admins.managements.players.index');
    }

    public function adminPlayerIndex(): JsonResponse
    {
        return $this->playerService->index();
    }

    public function coachPlayerIndex()
    {
        return $this->playerService->coachPlayerIndex($this->getLoggedCoachUser());
    }

    public function playerTeams(User $player)
    {
        if (request()->ajax()) {
            return $this->playerService->playerTeams($player->player);
        }
    }

    public function removeTeam(User $player, Team $team)
    {
        return $this->playerService->removeTeam($player->player, $team);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->playerService->create();

        return view('pages.admins.managements.players.create', [
            'countries' => $data['countries'],
            'positions' => $data['positions'],
            'teams' => $data['teams']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlayerRequest $request)
    {
        $data = $request->validated();
        $this->playerService->store($data, Auth::user()->academyId);

        $text = $data['firstName'].' account successfully added!';
        Alert::success($text);
        return redirect()->route('player-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
        $overview = $this->playerService->show($player);
        return view('pages.coaches.managements.players.detail', [
            'data' => $player,
            'overview' => $overview
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $player_management)
    {
        $fullname = $player_management->firstName . ' ' . $player_management->lastName;
        $positions = PlayerPosition::all();
        $teams = Team::all();
        $action =  World::countries();
        if ($action->success) {
            $countries = $action->data;
        }
        return view('pages.admins.managements.players.edit',[
            'player' => $player_management,
            'fullname' => $fullname,
            'positions' => $positions,
            'teams' => $teams,
            'countries' => $countries
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlayerRequest $request, User $player_management)
    {
        $data = $request->validated();

        $this->playerService->update($data, $player_management);

        $text = $player_management->firstName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('player-managements.show', $player_management->id);
    }

    public function updateTeams(Request $request, Player $player)
    {
        $validator = Validator::make($request->all(), [
            'teams' => ['required', Rule::exists('teams', 'id')]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()->toArray()
            ]);
        }else{
            $player = $this->playerService->updateTeams($validator->getData()['teams'], $player);
            return response()->json($player, 204);
        }
    }

    public function deactivate(User $player){
        $this->playerService->deactivate($player);

        Alert::success($player->firstName.' account status successfully deactivated!');
        return redirect()->route('player-managements.index');
    }

    public function activate(User $player){
        $this->playerService->activate($player);
        Alert::success($player->firstName.' account status successfully activated!');
        return redirect()->route('player-managements.index');
    }

    public function changePasswordPage(User $player){
        $fullName = $player->firstName . ' ' . $player->lastName;

        return view('pages.admins.managements.players.change-password',[
            'user' => $player,
            'fullName' => $fullName
        ]);
    }

    public function changePassword(Request $request, User $player){
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $this->playerService->changePassword($validator->getData()['password'], $player);
        Alert::success($player->firstName.' account password successfully updated!');
        return redirect()->route('player-managements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $player_management)
    {
        $this->playerService->destroy($player_management);

        return response()->json(['success' => true]);
    }
}
