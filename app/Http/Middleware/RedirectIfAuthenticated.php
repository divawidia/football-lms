<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Redirect based on roles
                if ($user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard');  // Admin dashboard
                } elseif ($user->hasRole('coach')) {
                    return redirect()->route('coach.dashboard');  // coach dashboard
                } elseif ($user->hasRole('player')) {
                    return redirect()->route('player.dashboard'); // player dashboard
                }

                // Default redirect if no role matched
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
