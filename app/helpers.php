<?php

use App\Models\Academy;
use Illuminate\Support\Facades\Auth;

function getLoggedUser(){
    return Auth::user();
}
function academyData(){
    return Academy::first();
}
function isSuperAdmin()
{
    return getLoggedUser()->hasRole('Super-Admin');
}
function isAdmin()
{
    return getLoggedUser()->hasRole('admin');
}
function isAllAdmin()
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