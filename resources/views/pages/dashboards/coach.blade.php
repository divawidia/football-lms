@extends('layouts.master')
@section('title')
    Dashboard
@endsection
@section('page-title')
    Dashboard
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
                    <h2 class="mb-0">@yield('title')</h2>
                    <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('coach.dashboard') }}">Home</a></li>
                    </ol>
        </div>
    </div>

    <div class="container page-section">

        <div class="card">
            <div class="nav-tabs-container">
                <ul class="nav nav-pills text-capitalize">
                    <x-tabs.item title="Overview" link="overview" :active="true"/>
                    <x-tabs.item title="Latest Matches" link="matches"/>
                    <x-tabs.item title="Upcoming Matches" link="upcoming-matches"/>
                    <x-tabs.item title="Upcoming Trainings" link="upcoming-trainings"/>
                </ul>
            </div>
        </div>

        <div class="tab-content mt-3">
            {{--    Overview    --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Managed Teams</div>
                </div>

                <div class="row">
                    @foreach($teams as $team)
                        <div class="col-lg-6">
                            <a class="card" href="{{route('team-managements.show', $team->hash)}}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 d-flex flex-column flex-md-row align-items-center">
                                            <img src="{{ Storage::url($team->logo) }}"
                                                 width="50"
                                                 height="50"
                                                 class="rounded-circle img-object-fit-cover"
                                                 alt="team-logo">
                                            <div class="ml-md-3 text-center text-md-left">
                                                <h5 class="mb-0">{{$team->teamName}}</h5>
                                                <p class="text-50 lh-1 mb-0">{{$team->ageGroup}}</p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex flex-column justify-content-center align-items-end">
                                            <div>
                                                <i class='fa fa-users icon-16pt text-danger mr-2'></i>
                                                {{ $team->players()->count() }} Players
                                            </div>
                                            <div>
                                                <i class="fa fa-user-tie icon-16pt text-danger mr-2"></i>
                                                {{ $team->coaches()->count() }} Coaches
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                    {{--            <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>--}}
                </div>

                <div class="row mb-4">
                    @include('components.cards.stats-card', ['title' => 'Matches','data' => $matchPlayed, 'dataThisMonth' => $matchPlayedThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Wins','data' => $wins, 'dataThisMonth' => $winsThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Losses','data' => $lose, 'dataThisMonth' => $loseThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Draws','data' => $draw, 'dataThisMonth' => $drawThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Win Rate (%)','data' => $winRate, 'dataThisMonth' => $winRateThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Goals For','data' => $goals, 'dataThisMonth' => $goalsThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Goal Against','data' => $goalConceded, 'dataThisMonth' => $goalConcededThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Goal Differences','data' => $goalsDifference, 'dataThisMonth' => $goalsDifferenceThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Clean Sheets','data' => $cleanSheets, 'dataThisMonth' => $cleanSheetsThisMonth])
                </div>
            </div>
            {{--    Latest Matches    --}}
            <div class="tab-pane fade show" id="matches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Latest Matches</div>
                </div>

                <div class="row">
                    @if(count($latestMatches) == 0)
                        <x-warning-alert text="There are no latest matches record on this team"/>
                    @endif
                    @foreach($latestMatches as $match)
                        <div class="col-lg-6">
                            <x-cards.match-card :match="$match" :latestMatch="true"/>
                        </div>
                    @endforeach
                </div>
            </div>

            {{--    Upcoming matches    --}}
            <div class="tab-pane fade show" id="upcoming-matches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Matches</div>
                    <x-buttons.link-button :href="route('match-schedules.index')" icon="chevron_right" color="white" text="View More" margin="ml-auto"/>
                </div>
                @if(count($upcomingMatches) < 1)
                    <x-warning-alert text="There are no matches scheduled at this time"/>
                @endif
                @foreach($upcomingMatches as $match)
                    <x-cards.match-card :match="$match" :latestMatch="false"/>
                @endforeach
            </div>

            <div class="tab-pane fade show" id="upcoming-trainings-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Trainings</div>
                    <x-buttons.link-button :href="route('training-schedules.index')" icon="chevron_right" color="white" text="View More" margin="ml-auto"/>
                </div>
                @if(count($upcomingTrainings) < 1)
                    <x-warning-alert text="There are no trainings scheduled at this time"/>
                @endif
                <div class="row">
                    @foreach($upcomingTrainings as $training)
                        <div class="col-lg-6">
                            <x-cards.training-card :training="$training"/>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
