<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Services\OpponentTeamService;
use App\Services\TeamService;
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

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private TeamService $teamService;
    private $academyId;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
        $this->academyId = Auth::user()->academyId;
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->teamService->index();
        }
        return view('pages.admins.managements.teams.index');
    }

    public function teamPlayers(Team $team){
        if (request()->ajax()) {
            $query = $team->players()->get();
            return Datatables::of($query)
                ->addColumn('action', function ($item) {
                    return '
                                <div class="dropdown">
                                  <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="material-icons">
                                        more_vert
                                    </span>
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="' . route('player-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Player</a>
                                    <a class="dropdown-item" href="' . route('player-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Player</a>
                                    <button type="button" class="dropdown-item remove-player" id="' . $item->id . '">
                                        <span class="material-icons">delete</span> Remove Player From Team
                                    </button>
                                  </div>
                                </div>';
                })
                ->editColumn('age', function ($item){
                    return $this->getAge($item->user->dob);
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
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' '.$item->user->lastName.'</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                })
                ->editColumn('appearance', function ($item) {
                    return 0;
                })
                ->editColumn('goals', function ($item) {
                    return 0;
                })
                ->editColumn('assists', function ($item) {
                    return 0;
                })
                ->editColumn('cleanSheets', function ($item) {
                    return 0;
                })
                ->rawColumns(['action', 'name', 'age', 'appearance', 'goals', 'assists','cleanSheets'])
                ->make();
        }
    }

    public function teamCoaches(Team $team){
        if (request()->ajax()) {
            $query = $team->coaches()->get();
            return Datatables::of($query)
                ->addColumn('action', function ($item) {
                    return '
                                <div class="dropdown">
                                  <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="material-icons">
                                        more_vert
                                    </span>
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="' . route('coach-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Coach</a>
                                    <a class="dropdown-item" href="' . route('coach-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Coach</a>
                                    <button type="button" class="dropdown-item remove-coach" id="' . $item->id . '">
                                        <span class="material-icons">delete</span> Remove Coach From Team
                                    </button>
                                  </div>
                                </div>';
                })
                ->editColumn('age', function ($item){
                    return $this->getAge($item->user->dob);
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
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' '.$item->user->lastName.'</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->specializations->name . ' - '.$item->certification->name.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                })
                ->editColumn('joinedDate', function ($item) {
                    return date('l, M d, Y. h:i A', strtotime($item->pivot->created_at));
                })
                ->editColumn('gender', function ($item) {
                    return $item->user->gender;
                })
                ->rawColumns(['action', 'name', 'age', 'gender','joinedDate'])
                ->make();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $players = Player::all();
        $coaches = Coach::all();

        return view('pages.admins.managements.teams.create', [
            'players' => $players,
            'coaches' => $coaches
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamRequest $request)
    {
        $data = $request->validated();

        $this->teamService->store($data, $this->academyId);

        $text = 'Team '.$data['teamName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('team-managements.index');
    }

    public function apiStore(TeamRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $team = $this->teamService->store($data, $this->academyId);

            return response()->json($team, 201);
        }catch (\Illuminate\Validation\ValidationException $e){
            return response()->json($e->errors(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        return view('pages.admins.managements.teams.detail', [
            'team' => $team,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $players = Player::all();
        $coaches = Coach::all();
        $player_id = [];
        $coach_id = [];

        foreach ($team->players as $player){
            $player_id[] = $player->id;
        }

        foreach ($team->coaches as $coach){
            $coach_id[] = $coach->id;
        }

        return view('pages.admins.managements.teams.edit',[
            'team' => $team,
            'players' => $players,
            'coaches' => $coaches,
            'player_id' => $player_id,
            'coach_id' => $coach_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $data = $request->validated();

        $this->teamService->update($data, $team);

        $text = 'Team '.$team->teamName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('team-managements.show', $team->id);
    }

    public function deactivate(Team $team){
        $this->teamService->deactivate($team);

        Alert::success('Team '.$team->teamName.' status successfully deactivated!');
        return redirect()->route('team-managements.index');
    }

    public function activate(Team $team){
        $this->teamService->activate($team);

        Alert::success('Team '.$team->teamName.' status successfully activated!');
        return redirect()->route('team-managements.index');
    }

    public function editPlayerTeam(Team $team)
    {
        $players = Player::all();
        $player_id = [];

        foreach ($team->players as $player){
            $player_id[] = $player->id;
        }

        return view('pages.admins.managements.teams.editPlayer',[
            'team' => $team,
            'players' => $players,
            'player_id' => $player_id,
        ]);
    }

    public function updatePlayerTeam(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'players' => ['required', Rule::exists('players', 'id')]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $this->teamService->updatePlayerTeam((array)$validator, $team);

        $text = 'Team '.$team->teamName.' Players successfully updated!';
        Alert::success($text);
        return redirect()->route('team-managements.show', $team->id);
    }

    public function editCoachesTeam(Team $team)
    {
        $coaches = Coach::all();
        $coach_id = [];

        foreach ($team->coaches as $coach){
            $coach_id[] = $coach->id;
        }

        return view('pages.admins.managements.teams.editCoach',[
            'team' => $team,
            'coaches' => $coaches,
            'coach_id' => $coach_id,
        ]);
    }

    public function updateCoachTeam(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'coaches' => ['required', Rule::exists('coaches', 'id')]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $this->teamService->updateCoachTeam((array)$validator, $team);

        $text = 'Team '.$team->teamName.' Coaches successfully updated!';
        Alert::success($text);
        return redirect()->route('team-managements.show', $team->id);
    }

    public function removePlayer(Team $team, Player $player)
    {
        $this->teamService->removePlayer($team, $player);

        return response()->json(['success' => true]);
    }

    public function removeCoach(Team $team, Coach $coach)
    {
        $this->teamService->removeCoach($team, $coach);
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
       $this->teamService->destroy($team);

        return response()->json(['success' => true]);
    }
}
