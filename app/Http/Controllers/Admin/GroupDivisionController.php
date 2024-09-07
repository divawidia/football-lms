<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\GroupDivision;
use App\Models\Player;
use App\Models\Team;
use App\Services\GroupDivisionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
}
