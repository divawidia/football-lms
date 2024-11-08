<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function checkRoleRedirect()
    {
        $route = '';
        if ($this->isAllAdmin()){
            $route = 'admin.dashboard';
        } elseif ($this->isCoach()){
            $route = 'coach.dashboard';
        } elseif ($this->isPlayer()){
            $route = 'player.dashboard';
        }
        return $route;
    }

    public function update(UserRequest $request)
    {
        $user = $this->getLoggedUser();
        $data = $request->validated();
        $this->userService->update($data, $user);

        $text = $data['firstName'].' '.$data['lastName'].' account successfully updated!';
        Alert::success($text);

        return redirect()->route($this->checkRoleRedirect());
    }

    public function resetPassword()
    {
        return view('pages.user-profile.reset-password');
    }

    public function updatePassword(ResetPasswordRequest $request)
    {
        $user = $this->getLoggedUser();
        $data = $request->validated();
        if (!Hash::check($request->old_password, Auth::user()->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }
        $this->userService->changePassword($data, $user);
        $text = $data['firstName'].' '.$data['lastName'].' accounts password successfully updated!';
        Alert::success($text);
        return redirect()->route($this->checkRoleRedirect());
    }
}
