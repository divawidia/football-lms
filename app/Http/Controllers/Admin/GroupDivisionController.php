<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupDivisionRequest;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\GroupDivision;
use App\Models\Player;
use App\Models\Team;
use App\Services\GroupDivisionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class GroupDivisionController extends Controller
{
    private GroupDivisionService $groupDivisionService;
    public function __construct(GroupDivisionService $groupDivisionService)
    {
        $this->groupDivisionService = $groupDivisionService;
    }

    public function index(Competition $competition, GroupDivision $group){
        if (request()->ajax()){
            return $this->groupDivisionService->index($group);
        }
    }

    public function create(Competition $competition){
        $teams = Team::where('teamSide', 'Academy Team')
            ->whereDoesntHave('divisions', function (Builder $query) use ($competition) {
                $query->where('competitionId', $competition->id);
            })->get();

        $opponentTeams = Team::where('teamSide', 'Opponent Team')
            ->whereDoesntHave('divisions', function (Builder $query) use ($competition) {
                $query->where('competitionId', $competition->id);
            })->get();
        $players = Player::all();
        $coaches = Coach::all();


        return view('pages.admins.managements.competitions.groups.create', [
            'teams' => $teams,
            'opponentTeams' => $opponentTeams,
            'competition' => $competition,
            'players' => $players,
            'coaches' => $coaches
        ]);
    }

    public function store(GroupDivisionRequest $request, Competition $competition){
        $data = $request->validated();

        $this->groupDivisionService->store($data, $competition);

        $text = 'Division '.$data['groupName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }

    public function addTeam(Competition $competition, GroupDivision $group)
    {
        // get team data where teams are our academy team and added in the group division, this variable is used for detect if team are already added in the group division
        $teams = Team::where('teamSide', 'Academy Team')
            ->whereHas('divisions', function (Builder $query) use ($group) {
                $query->where('divisionId', $group->id);
            })->get();

        $opponentTeams = Team::where('teamSide', 'Opponent Team')
            ->whereDoesntHave('divisions', function (Builder $query) use ($group, $competition) {
                $query->where('competitionId', $competition->id);
            })->get();
        $availableAcademyTeams = Team::where('teamSide', 'Academy Team')
            ->whereDoesntHave('divisions', function (Builder $query) use ($competition) {
                $query->where('competitionId', $competition->id);
            })->get();

        $players = Player::all();
        $coaches = Coach::all();

        return view('pages.admins.managements.competitions.groups.addTeam', [
            'teams' => $teams,
            'availableAcademyTeams' => $availableAcademyTeams,
            'opponentTeams' => $opponentTeams,
            'competition' => $competition,
            'group' => $group,
            'players' => $players,
            'coaches' => $coaches
        ]);

    }

    public function storeTeam(Request $request, Competition $competition, GroupDivision $group){
        $data = $request->validate([
            'teams' => ['nullable', Rule::exists('teams', 'id')],
            'opponentTeams' => ['nullable', Rule::exists('teams', 'id')],
        ]);

        $this->groupDivisionService->storeTeam($data, $group);

        $text = "Division ".$group->groupName."'s' teams successfully added!";
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }
}
