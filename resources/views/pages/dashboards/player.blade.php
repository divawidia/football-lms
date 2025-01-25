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
                <li class="breadcrumb-item"><a href="{{ route('player.dashboard') }}">Home</a></li>
            </ol>
        </div>
    </div>

    <div class="container page-section">

        <div class="card">
            <div class="nav-tabs-container">
                <ul class="nav nav-pills text-capitalize">
                    <x-tabs.item title="Overview" link="overview" :active="true"/>
                    <x-tabs.item title="Skill stats" link="skills-stats"/>
                    <x-tabs.item title="latest Trainings" link="latest-trainings"/>
                    <x-tabs.item title="Latest Matches" link="latest-matches"/>
                    <x-tabs.item title="performance review" link="performance-review"/>
                    <x-tabs.item title="parents/guardians" link="parents"/>
                    <x-tabs.item title="Upcoming Matches" link="upcoming-matches"/>
                    <x-tabs.item title="Upcoming Trainings" link="upcoming-trainings"/>
                </ul>
            </div>
        </div>

        <div class="tab-content mt-3">
            {{--    Overview    --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Joined Teams</div>
                </div>

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

                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                    {{--            <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>--}}
                </div>

                <div class="row card-group-row mb-4">
                    @include('components.cards.stats-card', ['title' => 'Match Played','data' => $playerMatchPlayed, 'dataThisMonth' => $playerMatchPlayedThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Minutes Played','data' => $playerStats['minutesPlayed'], 'dataThisMonth' => $playerStats['minutesPlayedThisMonth']])
                    @if($data->position == 'Goalkeeper (GK)')
                        @include('components.cards.stats-card', ['title' => 'Saves','data' => $playerStats['saves'], 'dataThisMonth' => $playerStats['savesThisMonth']])
                    @endif

                    @include('components.cards.stats-card', ['title' => 'shots','data' => $playerStats['shots'], 'dataThisMonth' => $playerStats['shotsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'passes','data' => $playerStats['passes'], 'dataThisMonth' => $playerStats['passesThisMonth']])

                    @include('components.cards.stats-card', ['title' => 'Fouls','data' => $playerStats['fouls'], 'dataThisMonth' => $playerStats['foulsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'yellowCards','data' => $playerStats['yellowCards'], 'dataThisMonth' => $playerStats['yellowCardsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'redCards','data' => $playerStats['redCards'], 'dataThisMonth' => $playerStats['redCardsThisMonth']])

                    @include('components.cards.stats-card', ['title' => 'Goals','data' => $playerStats['goals'], 'dataThisMonth' => $playerStats['goalsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Assists','data' => $playerStats['assists'], 'dataThisMonth' => $playerStats['assistsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Own Goals','data' => $playerStats['ownGoal'], 'dataThisMonth' => $playerStats['ownGoalThisMonth']])

                    @include('components.cards.stats-card', ['title' => 'Wins','data' => $matchResults['Win'], 'dataThisMonth' => $matchResults['WinThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Losses','data' => $matchResults['Lose'], 'dataThisMonth' => $matchResults['LoseThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Draws','data' => $matchResults['Draw'], 'dataThisMonth' => $matchResults['DrawThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'WIn Rate (%)','data' => $winRate, 'dataThisMonth' => null])
                </div>
            </div>

            {{--Skill stats Section--}}
            <div class="tab-pane fade show" id="skills-stats-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats</div>
                    <x-buttons.link-button :href="route('player.skill-stats')" icon="chevron_right" color="white" text="View More" margin="ml-auto"/>
                </div>
                <div class="card">
                    <x-player-skill-stats-radar-chart :labels="$playerSkillStats['label']"
                                                      :datas="$playerSkillStats['data']" chartId="uniqueChartId"/>
                </div>
            </div>

            {{--Latest Training Section--}}
            <div class="tab-pane fade show" id="latest-trainings-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Latest Trainings</div>
                </div>
                @if(count($latestTrainings) == 0)
                    <x-warning-alert text="There are no latest trainings at this moment"/>
                @endif
                <div class="row">
                    @foreach($latestTrainings as $training)
                        <x-cards.training-card :training="$training"/>
                    @endforeach
                </div>
            </div>

            {{--Latest matches Section--}}
            <div class="tab-pane fade show" id="latest-matches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Latest Match</div>
                </div>
                @if(count($latestMatches) == 0)
                    <x-warning-alert text="There are no latest matches at this moment"/>
                @endif
                @foreach($latestMatches as $match)
                    <x-cards.match-card :match="$match" :latestMatch="true"/>
                @endforeach
            </div>

            {{--player performance review--}}
            <div class="tab-pane fade show" id="performance-review-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">player performance review</div>
                </div>
                <x-player-performance-review-table
                    :route="route('player-managements.performance-reviews.index', ['player' => $data->id])"
                    tableId="performanceReviewTable"/>
            </div>

            {{--Parents/Guardians Section--}}
            <div class="tab-pane fade show" id="parents-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Parents/Guardians</div>
                </div>
                <x-player-parents-tables :player="$data->id"/>
            </div>

            {{--Upcoming Matches Section--}}
            <div class="tab-pane fade show" id="upcoming-matches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Matches</div>
                    <x-buttons.link-button :href="route('match-schedules.index')" icon="chevron_right" color="white" text="View More" margin="ml-auto"/>
                </div>
                @if(count($playerUpcomingMatches) < 1)
                    <x-warning-alert text="There are no matches scheduled at this moment"/>
                @endif
                @foreach($playerUpcomingMatches as $match)
                    <x-cards.match-card :match="$match" :latestMatch="false"/>
                @endforeach
            </div>

            {{--Upcoming Training Section--}}
            <div class="tab-pane fade show" id="upcoming-trainings-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text"></div>
                    <x-buttons.link-button :href="route('training-schedules.index')" icon="chevron_right" color="white" text="View More" margin="ml-auto"/>
                </div>
                @if(count($playerUpcomingTrainings) == 0)
                    <x-warning-alert text="There are no trainings scheduled at this moment"/>
                @endif
                <div class="row">
                    @foreach($playerUpcomingTrainings as $training)
                        <div class="col-lg-6">
                            <x-cards.training-card :training="$training"/>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
