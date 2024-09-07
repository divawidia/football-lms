<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\GroupDivision;
use App\Models\Team;
use App\Services\GroupDivisionService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class GroupDivisionController extends Controller
{
    private GroupDivisionService $groupDivisionService;
    public function __construct(GroupDivisionService $groupDivisionService)
    {
        $this->groupDivisionService = $groupDivisionService;
    }

    public function index(GroupDivision $groupDivision){
        if (request()->ajax()){
            return $this->groupDivisionService->index($groupDivision);
        }
    }

    public function create(GroupDivision $groupDivision){
        $teams = Team::where('teamSide', 'Academy Team')->get();
        $opponentTeams = Team::where('teamSide', 'Opponent Team')->get();

        return view('pages.admins.managements.competitions.groups.create', [
            'teams' => $teams,
            'opponentTeams' => $opponentTeams
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
