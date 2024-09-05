<?php

namespace App\Services;

use App\Models\Competition;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CompetitionService extends Service
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $query = Competition::all();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-competition', $item->id) . '" method="POST">
                                                    ' . method_field("PATCH") . '
                                                    ' . csrf_field() . '
                                                    <button type="submit" class="dropdown-item">
                                                        <span class="material-icons">block</span> Deactivate Competition
                                                    </button>
                                                </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-competition', $item->id) . '" method="POST">
                                                    ' . method_field("PATCH") . '
                                                    ' . csrf_field() . '
                                                    <button type="submit" class="dropdown-item">
                                                        <span class="material-icons">check_circle</span> Activate Competition
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
                                <a class="dropdown-item" href="' . route('competition-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Competition</a>
                                <a class="dropdown-item" href="' . route('competition-managements.show', $item->id) . '"><span class="material-icons">visibility</span> View Competition</a>
                                ' . $statusButton . '
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
            ->editColumn('date', function ($item) {
                $startDate = date('M d, Y', strtotime($item->startDate));
                $endDate = date('M d, Y', strtotime($item->endDate));
                return $startDate.' '.$endDate;
            })
            ->editColumn('contact', function ($item) {
                return $item->contactName. ' ('.$item->contactPhone.')';
            })
            ->rawColumns(['action', 'name', 'teams', 'opponentTeams', 'date', 'contact'])
            ->make();
    }
    public  function store(array $competitionData){

        if (array_key_exists('logo', $competitionData)){
            $competitionData['logo'] =$competitionData['logo']->store('assets/competition-logo', 'public');
        }else{
            $competitionData['logo'] = 'images/undefined-user.png';
        }
        $competitionData['status'] = '1';

        $competition = Competition::create($competitionData);

        if (array_key_exists('teams', $competitionData)){
            $competition->teams()->attach($competitionData['teams']);
        }
        if (array_key_exists('opponentTeams', $competitionData)){
            $competition->opponentTeams()->attach($competitionData['opponentTeams']);
        }
        return $competition;
    }

    public function update(array $competitionData, Competition $competition): Competition
    {
        if (array_key_exists('logo', $competitionData)){
            $this->deleteImage($competitionData['logo']);
            $competitionData['logo'] = $competitionData['logo']->store('assets/competition-logo', 'public');
        }else{
            $competitionData['logo'] = $competition->logo;
        }

        $competition->update($competitionData);
        $competition->teams()->sync($competitionData['teams']);
        $competition->opponentTeams()->sync($competitionData['teams']);

        return $competition;
    }

    public function activate(Competition $competition): Competition
    {
        $competition->update(['status' => '1']);
        return $competition;
    }

    public function deactivate(Competition $competition): Competition
    {
        $competition->update(['status' => '0']);
        return $competition;
    }

    public function destroy(Competition $competition): Competition
    {
        $this->deleteImage($competition->logo);
        $competition->teams()->detach();
        $competition->opponentTeams()->detach();
        $competition->delete();
        return $competition;
    }
}
