<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerParentRequest;
use App\Models\PlayerParrent;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;
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
        $fullname = $user->firstName . ' ' . $user->lastName;
        return view('pages.admins.managements.players.player-parents.create',[
            'user' => $user,
            'fullname' => $fullname
        ]);
    }

    public function store(PlayerParentRequest $request, string $id)
    {
        $user = User::with('player')->findOrFail($id);
        $data = $request->validated();
        $data['playerId'] = $user->player->id;

        PlayerParrent::create($data);

        $text = "Player's parent/guardian successfully added!";
        Alert::success($text);

        return redirect()->route('player-managements.show', $id);
    }

    public function edit(User $player, PlayerParrent $parent)
    {
        $parent = PlayerParrent::findOrFail($parent->id);

        return view('pages.admins.managements.admins.player-parents.edit',[
            'parent' => $parent,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlayerParentRequest $request, User $player, PlayerParrent $parent)
    {
        $data = $request->validated();
        $parent = PlayerParrent::findOrFail($parent->id);

        $parent->update($data);

        $text = $parent->firstName.' successfully updated!';
        Alert::success($text);
        return redirect()->route('admin-managements.show', $player->id);
    }

    public function destroy(User $player, PlayerParrent $parent)
    {
        $parent = PlayerParrent::findOrFail($parent->id);

        $parent->delete();

        Alert::success($parent->firstName.' successfully deleted!');
        return redirect()->route('admin-managements.show', $player->id);
    }
}
