<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompetitionRequest;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\Player;
use App\Models\Team;
use App\Services\CompetitionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class CompetitionController extends Controller
{
    private CompetitionService $competitionService;
    public function __construct(CompetitionService $competitionService)
    {
        $this->competitionService = $competitionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->competitionService->datatables();
        }
        return view('pages.admins.managements.competitions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teams = $this->competitionService->getTeams();
        $opponentTeams = $this->competitionService->getOpponentTeams();
        $players = Player::all();
        $coaches = Coach::all();

        return view('pages.admins.managements.competitions.create', [
            'teams' => $teams,
            'opponentTeams' => $opponentTeams,
            'players' => $players,
            'coaches' => $coaches
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompetitionRequest $request)
    {
        $data = $request->validated();

        $this->competitionService->store($data);

        $text = 'Competition '.$data['name'].' successfully added!';
        Alert::success($text);
        return redirect()->route('competition-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Competition $competition)
    {
        $overviewStats = $this->competitionService->overviewStats($competition);
        return view('pages.admins.managements.competitions.detail', [
            'competition' => $competition,
            'overviewStats' => $overviewStats,
        ]);
    }
    public function competitionMatches(Competition $competition)
    {
        return $this->competitionService->competitionMatches($competition);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competition $competition)
    {
        return view('pages.admins.managements.competitions.edit',[
            'competition' => $competition,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Competition $competition)
    {

        $data = $request->validate([
            'name' => ['required', 'string'],
            'type' => ['required', Rule::in('League', 'Tournament')],
            'logo' => ['nullable', 'image', 'max:10240'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after:startDate'],
            'location' => ['required', 'string'],
            'contactName' => ['nullable', 'string'],
            'contactPhone' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $this->competitionService->update($data, $competition);

        $text = 'Competition '.$competition->name.' successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.index');
    }

    public function activate(Competition $competition)
    {
        $this->competitionService->activate($competition);

        $text = 'Competition '.$competition->name.' status successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }

    public function deactivate(Competition $competition)
    {
        $this->competitionService->deactivate($competition);

        $text = 'Competition '.$competition->name.' status successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $competition->id);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competition $competition)
    {
        $this->competitionService->destroy($competition);

        return response()->json(['success' => true]);
    }
}
