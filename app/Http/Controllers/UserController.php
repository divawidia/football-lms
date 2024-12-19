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
        $this->userService->update($request->validated(), $this->getLoggedUser());

        Alert::success($this->getLoggedUserFullName()."'s accounts successfully updated!");
        return redirect()->route($this->checkRoleRedirect());
    }

    public function resetPassword()
    {
        return view('pages.user-profile.reset-password');
    }

    public function updatePassword(ResetPasswordRequest $request)
    {
        $user = $this->getLoggedUser();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $this->userService->changePassword($request->validated(), $user);

        Alert::success($this->getLoggedUserFullName()."'s accounts password successfully updated!");
        return redirect()->route($this->checkRoleRedirect());
    }
}
