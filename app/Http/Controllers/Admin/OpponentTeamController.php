<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpponentTeamRequest;
use App\Models\OpponentTeam;
use App\Service\OpponentTeamService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class OpponentTeamController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = OpponentTeam::all();
            return Datatables::of($query)->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-opponentTeam', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="dropdown-item">
                                            <span class="material-icons">block</span> Deactivate Team
                                        </button>
                                    </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-opponentTeam', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="dropdown-item">
                                            <span class="material-icons">check_circle</span> Activate Team
                                        </button>
                                    </form>';
                }
                return '
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . route('opponentTeam-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Team</a>
                                <a class="dropdown-item" href="' . route('opponentTeam-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Team</a>
                                ' . $statusButton . '
                                <button type="button" class="dropdown-item delete-team" id="' . $item->id . '">
                                    <span class="material-icons">delete</span> Delete Team
                                </button>
                              </div>
                            </div>';
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
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                })
                ->editColumn('status', function ($item) {
                    if ($item->status == '1') {
                        return '<span class="badge badge-pill badge-success">Aktif</span>';
                    } elseif ($item->status == '0') {
                        return '<span class="badge badge-pill badge-danger">Non Aktif</span>';
                    }
                })
                ->editColumn('players', function ($item) {
                    return $item->totalPlayers . ' Player(s)';
                })
                ->rawColumns(['action', 'name', 'status', 'players'])
                ->make();
        }
        return view('pages.admins.managements.opponentTeams.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admins.managements.opponentTeams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OpponentTeamRequest $request, OpponentTeamService $opponentTeamService)
    {
        $data = $request->validated();

        $opponentTeamService->store($data);

        $text = 'Team '.$data['teamName'].' successfully added!';
        Alert::success($text);
        return redirect()->route('opponentTeam-managements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(OpponentTeam $team)
    {
        return view('pages.admins.managements.opponentTeams.detail', [
            'team' => $team,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OpponentTeam $team)
    {
        return view('pages.admins.managements.opponentTeams.edit',[
            'team' => $team,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OpponentTeamRequest $request, OpponentTeam $team, OpponentTeamService $opponentTeamService)
    {
        $data = $request->validated();

        $opponentTeamService->update($data, $team);

        $text = 'Team '.$team->teamName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('opponentTeam-managements.show', $team->id);
    }

    public function deactivate(OpponentTeam $team){
        $team->update([
            'status' => '0'
        ]);
        Alert::success('Team '.$team->teamName.' status successfully deactivated!');
        return redirect()->route('opponentTeam-managements.index');
    }

    public function activate(OpponentTeam $team){
        $team->update([
            'status' => '1'
        ]);
        Alert::success('Team '.$team->teamName.' status successfully activated!');
        return redirect()->route('opponentTeam-managements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OpponentTeam $team)
    {
        if (File::exists($team->logo) && $team->logo != 'images/undefined-user.png') {
            File::delete($team->logo);
        }
        $team->delete();

        return response()->json(['success' => true]);
    }
}
