<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    private Auth $auth;

    public function getAcademyId(){
        return $this->auth->user()->academyId;
    }

    public function getLoggedUserId(){
        return $this->auth->user()->id;
    }
}
