<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    private UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function edit()
    {
        return view('pages.user-profile.edit', [
            'data' => $this->getLoggedUser(),
            'countries' => $this->userService->getCountryData()
        ]);
    }

    public function update(UserRequest $request)
    {
        $user = $this->getLoggedUser();
        $data = $request->validated();
        $this->userService->update($data, $user);

        $text = $data['firstName'].' '.$data['lastName'].' account successfully updated!';
        Alert::success($text);

        if ($this->isAdmin()){
            $route = 'admin.dashboard';
        } elseif ($this->isCoach()){
            $route = 'coach.dashboard';
        } elseif ($this->isPlayer()){
            $route = 'player.dashboard';
        }

        return redirect()->route($route);
    }
}
