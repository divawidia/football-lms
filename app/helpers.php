<?php

use Illuminate\Support\Facades\Auth;

function getLoggedUser(){
    return Auth::user();
}
function isAdmin()
{
    return getLoggedUser()->hasRole('admin|Super-Admin');
}
function isCoach()
{
    return getLoggedUser()->hasRole('coach');
}
function isPlayer()
{
    return getLoggedUser()->hasRole('player');
}
