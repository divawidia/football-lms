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

function checkRoleDashboardRoute()
{
    $route = '';
    if (isAllAdmin()){
        $route = route('admin.dashboard');
    } elseif (isCoach()) {
        $route = route('coach.dashboard');
    } elseif (isPlayer()) {
        $route = route('player.dashboard');
    }
    return $route;
}

function secondToMinute($seconds){
    $minutes = floor($seconds / 60);  // Get the number of whole minutes
    $remaining_seconds = $seconds % 60;  // Get the remaining seconds

    return $minutes . "m " . $remaining_seconds . "s";
}

function priceFormat($price){
    return 'Rp. ' . number_format($price);
}

function convertToDatetime($timestamp){
    return date('M d, Y ~ h:i A', strtotime($timestamp));
}
