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
            @include('components.cards.stats-card', ['title' => 'Match Played','data' => $totalMatchPlayed, 'dataThisMonth' => $totalMatchPlayedThisMonth])
            @include('components.cards.stats-card', ['title' => 'Goals Scored','data' => $goalScored, 'dataThisMonth' => $goalScoredThisMonth])
            @include('components.cards.stats-card', ['title' => 'Goals Conceded','data' => $goalConceded, 'dataThisMonth' => $goalConcededThisMonth])
            @include('components.cards.stats-card', ['title' => 'Goals Difference','data' => $goalDifference, 'dataThisMonth' => $goalDifferenceThisMonth])
            @include('components.cards.stats-card', ['title' => 'Clean Sheets','data' => $cleanSheets, 'dataThisMonth' => $cleanSheetsThisMonth])
            @include('components.cards.stats-card', ['title' => 'team OwnGoal','data' => $teamOwnGoal, 'dataThisMonth' => $teamOwnGoalThisMonth])
            @include('components.cards.stats-card', ['title' => 'Wins','data' => $teamWins, 'dataThisMonth' => $teamWinsThisMonth])
            @include('components.cards.stats-card', ['title' => 'Losses','data' => $teamLosses, 'dataThisMonth' => $teamLossesThisMonth])
            @include('components.cards.stats-card', ['title' => 'Draws','data' => $teamDraws, 'dataThisMonth' => $teamDrawsThisMonth])
            @include('components.cards.stats-card', ['title' => 'win Rate (%)','data' => $winRate, 'dataThisMonth' => $winRateThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Shots','data' => $teamShots, 'dataThisMonth' => $teamShotsThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Shots on target','data' => $teamShotOnTarget, 'dataThisMonth' => $teamShotOnTargetThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Touches','data' => $teamTouches, 'dataThisMonth' => $teamTouchesThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Tackles','data' => $teamTackles, 'dataThisMonth' => $teamTacklesThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Clearances','data' => $teamClearances, 'dataThisMonth' => $teamClearancesThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Corners','data' => $teamCorners, 'dataThisMonth' => $teamCornersThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Offsides','data' => $teamOffsides, 'dataThisMonth' => $teamOffsidesThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Yellow Cards','data' => $teamYellowCards, 'dataThisMonth' => $teamYellowCardsThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Red Cards','data' => $teamRedCards, 'dataThisMonth' => $teamRedCardsThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Fouls Conceded','data' => $teamFoulsConceded, 'dataThisMonth' => $teamFoulsConcededThisMonth])
            @include('components.cards.stats-card', ['title' => 'team Passes','data' => $teamPasses, 'dataThisMonth' => $teamPassesThisMonth])
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Match History</div>
        </div>
        <x-match-tables :route="route('match-histories.admin-index')" tableId="matchHistoryTable"/>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            processWithConfirmation(
                '.delete',
                "{{ route('match-schedules.destroy', ['match' => ':id']) }}",
                "{{ route('match-histories.index') }}",
                'DELETE',
                "Are you sure to delete this match?",
                "Something went wrong when deleting this match!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.cancelBtn',
                "{{ route('match-schedules.cancel', ['match' =>':id']) }}",
                "{{ route('match-histories.index') }}",
                'PATCH',
                "Are you sure to cancel this match?",
                "Something went wrong when cancelling this match!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.scheduled-btn',
                "{{ route('match-schedules.scheduled', ['match' =>':id']) }}",
                "{{ route('match-histories.index') }}",
                'PATCH',
                "Are you sure to set this match to scheduled?",
                "Something went wrong when set this match to scheduled!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush

