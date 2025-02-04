@extends('layouts.master')
@section('title')
    Performance Report
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
        {{--    Overview    --}}
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>

        <div class="row mb-3">
                @include('components.cards.stats-card', ['title' => 'Match Played','data' => $playerMatchPlayed, 'dataThisMonth' => $playerMatchPlayedThisMonth])
                @include('components.cards.stats-card', ['title' => 'Minutes Played','data' => $playerStats['minutesPlayed'], 'dataThisMonth' => $playerStats['minutesPlayedThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Fouls','data' => $playerStats['fouls'], 'dataThisMonth' => $playerStats['foulsThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Saves','data' => $playerStats['saves'], 'dataThisMonth' => $playerStats['savesThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Goals','data' => $playerStats['goals'], 'dataThisMonth' => $playerStats['goalsThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Assists','data' => $playerStats['assists'], 'dataThisMonth' => $playerStats['assistsThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Own Goals','data' => $playerStats['ownGoal'], 'dataThisMonth' => $playerStats['ownGoalThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Shots','data' => $playerStats['shots'], 'dataThisMonth' => $playerStats['shotsThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Passes','data' => $playerStats['passes'], 'dataThisMonth' => $playerStats['passesThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Fouls','data' => $playerStats['fouls'], 'dataThisMonth' => $playerStats['foulsThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Yellow Cards','data' => $playerStats['yellowCards'], 'dataThisMonth' => $playerStats['yellowCardsThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Red Cards','data' => $playerStats['redCards'], 'dataThisMonth' => $playerStats['redCardsThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Wins','data' => $matchResults['Win'], 'dataThisMonth' => $matchResults['WinThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Losses','data' => $matchResults['Lose'], 'dataThisMonth' => $matchResults['LoseThisMonth']])
                @include('components.cards.stats-card', ['title' => 'Draws','data' => $matchResults['Draw'], 'dataThisMonth' => $matchResults['DrawThisMonth']])
            @include('components.cards.stats-card', ['title' => 'win Rate','data' => $winRate, 'dataThisMonth' => null])
        </div>

                {{--Skill stats Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats</div>
                    <x-buttons.link-button :href="route('player.skill-stats')" icon="chevron_right" color="white" text="View More" margin="ml-auto"/>
                </div>
                <div class="card">
                    <x-player-skill-stats-radar-chart :labels="$playerSkillStats['label']"
                                                      :datas="$playerSkillStats['data']"
                                                      chartId="uniqueChartId"/>
                </div>

        <div class="page-separator">
            <div class="page-separator__text">Match History</div>
        </div>
        <x-match-tables :route="route('match-histories.player-index')" tableId="matchHistoryTable"/>
    </div>
@endsection
