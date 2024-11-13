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
        return $this->groupDivisionService->index($competition, $group);
    }

    public function create(Competition $competition){
        $data = $this->groupDivisionService->create($competition);

        return view('pages.admins.managements.competitions.groups.create', [
            'teams' => $data['teams'],
            'opponentTeams' => $data['opponentTeams'],
            'competition' => $competition,
            'players' => $data['players'],
            'coaches' => $data['coaches']
        ]);
    }

    public function store(GroupDivisionRequest $request, Competition $competition){
        $data = $request->validated();

        $this->groupDivisionService->store($data, $competition, $this->getLoggedUser());

        $text = 'Division '.$data['groupName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }

    public function addTeam(Competition $competition, GroupDivision $group)
    {
        // get team data where teams is our academy team and has been added in the group division, this variable is used for detect if team are already added in the group division
        $data = $this->groupDivisionService->addTeam($competition, $group);

        return view('pages.admins.managements.competitions.groups.addTeam', [
            'teams' => $data['teams'],
            'availableAcademyTeams' => $data['availableAcademyTeams'],
            'opponentTeams' => $data['opponentTeams'],
            'competition' => $competition,
            'group' => $group,
            'players' => $data['players'],
            'coaches' => $data['coaches']
        ]);

    }

    public function storeTeam(Request $request, Competition $competition, GroupDivision $group){
        $data = $request->validate([
            'teams' => ['nullable', Rule::exists('teams', 'id')],
            'opponentTeams' => ['nullable', Rule::exists('teams', 'id')],
        ]);

        $this->groupDivisionService->storeTeam($data, $group, $competition);

        $text = "Division ".$group->groupName."'s' teams successfully added!";
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }


    public function removeTeam(Competition $competition, GroupDivision $group, Team $team)
    {
        $this->groupDivisionService->removeTeam($group, $team);

        $text = "Team ".$team->teamName." successfully removed from ".$group->name."!";
        Alert::success($text);
        return redirect()->route('competition-managements.show', ['competition'=>$competition->id]);
    }

    public function edit(Competition $competition, GroupDivision $group)
    {
        return response()->json($group);
    }

    public function update(Request $request, Competition $competition, GroupDivision $group)
    {
        $data = $request->validate([
            'groupName' => ['required', 'string']
        ]);

        $groudDivision = $this->groupDivisionService->update($data, $group, $this->getLoggedUser());
        return response()->json($groudDivision, 204);
    }

    public function destroy(Competition $competition, GroupDivision $group)
    {
        $data = $this->groupDivisionService->destroy($group, $this->getLoggedUser());
        return response()->json($data, 204);
    }
}
