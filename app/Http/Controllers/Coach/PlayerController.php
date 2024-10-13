<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerRequest;
use App\Models\Admin;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use App\Services\Coach\PlayerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nnjeim\World\World;
use RealRashid\SweetAlert\Facades\Alert;

class PlayerController extends Controller
{
    private PlayerService $playerService;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->playerService = new PlayerService($this->getLoggedCoachUser());
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->playerService->index();
        }
        return view('pages.coaches.managements.players.index');
    }
}
