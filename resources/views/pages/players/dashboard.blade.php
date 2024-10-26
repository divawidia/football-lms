@extends('layouts.master')
@section('title')
    Dashboard
@endsection
@section('page-title')
    Dashboard
@endsection

@section('content')
    <div class="pt-32pt">
        <div
            class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left">
            <div class="flex d-flex flex-column flex-sm-row align-items-center">
                <div class="mr-sm-24pt text-sm-left">
                    <h2 class="mb-0">@yield('title')</h2>
                    <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('player.dashboard') }}">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Teams</div>
        </div>

        <div class="row">
            @foreach($dataOverview['teams'] as $team)
                <div class="col-lg-6">
                    <a class="card" href="">
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

        <div class="row card-group-row mb-4">
            @include('components.stats-card', ['title' => 'Minutes Played','data' => $overviewStats['totalMatchPlayed'], 'dataThisMonth' => $overviewStats['thisMonthTotalMatchPlayed']])
            @include('components.stats-card', ['title' => 'Fouls','data' => $overviewStats['totalGoals'], 'dataThisMonth' => $overviewStats['thisMonthTotalGoals']])
            @include('components.stats-card', ['title' => 'Saves','data' => $overviewStats['totalGoalsConceded'], 'dataThisMonth' => $overviewStats['thisMonthTotalGoalsConceded']])
            @include('components.stats-card', ['title' => 'Goals','data' => $overviewStats['goalsDifference'], 'dataThisMonth' => $overviewStats['thisMonthGoalsDifference']])
            @include('components.stats-card', ['title' => 'Assists','data' => $overviewStats['totalCleanSheets'], 'dataThisMonth' => $overviewStats['thisMonthTotalCleanSheets']])
            @include('components.stats-card', ['title' => 'Own Goals','data' => $overviewStats['totalOwnGoals'], 'dataThisMonth' => $overviewStats['thisMonthTotalOwnGoals']])
            @include('components.stats-card', ['title' => 'Wins','data' => $overviewStats['totalWins'], 'dataThisMonth' => $overviewStats['thisMonthTotalWins']])
            @include('components.stats-card', ['title' => 'Losses','data' => $overviewStats['totalLosses'], 'dataThisMonth' => $overviewStats['thisMonthTotalLosses']])
            @include('components.stats-card', ['title' => 'Draws','data' => $overviewStats['totalDraws'], 'dataThisMonth' => $overviewStats['thisMonthTotalDraws']])
        </div>

        <div class="row">
            <div class="col-sm-6 flex-column">
                {{--Teams Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats</div>
                    <a href="{{ route('player-managements.skill-stats', $data->id) }}"
                       class="btn btn-white border btn-sm ml-auto">
                        View More
                        <span class="material-icons ml-2 icon-16pt">chevron_right</span>
                    </a>
                </div>
                <div class="card">
                    <canvas id="skillStatsChart"></canvas>
                </div>
            </div>
            <div class="col-sm-6 flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Latest Match</div>
                </div>
                @if(count($latestMatches) == 0)
                    <div class="alert alert-light border-left-accent" role="alert">
                        <div class="d-flex flex-wrap align-items-center">
                            <i class="material-icons mr-8pt">error_outline</i>
                            <div class="media-body"
                                 style="min-width: 180px">
                                <small class="text-black-100">There are no latest matches record on this team</small>
                            </div>
                        </div>
                    </div>
                @endif
                @foreach($latestMatches as $match)
                    <a class="card" href="{{ route('match-schedules.show', $match->id) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                                    <img src="{{ Storage::url($match->teams[0]->logo) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="team-logo">
                                    <div class="ml-md-3 text-center text-md-left">
                                        <h6 class="mb-0">{{ $match->teams[0]->teamName }}</h6>
                                        <p class="text-50 lh-1 mb-0">{{ $match->teams[0]->ageGroup }}</p>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <h2 class="mb-0">{{ $match->teams[0]->pivot->teamScore }}
                                        - {{ $match->teams[1]->pivot->teamScore }}</h2>
                                </div>
                                <div
                                    class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                                    <div class="mr-md-3 text-center text-md-right">
                                        <h6 class="mb-0">{{ $match->teams[1]->teamName }}</h6>
                                        <p class="text-50 lh-1 mb-0">{{ $match->teams[1]->ageGroup }}</p>
                                    </div>
                                    <img src="{{ Storage::url($match->teams[1]->logo) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="team-logo">
                                </div>
                            </div>

                            <div class="row justify-content-center mt-3">
                                <div class="mr-2">
                                    <i class="material-icons text-danger icon--left icon-16pt">event</i>
                                    {{ date('D, M d Y', strtotime($match->date)) }}
                                </div>
                                <div class="mr-2">
                                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                                    {{ date('h:i A', strtotime($match->startTime)) }}
                                    - {{ date('h:i A', strtotime($match->endTime)) }}
                                </div>
                                <div>
                                    <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                    {{ $match->place }}
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{--Parents/Guardians Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Parents/Guardians</div>
            @if(Auth::user()->hasRole('admin|Super-Admin'))
                <a href="{{  route('player-parents.create', $data->id) }}" class="btn btn-sm btn-primary ml-auto"
                   id="add-new">
                <span class="material-icons mr-2">
                    add
                </span>
                    Add New
                </a>
            @endif
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="parentsTable">
                        <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Relation</th>
                            @if(Auth::user()->hasRole('admin|Super-Admin'))
                                <th>Action</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Upcoming Matches</div>
            <a href="{{ route('match-schedules.index') }}" class="btn btn-outline-secondary bg-white btn-sm ml-auto">
                View More
                <span class="material-icons ml-2 icon-16pt">chevron_right</span>
            </a>
        </div>
        @if(count($upcomingMatches) == 0)
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">There are no matches scheduled at this time</small>
                    </div>
                </div>
            </div>
        @endif
        @foreach($upcomingMatches as $match)
            <a class="card" href="{{ route('match-schedules.show', $match->id) }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                            <img src="{{ Storage::url($match->teams[0]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="team-logo">
                            <div class="ml-md-3 text-center text-md-left">
                                <h5 class="mb-0">{{$match->teams[0]->teamName}}</h5>
                                <p class="text-50 lh-1 mb-0">{{$match->teams[0]->ageGroup}}</p>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h2 class="mb-0">Vs.</h2>
                        </div>
                        <div
                            class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                            <div class="mr-md-3 text-center text-md-right">
                                <h5 class="mb-0">{{ $match->teams[1]->teamName }}</h5>
                                <p class="text-50 lh-1 mb-0">{{$match->teams[1]->ageGroup}}</p>
                            </div>
                            <img src="{{ Storage::url($match->teams[1]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="team-logo">
                        </div>
                    </div>

                    <div class="row justify-content-center mt-3">
                        <div class="mr-2">
                            <i class="material-icons text-danger icon--left icon-16pt">event</i>
                            {{ date('D, M d Y', strtotime($match->date)) }}
                        </div>
                        <div class="mr-2">
                            <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                            {{ date('h:i A', strtotime($match->startTime)) }}
                            - {{ date('h:i A', strtotime($match->endTime)) }}
                        </div>
                        <div>
                            <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                            {{ $match->place }}
                        </div>
                    </div>
                </div>
            </a>
        @endforeach

        <div class="page-separator">
            <div class="page-separator__text">Upcoming Training</div>
            <a href="{{ route('training-schedules.index') }}" class="btn btn-outline-secondary bg-white btn-sm ml-auto">
                View More
                <span class="material-icons ml-2 icon-16pt">chevron_right</span>
            </a>
        </div>
        @if(count($upcomingTrainings) == 0)
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">There are no trainings scheduled at this time</small>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            @foreach($upcomingTrainings as $training)
                <div class="col-lg-6">
                    <a class="card" href="{{ route('training-schedules.show', $training->id) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 d-flex flex-column flex-md-row align-items-center">
                                    <img src="{{ Storage::url($training->teams[0]->logo) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="team-logo">
                                    <div class="ml-md-3 text-center text-md-left">
                                        <h5 class="mb-0">{{$training->teams[0]->teamName}}</h5>
                                        <p class="text-50 lh-1 mb-0">{{$training->teams[0]->ageGroup}}</p>
                                    </div>
                                </div>
                                <div class="col-6 d-flex flex-column">
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">event</i>
                                        {{ date('D, M d Y', strtotime($training->date)) }}
                                    </div>
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                                        {{ date('h:i A', strtotime($training->startTime)) }}
                                        - {{ date('h:i A', strtotime($training->endTime)) }}
                                    </div>
                                    <div>
                                        <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                        {{ $training->place }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Team Leaderboard</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="teamsLeaderboardTable">
                        <thead>
                        <tr>
                            <th>Pos.</th>
                            <th>Teams</th>
                            <th>Match Played</th>
                            <th>Won</th>
                            <th>Drawn</th>
                            <th>Lost</th>
                            <th>Goals</th>
                            <th>Goals Conceded</th>
                            <th>Clean Sheets</th>
                            <th>Own Goal</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Player Leaderboard</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="playersLeaderboardTable">
                        <thead>
                        <tr>
                            <th>Pos.</th>
                            <th>Name</th>
                            <th>Team</th>
                            <th>Apps</th>
                            <th>Goals</th>
                            <th>Assists</th>
                            <th>Own Goals</th>
                            <th>Shots</th>
                            <th>Passes</th>
                            <th>Fouls Conceded</th>
                            <th>Yellow Cards</th>
                            <th>Red Cards</th>
                            <th>Saves</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--performance review Section--}}
        <div class="page-separator">
            <div class="page-separator__text">performance review</div>
        </div>
        @if(count($performanceReviews)==0)
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">You haven't added any note performance review to this player
                            yet</small>
                    </div>
                </div>
            </div>
        @endif
        @foreach($performanceReviews as $review)
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div class="flex">
                        <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($review->created_at)) }}</h4>
                        <div class="card-subtitle text-50">Last updated
                            at {{ date('D, M d Y h:i A', strtotime($review->updated_at)) }}</div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="material-icons">
                            more_vert
                        </span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit-note" id="{{ $review->id }}" href="">
                                <span class="material-icons">edit</span>
                                Edit Note
                            </a>
                            <button type="button" class="dropdown-item delete-note" id="{{ $review->id }}">
                                <span class="material-icons">delete</span>
                                Delete Note
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        echo $review->performanceReview
                    @endphp
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            {{--            $('#teamsLeaderboardTable').DataTable({--}}
            {{--                pageLength: 5,--}}
            {{--                processing: true,--}}
            {{--                serverSide: true,--}}
            {{--                ordering: true,--}}
            {{--                ajax: {--}}
            {{--                    url: '{!! route('leaderboards.teams') !!}',--}}
            {{--                },--}}
            {{--                columns: [--}}
            {{--                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },--}}
            {{--                    { data: 'name', name: 'name' },--}}
            {{--                    { data: 'match', name: 'match' },--}}
            {{--                    { data: 'won', name: 'won'},--}}
            {{--                    { data: 'drawn', name: 'drawn'},--}}
            {{--                    { data: 'lost', name: 'lost'},--}}
            {{--                    { data: 'goals', name: 'goals'},--}}
            {{--                    { data: 'goalsConceded', name: 'goalsConceded'},--}}
            {{--                    { data: 'cleanSheets', name: 'cleanSheets'},--}}
            {{--                    { data: 'ownGoals', name: 'ownGoals'},--}}
            {{--                    {--}}
            {{--                        data: 'action',--}}
            {{--                        name: 'action',--}}
            {{--                        orderable: false,--}}
            {{--                        searchable: false,--}}
            {{--                        width: '15%'--}}
            {{--                    },--}}
            {{--                ],--}}
            {{--                order: [[3, 'desc']]--}}
            {{--            });--}}

            {{--            $('#playersLeaderboardTable').DataTable({--}}
            {{--                pageLength: 5,--}}
            {{--                processing: true,--}}
            {{--                serverSide: true,--}}
            {{--                ordering: true,--}}
            {{--                ajax: {--}}
            {{--                    url: '{!! route('leaderboards.players') !!}',--}}
            {{--                },--}}
            {{--                columns: [--}}
            {{--                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },--}}
            {{--                    { data: 'name', name: 'name' },--}}
            {{--                    { data: 'teams', name: 'teams' },--}}
            {{--                    { data: 'apps', name: 'apps'},--}}
            {{--                    { data: 'goals', name: 'goals'},--}}
            {{--                    { data: 'assists', name: 'assists'},--}}
            {{--                    { data: 'ownGoals', name: 'ownGoals'},--}}
            {{--                    { data: 'shots', name: 'shots'},--}}
            {{--                    { data: 'passes', name: 'passes'},--}}
            {{--                    { data: 'fouls', name: 'fouls'},--}}
            {{--                    { data: 'yellowCards', name: 'yellowCards'},--}}
            {{--                    { data: 'redCards', name: 'redCards'},--}}
            {{--                    { data: 'saves', name: 'saves'},--}}
            {{--                    {--}}
            {{--                        data: 'action',--}}
            {{--                        name: 'action',--}}
            {{--                        orderable: false,--}}
            {{--                        searchable: false,--}}
            {{--                        width: '15%'--}}
            {{--                    },--}}
            {{--                ],--}}
            {{--                order: [[4, 'desc']]--}}
            {{--            });--}}

            {{--            const revenueChart = document.getElementById('revenueChart');--}}
            {{--            const teamAgeChart = document.getElementById('teamAgeChart');--}}

            {{--            new Chart(revenueChart, {--}}
            {{--                type: 'line',--}}
            {{--                data: {--}}
            {{--                    labels: @json($revenueChart['label']),--}}
            {{--                    datasets: [{--}}
            {{--                        label: 'Revenue',--}}
            {{--                        data: @json($revenueChart['data']),--}}
            {{--                        borderColor: '#20F4CB',--}}
            {{--                        tension: 0.4,--}}
            {{--                    }]--}}
            {{--                },--}}
            {{--                options: {--}}
            {{--                    responsive: true,--}}
            {{--                },--}}
            {{--            });--}}
            {{--            new Chart(teamAgeChart, {--}}
            {{--                type: 'doughnut',--}}
            {{--                data: {--}}
            {{--                    labels: @json($teamAgeChart['label']),--}}
            {{--                    datasets: [{--}}
            {{--                        label: '# of Player',--}}
            {{--                        data: @json($teamAgeChart['data']),--}}
            {{--                        backgroundColor: ['#20F4CB', '#E52534', '#F9B300', '#00122A']--}}
            {{--                    }]--}}
            {{--                },--}}
            {{--                options: {--}}
            {{--                    responsive: true,--}}
            {{--                },--}}
            {{--            });--}}
        });
    </script>
@endpush
