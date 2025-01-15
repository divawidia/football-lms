@extends('layouts.master')
@section('title')
    Leaderboard
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container d-flex flex-column">
                <h2 class="mb-0">@yield('title')</h2>
                <ol class="breadcrumb p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                    <li class="breadcrumb-item active">
                        @yield('title')
                    </li>
                </ol>
            </div>
        </div>

        <div class="container page__container page-section">
            <div class="page-separator">
                <div class="page-separator__text">Team Leaderboard</div>
            </div>
            <x-tables.team-leaderboard :teamsLeaderboardRoute="$teamsLeaderboardRoute"/>


            <div class="page-separator">
                <div class="page-separator__text">Player Leaderboard</div>
            </div>
            <x-tables.player-leaderboard :playersLeaderboardRoute="$playersLeaderboardRoute"/>
        </div>
    @endsection
