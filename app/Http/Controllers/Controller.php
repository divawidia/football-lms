<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use DateTime;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getAcademyId(){
        return Auth::user()->academyId;
    }

    public function getLoggedUser(){
        return Auth::user();
    }

    public function getLoggedUserId(){
        return $this->getLoggedUser()->id;
    }

    public function getLoggedCoachUser(){
        return Coach::where('userId', $this->getLoggedUserId())->select('id')->first();
    }

    public function isAdmin()
    {
        return Auth::user()->hasRole('admin|SUper-Admin');
    }
    public function isCoach()
    {
        return Auth::user()->hasRole('coach');
    }
    public function isPlayer()
    {
        return Auth::user()->hasRole('player');
    }

}
