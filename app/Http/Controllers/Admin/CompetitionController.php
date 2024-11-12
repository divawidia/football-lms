<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompetitionRequest;
use App\Http\Requests\UpdateCompetitionRequest;
use App\Models\Coach;
use App\Models\Competition;
use App\Models\Player;
use App\Models\Team;
use App\Services\CompetitionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $data = $this->competitionService->create();
        return view('pages.admins.managements.competitions.create', [
            'teams' => $data['teams'],
            'opponentTeams' => $data['opponentTeams'],
            'players' => $data['players'],
            'coaches' => $data['coaches']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompetitionRequest $request)
    {
        $data = $request->validated();

        $this->competitionService->store($data, $this->getLoggedUser());

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
    public function update(UpdateCompetitionRequest $request, Competition $competition)
    {

        $data = $request->validated();
        $this->competitionService->update($data, $competition, $this->getLoggedUser());

        $text = 'Competition '.$competition->name.' successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.index');
    }

    public function status(Competition $competition, $status)
    {
        try {
            $this->competitionService->setStatus($competition, $status);
            return response()->json(['message' => 'Competition '.$competition->name.' status successfully mark to '.$status.'!']);

        } catch (Exception $e) {
            Log::error('Error marking invoice as Scheduled: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while marking the competition '.$competition->name.' as '.$status.'.'], 500);
        }
    }

    public function scheduled(Competition $competition)
    {
        return $this->status($competition, 'Scheduled');
    }

    public function ongoing(Competition $competition)
    {
        return $this->status($competition, 'Ongoing');
    }
    public function completed(Competition $competition)
    {
        return $this->status($competition, 'Completed');
    }
    public function cancelled(Competition $competition)
    {
        return $this->status($competition, 'Cancelled');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competition $competition)
    {
        $this->competitionService->destroy($competition, $this->getLoggedUser());
        return response()->json(['success' => true]);
    }
}
