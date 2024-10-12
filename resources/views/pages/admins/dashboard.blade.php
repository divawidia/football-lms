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
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container page__container page-section">
        {{--    Overview    --}}
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
{{--            <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>--}}
        </div>
        <div class="row card-group-row">
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalPlayers'] }}</div>
                            <div class="flex">
                                <div class="card-title">Total Players</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['thisMonthTotalPlayers'] }}
                                    @if($dataOverview['thisMonthTotalPlayers'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                        <i class='fa fa-user icon-24pt text-danger' ></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalCoaches'] }}</div>
                            <div class="flex">
                                <div class="card-title">Total Coaches</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['thisMonthTotalCoaches'] }}
                                    @if($dataOverview['thisMonthTotalCoaches'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                        <i class="fa fa-user-tie icon-24pt text-danger"></i>
{{--                        <i class='bx bxs-user-check icon-32pt text-danger ml-8pt'></i>--}}
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalTeams'] }}</div>
                            <div class="flex">
                                <div class="card-title">Total Teams</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['thisMonthTotalTeams'] }}
                                    @if($dataOverview['thisMonthTotalTeams'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                        <i class='fa fa-users icon-24pt text-danger ml-8pt' ></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row card-group-row">
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalAdmins'] }}</div>
                            <div class="flex">
                                <div class="card-title">Total Admins</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['thisMonthTotalAdmins'] }}
                                    @if($dataOverview['thisMonthTotalAdmins'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                        <i class='fa fa-user icon-24pt text-danger'></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalCompetitions'] }}</div>
                            <div class="flex">
                                <div class="card-title">Competitions Joined</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['thisMonthTotalCompetitions'] }}
                                    @if($dataOverview['thisMonthTotalCompetitions'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                        <i class='fa fa-trophy icon-24pt text-danger' ></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalUpcomingMatches'] }}</div>
                            <div class="flex">
                                <div class="card-title">Match Schedule</div>
                            </div>
                        </div>
                        <i class='fa fa-calendar-day icon-24pt text-danger'></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row card-group-row">
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalUpcomingTrainings'] }}</div>
                            <div class="flex">
                                <div class="card-title">Training Schedules</div>
                            </div>
                        </div>
                        <i class='fa fa-calendar-day icon-24pt text-danger'></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h3 mb-0 mr-3">Rp. {{ $dataOverview['totalRevenues'] }}</div>
                            <div class="flex">
                                <div class="card-title">Total Revenue</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['thisMonthTotalRevenues'] }}
                                    @if($dataOverview['thisMonthTotalRevenues'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                        <i class='fa fa-money-bill icon-24pt text-danger'></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $dataOverview['totalTeams'] }}</div>
                            <div class="flex">
                                <div class="card-title">Revenue Growth</div>
                                <p class="card-subtitle text-50">
                                    {{ $dataOverview['totalTeams'] }}
                                    @if($dataOverview['thisMonthTotalPlayers'] > 0)
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    @endif
                                    From Last Month
                                </p>
                            </div>
                        </div>
                        <i class='bx bx-line-chart icon-32pt text-danger'></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row card-group-row">
            <div class="col-md-7 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex flex-row align-items-center flex-0">
                        <div class="h2 mb-0 mr-3">Rp. {{ $dataOverview['totalRevenues'] }}</div>
                        <div class="flex">
                            <div class="card-title">Total Revenue</div>
                            <div class="card-subtitle text-50 d-flex align-items-center">2.6% <i class="material-icons text-accent icon-16pt ml-4pt">keyboard_arrow_up</i></div>
                        </div>
                        <div class="ml-3 align-self-start">
                            <div class="dropdown mb-2">
                                <a href=""
                                   class="dropdown-toggle"
                                   data-toggle="dropdown"
                                   data-caret="false"><i class="material-icons text-50">more_horiz</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href=""
                                       class="dropdown-item">View report</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body flex-0 row">
                        <div class="col-6">
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Paid</strong></span>
                                    {{ $dataOverview['totalPaidInvoices'] }} Invoices
                                </span>
                                <span class="mx-3">Rp. {{ $dataOverview['sumPaidInvoices'] }}</span>
                            </small>
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Past Due</strong></span>
                                    {{ $dataOverview['totalPastDueInvoices'] }} Invoices
                                </span>
                                <span class="mx-3">Rp. {{ $dataOverview['sumPastDueInvoices'] }}</span>
                            </small>
                        </div>
                        <div class="col-6">
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Open</strong></span>
                                    {{ $dataOverview['totalOpenInvoices'] }} Invoices
                                </span>
                                <span class="mx-3">Rp. {{ $dataOverview['sumOpenInvoices'] }}</span>
                            </small>
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Uncollectible</strong></span>
                                    {{ $dataOverview['totalUncollectInvoices'] }} Invoices
                                </span>
                                <span class="mx-3">Rp. {{ $dataOverview['sumUncollectInvoices'] }}</span>
                            </small>
                        </div>
                    </div>
                    <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-5 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex flex-row align-items-center flex-0">
                        <div class="card-title">Team Age Groups</div>
                        <div class="ml-auto align-self-start">
                            <div class="dropdown mb-2">
                                <a href=""
                                   class="dropdown-toggle"
                                   data-toggle="dropdown"
                                   data-caret="false"><i class="material-icons text-50">more_horiz</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href=""
                                       class="dropdown-item">View report</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                        <canvas id="teamAgeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Upcoming Matches</div>
                <a href="" class="btn btn-outline-secondary bg-white btn-sm ml-auto">
                View More
                    <span class="material-icons ml-2 icon-16pt">chevron_right</span>
            </a>
        </div>

        @foreach($upcomingMatches as $match)
            <a class="card" href="">
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
            <a href="" class="btn btn-outline-secondary bg-white btn-sm ml-auto">
                View More
                <span class="material-icons ml-2 icon-16pt">chevron_right</span>
            </a>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <a class="card" href="">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 d-flex flex-column flex-md-row align-items-center">
                                <img src=""
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="team-logo">
                                <div class="ml-md-3 text-center text-md-left">
                                    <h5 class="mb-0">Tim U-12</h5>
                                    <p class="text-50 lh-1 mb-0">U-12</p>
                                </div>
                            </div>
                            <div class="col-6 d-flex flex-column">
                                <div class="mr-2">
                                    <i class="material-icons text-danger icon--left icon-16pt">event</i>
                                    Thu, 25 Sept 2024
                                </div>
                                <div class="mr-2">
                                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                                    12:00 PM - 14:00 PM
                                </div>
                                <div>
                                    <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                    Old Trafford
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-6">
                <a class="card" href="">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 d-flex flex-column flex-md-row align-items-center">
                                <img src=""
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="team-logo">
                                <div class="ml-md-3 text-center text-md-left">
                                    <h5 class="mb-0">Tim U-12</h5>
                                    <p class="text-50 lh-1 mb-0">U-12</p>
                                </div>
                            </div>
                            <div class="col-6 d-flex flex-column">
                                <div class="mr-2">
                                    <i class="material-icons text-danger icon--left icon-16pt">event</i>
                                    Thu, 25 Sept 2024
                                </div>
                                <div class="mr-2">
                                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                                    12:00 PM - 14:00 PM
                                </div>
                                <div>
                                    <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                    Old Trafford
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <div class="row">
            <div class="col-lg-6">
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
            </div>
            <div class="col-lg-6">
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
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#teamsLeaderboardTable').DataTable({
                pageLength: 5,
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('leaderboards.teams') !!}',
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'match', name: 'match' },
                    { data: 'won', name: 'won'},
                    { data: 'drawn', name: 'drawn'},
                    { data: 'lost', name: 'lost'},
                    { data: 'goals', name: 'goals'},
                    { data: 'goalsConceded', name: 'goalsConceded'},
                    { data: 'cleanSheets', name: 'cleanSheets'},
                    { data: 'ownGoals', name: 'ownGoals'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[3, 'desc']]
            });

            $('#playersLeaderboardTable').DataTable({
                pageLength: 5,
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('leaderboards.players') !!}',
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'teams', name: 'teams' },
                    { data: 'apps', name: 'apps'},
                    { data: 'goals', name: 'goals'},
                    { data: 'assists', name: 'assists'},
                    { data: 'ownGoals', name: 'ownGoals'},
                    { data: 'shots', name: 'shots'},
                    { data: 'passes', name: 'passes'},
                    { data: 'fouls', name: 'fouls'},
                    { data: 'yellowCards', name: 'yellowCards'},
                    { data: 'redCards', name: 'redCards'},
                    { data: 'saves', name: 'saves'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[4, 'desc']]
            });

            const revenueChart = document.getElementById('revenueChart');
            const teamAgeChart = document.getElementById('teamAgeChart');

            new Chart(revenueChart, {
                type: 'line',
                data: {
                    labels: @json($revenueChart['label']),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($revenueChart['data']),
                        borderColor: '#20F4CB',
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                },
            });
            new Chart(teamAgeChart, {
                type: 'doughnut',
                data: {
                    labels: @json($teamAgeChart['label']),
                    datasets: [{
                        label: '# of Player',
                        data: @json($teamAgeChart['data']),
                        backgroundColor: ['#20F4CB', '#E52534', '#F9B300', '#00122A']
                    }]
                },
                options: {
                    responsive: true,
                },
            });
        });
    </script>
@endpush
