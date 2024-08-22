<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    public function authenticated(Request $request, $user): RedirectResponse
    {
        $user->update([
            'last_seen' => Carbon::now()
        ]);

        Cache::put('user-is-online-' . $user->id, true, Carbon::now()->addMinutes(10));

        if (Auth::user()->hasRole('admin') {
            Alert::toast('Selamat datang ' . Auth::user()->name, 'success');
            return redirect()->route('admin.dashboard');
        } else if (Auth::user()->hasRole('coach')) {
            Alert::toast('Selamat datang ' . Auth::user()->name, 'success');
            return redirect()->route('instruktur.dashboard');
        } else  if (Auth::user()->hasRole('player')) {
            Alert::toast('Selamat datang ' . Auth::user()->name, 'success');
            return redirect()->route('mahasiswa.dashboard');
        }
    }
}
