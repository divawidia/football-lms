<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\PlayerPosition;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use Yajra\DataTables\Facades\DataTables;

class CoachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Coach::with('user', 'teams')->get();
            return Datatables::of($query)
                ->addColumn('action', function ($item) {
                    if ($item->user->status == '1') {
                        $statusButton = '<form action="' . route('deactivate-coach', $item->userId) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">block</span> Deactivate Coach
                                                </button>
                                            </form>';
                    } else {
                        $statusButton = '<form action="' . route('activate-coach', $item->userId) . '" method="POST">
                                                ' . method_field("PATCH") . '
                                                ' . csrf_field() . '
                                                <button type="submit" class="dropdown-item">
                                                    <span class="material-icons">check_circle</span> Activate Coach
                                                </button>
                                            </form>';
                    }
                    return '
                            <div class="dropdown">
                              <button class="btn btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="' . route('coach-managements.edit', $item->userId) . '"><span class="material-icons">edit</span> Edit Coach</a>
                                <a class="dropdown-item" href="' . route('coach-managements.show', $item->userId) . '"><span class="material-icons">visibility</span> View Coach</a>
                                ' . $statusButton . '
                                <a class="dropdown-item" href="' . route('coach-managements.change-password-page', $item->userId) . '"><span class="material-icons">lock</span> Change Coach Password</a>
                                <button type="button" class="dropdown-item delete-user" id="' . $item->userId . '">
                                    <span class="material-icons">delete</span> Delete Coach
                                </button>
                              </div>
                            </div>';
                })
                ->editColumn('teams.name', function ($item) {
                    if (count($item->teams) === 0) {
                        $team = 'No Team';
                    } else {
                        $team = $item->teams->name;
                    }
                    return $team;
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
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' ' . $item->user->lastName . '</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                        </div>
                                    </div>

                                </div>
                            </div>';
                })
                ->editColumn('status', function ($item) {
                    if ($item->user->status == '1') {
                        return '<span class="badge badge-pill badge-success">Aktif</span>';
                    } elseif ($item->user->status == '0') {
                        return '<span class="badge badge-pill badge-danger">Non Aktif</span>';
                    }
                })
                ->editColumn('age', function ($item) {
                    return $this->getAge($item->user->dob);
                })
                ->rawColumns(['action', 'name', 'status', 'age', 'teams.name'])
                ->make();
        }
        return view('pages.admins.managements.coaches.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action =  World::countries();
        if ($action->success) {
            $countries = $action->data;
        }

        $teams = Team::all();

        return view('pages.admins.managements.coaches.create', [
            'countries' => $countries,
            'teams' => $teams
        ]);
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
