<?php

use App\Models\Academy;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

function getLoggedUser(){
    return Auth::user();
}
function academyData(): Academy
{
    return Academy::first();
}
function isSuperAdmin(): bool
{
    return getLoggedUser()->hasRole('Super-Admin');
}
function isAdmin(): bool
{
    return getLoggedUser()->hasRole('admin');
}
function isAllAdmin(): bool
{
    return getLoggedUser()->hasRole('admin|Super-Admin');
}
function isCoach(): bool
{
    return getLoggedUser()->hasRole('coach');
}
function isPlayer(): bool
{
    return getLoggedUser()->hasRole('player');
}

function checkRoleDashboardRoute(): string
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

function secondToMinute($seconds): string
{
    $minutes = floor($seconds / 60);  // Get the number of whole minutes
    $remaining_seconds = $seconds % 60;  // Get the remaining seconds

    return $minutes . "m " . $remaining_seconds . "s";
}

function priceFormat($price): string
{
    return 'Rp. ' . number_format($price);
}

function convertToDate($timestamp): string
{
    return date('M d, Y', strtotime($timestamp));
}

function convertToTime($timestamp): string
{
    return date('h:i A', strtotime($timestamp));
}
function convertToDatetime($timestamp): string
{
    return date('M d, Y ~ h:i A', strtotime($timestamp));
}

function getAge($date): int
{
    $dob = new DateTime($date);
    $today = new DateTime('today');
    return $dob->diff($today)->y;
}

function getUserFullName(User $user): string
{
    return $user->firstName . ' ' . $user->lastName;
}
