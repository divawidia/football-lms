<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlayerParrent;
use App\Models\User;
use http\Env\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class PlayerParentController extends Controller
{
    public function index(User $player)
    {

        if (request()->ajax()) {
            $query = PlayerParrent::where('playerId', $player->player->id);
            return Datatables::of($query)
                ->addColumn('action', function ($item) {
                    return '
                        <div class="dropdown">
                          <button class="btn btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="' . route('player-managements.edit', $item->id) . '"><span class="material-icons">edit</span> Edit Parent/Guardian</a>
                            <button type="button" class="dropdown-item delete-user" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Parent/Guardian
                            </button>
                          </div>
                        </div>';
                })
//                ->editColumn('name', function ($item) {
//                    return '<p class="mb-0"><strong class="js-lists-values-lead">'. $item->firstName .' '. $item->lastName .'</strong></p>';
//                })
                ->rawColumns(['action'])
                ->make();
        }
        return view('pages.admins.managements.players.detail');
    }

    public function create(string $id)
    {
        $user = User::findOrFail($id);
        return view('pages.admins.managements.players.player-parents.create',[
            'user' => $user
        ]);
    }

//    public function store(Request $request)
//    {
//        try {
//            $request->validate([
//                'firstName' => 'required|string',
//                'lastName' => 'required|string',
//                'email' => 'required|email|unique:player_parents',
//                'phoneNumber' => 'required|string',
//                'relations' => 'nullable|string',
//            ]);
//
//            $playerParent = PlayerParrent::create($request->all());
//            return response()->json($playerParent, 201);
//        } catch (ValidationException $e) {
//            return response()->json(['errors' => $e->errors()], 422);
//        }
//    }
}
