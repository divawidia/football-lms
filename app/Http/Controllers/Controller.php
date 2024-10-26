<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Player;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

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

    public function getLoggedPLayerUser(){
        return Player::where('userId', $this->getLoggedUserId())->select('id')->first();
    }

    public function isAdmin()
    {
        return $this->getLoggedUser()->hasRole('admin');
    }
    public function isASuperdmin()
    {
        return $this->getLoggedUser()->hasRole('Super-Admin');
    }
    public function isAllAdmin()
    {
        return $this->getLoggedUser()->hasRole('admin|Super-Admin');
    }
    public function isCoach()
    {
        return $this->getLoggedUser()->hasRole('coach');
    }
    public function isPlayer()
    {
        return $this->getLoggedUser()->hasRole('player');
    }

    public function successAlertAddUser(array $data, string $context)
    {
        $text = $data['firstName'].' '.$data['lastName'].' successfully '.$context.'!';
        return Alert::success($text);
    }

    public function successAlertStatusUser(User $user, string $context)
    {
        $text = $user->firstName.' '.$user->lasstName.' status successfully '.$context.'!';
        return Alert::success($text);
    }

}
