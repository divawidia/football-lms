<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlayerRequest;
use App\Models\Player;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use App\Services\PlayerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nnjeim\World\World;
use RealRashid\SweetAlert\Facades\Alert;

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
        if (\request()->ajax()) {
            return $this->playerService->index();
        }
        return view('pages.managements.players.index');
    }

    public function coachIndex()
    {
        if (request()->ajax()) {
            return $this->playerService->coachPlayerIndex($this->getLoggedCoachUser());
        }
        return view('pages.managements.players.index');
    }

    public function playerTeams(User $player)
    {
        return $this->playerService->playerTeams($player->player);
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
        return view('pages.managements.players.create', [
            'countries' => $this->playerService->getCountryData(),
            'positions' => $this->playerService->getPlayerPosition(),
            'teams' => $this->playerService->getAcademyTeams(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlayerRequest $request)
    {
        $data = $request->validated();
        $this->playerService->store($data, Auth::user()->academyId);

        $text = $data['firstName'] . ' account successfully added!';
        Alert::success($text);
        return redirect()->route('player-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
        $overview = $this->playerService->show($player);
        $performanceReviews = $player->playerPerformanceReview;
        $playerSkillStats = $this->playerService->skillStatsChart($player);

        return view('pages.coaches.managements.players.detail', [
            'data' => $player,
            'overview' => $overview,
            'performanceReviews' => $performanceReviews,
            'playerSkillStats' => $playerSkillStats
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $player)
    {
        $fullname = $player->firstName . ' ' . $player->lastName;
        $positions = PlayerPosition::all();
        $teams = Team::all();
        $action = World::countries();
        if ($action->success) {
            $countries = $action->data;
        }
        return view('pages.admins.managements.players.edit', [
            'player' => $player,
            'fullname' => $fullname,
            'positions' => $positions,
            'teams' => $teams,
            'countries' => $countries
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlayerRequest $request, User $player)
    {
        $data = $request->validated();

        $this->playerService->update($data, $player);

        $text = $player->firstName . ' successfully updated!';
        Alert::success($text);
        return redirect()->route('player-managements.show', $player->player->id);
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
        } else {
            $player = $this->playerService->updateTeams($validator->getData()['teams'], $player);
            return response()->json($player, 204);
        }
    }

    public function deactivate(User $player)
    {
        $this->playerService->deactivate($player);

        Alert::success($player->firstName . ' account status successfully deactivated!');
        return redirect()->route('player-managements.index');
    }

    public function activate(User $player)
    {
        $this->playerService->activate($player);
        Alert::success($player->firstName . ' account status successfully activated!');
        return redirect()->route('player-managements.index');
    }

    public function changePasswordPage(Player $player)
    {
        return view('pages.admins.managements.players.change-password', [
            'data' => $player,
        ]);
    }

    public function changePassword(Request $request, User $player)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $this->playerService->changePassword($validator->getData()['password'], $player);
        Alert::success($player->firstName . ' account password successfully updated!');
        return redirect()->route('player-managements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $player)
    {
        $this->playerService->destroy($player);

        return response()->json(['success' => true]);
    }
}
