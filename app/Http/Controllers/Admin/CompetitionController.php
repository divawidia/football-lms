<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Competition::with('teams', 'opponentTeams')->get();
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
                                <a class="dropdown-item" href="' . route('team-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Competition</a>
                                <a class="dropdown-item" href="' . route('team-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Competition</a>
                                <button type="button" class="dropdown-item delete-user" id="' . $item->id . '">
                                    <span class="material-icons">delete</span> Delete Competition
                                </button>
                              </div>
                            </div>';
                })
                ->editColumn('teams', function ($item) {
                    $academyTeam = '';
                    if (count($item->teams) == 0){
                        $academyTeam = 'No teams in this competition at this moment';
                    }else{
                        foreach ($item->teams as $team) {
                            $academyTeam .= '<span class="badge badge-pill badge-danger">'.$team->teamName.'</span>';
                        }
                    }
                    return $academyTeam;
                })
                ->editColumn('opponentTeams', function ($item) {
                    $opponentTeam = '';
                    if (count($item->opponentTeams) == 0){
                        $opponentTeam = 'No teams in this competition at this moment';
                    }else{
                        $opponentTeam = count($item->opponentTeams).' Opponent teams';
                    }
                    return $opponentTeam;
                })
                ->editColumn('name', function ($item) {
                    return '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->name . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->type . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                })
                ->rawColumns(['action', 'name', 'teams', 'opponentTeams'])
                ->make();
        }
        return view('pages.admins.managements.competitions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
