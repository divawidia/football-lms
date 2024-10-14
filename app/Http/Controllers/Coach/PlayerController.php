<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Services\PlayerService;

class PlayerController extends Controller
{
    private PlayerService $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->playerService->coachPlayerIndex($this->getLoggedCoachUser());
        }
        return view('pages.coaches.managements.players.index');
    }
}
