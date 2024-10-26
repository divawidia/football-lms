@extends('layouts.master')
@section('title')
    Dashboard
@endsection
@section('page-title')
    Dashboard
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left">
            <div class="flex d-flex flex-column flex-sm-row align-items-center">
                <div class="mr-sm-24pt text-sm-left">
                    <h2 class="mb-0">@yield('title')</h2>
                    <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('coach.dashboard') }}">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Managed Teams</div>
        </div>

        <div class="row">
            @foreach($dataOverview['teamsManaged'] as $team)
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
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalMatchPlayed'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Match Played</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['totalMatchPlayed'] }}
                                    @if($dataOverview['thisMonthTotalMatchPlayed'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalGoals'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Goals</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['totalGoals'] }}
                                    @if($dataOverview['thisMonthTotalGoals'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalGoalsConceded'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">goals conceded</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['totalGoalsConceded'] }}
                                    @if($dataOverview['thisMonthTotalGoalsConceded'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['goalsDifference'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">goal difference</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['goalsDifference'] }}
                                    @if($dataOverview['thisMonthGoalsDifference'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalCleanSheets'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">clean sheets</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['totalCleanSheets'] }}
                                    @if($dataOverview['thisMonthTotalCleanSheets'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalOwnGoals'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">own goals</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['totalOwnGoals'] }}
                                    @if($dataOverview['thisMonthTotalOwnGoals'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalWins'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">wins</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['totalWins'] }}
                                    @if($dataOverview['thisMonthTotalWins'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalLosses'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">losses</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['totalLosses'] }}
                                    @if($dataOverview['thisMonthTotalLosses'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalDraws'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">draws</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['totalDraws'] }}
                                    @if($dataOverview['thisMonthTotalDraws'] > 0)
                                    <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Latest Match</div>
        </div>

        <div class="row">
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
                    <div class="col-lg-6">
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
                                        <h2 class="mb-0">{{ $match->teams[0]->pivot->teamScore }} - {{ $match->teams[1]->pivot->teamScore }}</h2>
                                    </div>
                                    <div class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
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
                                        {{ date('h:i A', strtotime($match->startTime)) }} - {{ date('h:i A', strtotime($match->endTime)) }}
                                    </div>
                                    <div>
                                        <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                        {{ $match->place }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
            @endforeach
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
                        <div class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
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
                            {{ date('h:i A', strtotime($match->startTime)) }} - {{ date('h:i A', strtotime($match->endTime)) }}
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
                                        {{ date('h:i A', strtotime($training->startTime)) }} - {{ date('h:i A', strtotime($training->endTime)) }}
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
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
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
