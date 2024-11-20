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
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            </ol>
        </div>
    </div>

    <div class="container  page-section">
        {{--    Overview    --}}
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
            {{--            <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>--}}
        </div>
        <div class="row">
            <x-stats-with-icon-card  title="Total Players" :datas="$dataOverview['totalPlayers']" :dataThisMonth="$dataOverview['thisMonthTotalPlayers']" icon="fa fa-user"/>
            <x-stats-with-icon-card  title="Total Coaches" :datas="$dataOverview['totalCoaches']" :dataThisMonth="$dataOverview['thisMonthTotalPlayers']" icon="fa fa-user-tie"/>
            <x-stats-with-icon-card  title="Total Teams" :datas="$dataOverview['totalTeams']" :dataThisMonth="$dataOverview['thisMonthTotalTeams']" icon="fa fa-users"/>
            <x-stats-with-icon-card  title="Total Admins" :datas="$dataOverview['totalAdmins']" :dataThisMonth="$dataOverview['thisMonthTotalAdmins']" icon="fa fa-user"/>
            <x-stats-with-icon-card  title="Competitions Joined" :datas="$dataOverview['totalCompetitions']" :dataThisMonth="$dataOverview['thisMonthTotalCompetitions']" icon="fa fa-trophy"/>
            <x-stats-with-icon-card  title="Match Schedule" :datas="$dataOverview['totalUpcomingMatches']" icon="fa fa-calendar-day"/>
            <x-stats-with-icon-card  title="Training Schedules" :datas="$dataOverview['totalUpcomingTrainings']" icon="fa fa-calendar-day"/>
            <x-stats-with-icon-card  title="Total Revenue" :datas="'Rp. '.$dataOverview['totalRevenues']" :dataThisMonth="$dataOverview['thisMonthTotalRevenues']" icon="fa fa-money-bill"/>
            <x-stats-with-icon-card  title="Revenue Growth" :datas="$dataOverview['revenueGrowth']" icon="bx bx-line-chart" subtitle="From last month"/>
        </div>

        <div class="row card-group-row">
            <div class="col-md-7 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex flex-row align-items-center flex-0">
                        <div class="h2 mb-0 mr-3" id="totalRevenue"></div>
                        <div class="flex">
                            <div class="card-title h5">Total Revenue</div>
                            <div class="card-subtitle text-50 d-flex align-items-center">
                                {{ $dataOverview['revenueGrowth'] }}
                                @if($dataOverview['revenueGrowth'] > 0)
                                    <i class="material-icons text-success icon-16pt">keyboard_arrow_up</i>
                                @elseif($dataOverview['revenueGrowth'] < 0)
                                    <i class="material-icons text-danger icon-16pt">keyboard_arrow_up</i>
                                @endif
                                From Last Month
                            </div>
                        </div>
                        <div class="ml-3 align-self-start">
                            <div class="dropdown mb-2">
                                <a href="" class="dropdown-toggle" data-toggle="dropdown" data-caret="false">Filter by : <span id="filter-type">All Time</span><i class="material-icons text-50 pb-1">expand_more</i></a>
                                <div id="filterRevenue" class="dropdown-menu dropdown-menu-right">
                                    <button type="button" class="dropdown-item" id="today">Today</button>
                                    <button type="button" class="dropdown-item" id="weekly">Weekly</button>
                                    <button type="button" class="dropdown-item" id="monthly">Monthly</button>
                                    <button type="button" class="dropdown-item" id="yearly">Yearly</button>
                                    <button type="button" class="dropdown-item" id="allTime">All Time</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body flex-0 row">
                        <div class="col-6">
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Paid</strong></span>
                                    <span id="totalPaidInvoices"></span>
                                </span>
                                <span class="mx-3" id="sumPaidInvoices"></span>
                            </small>
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Past Due</strong></span>
                                    <span id="totalPastDueInvoices"></span>
                                </span>
                                <span class="mx-3" id="sumPastDueInvoices"></span>
                            </small>
                        </div>
                        <div class="col-6">
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Open</strong></span>
                                    <span id="totalOpenInvoices"></span>
                                </span>
                                <span class="mx-3" id="sumOpenInvoices"></span>
                            </small>
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Uncollectible</strong></span>
                                    <span id="totalUncollectInvoices"></span>
                                </span>
                                <span class="mx-3" id="sumUncollectInvoices"></span>
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
                        <div class="card-title h5">Team Age Groups</div>
                    </div>
                    <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                        <canvas id="teamAgeChart"></canvas>
                    </div>
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
                        <small class="text-black-100">There are no team matches scheduled at this time</small>
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
                        {{ date('D, M d Y', strtotime($match->startDatetime)) }}
                    </div>
                    <div class="mr-2">
                        <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                        {{ date('h:i A', strtotime($match->startDatetime)) }}
                        - {{ date('h:i A', strtotime($match->endDatetime)) }}
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
                                    {{ date('D, M d Y', strtotime($training->startDatetime)) }}
                                </div>
                                <div class="mr-2">
                                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                                    {{ date('h:i A', strtotime($training->startDatetime)) }}
                                    - {{ date('h:i A', strtotime($training->endDatetime)) }}
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
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#teamsLeaderboardTable').DataTable({
                pageLength: 5,
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('leaderboards.teams') !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'match', name: 'match'},
                    {data: 'won', name: 'won'},
                    {data: 'drawn', name: 'drawn'},
                    {data: 'lost', name: 'lost'},
                    {data: 'goals', name: 'goals'},
                    {data: 'goalsConceded', name: 'goalsConceded'},
                    {data: 'cleanSheets', name: 'cleanSheets'},
                    {data: 'ownGoals', name: 'ownGoals'},
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
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'teams', name: 'teams'},
                    {data: 'apps', name: 'apps'},
                    {data: 'goals', name: 'goals'},
                    {data: 'assists', name: 'assists'},
                    {data: 'ownGoals', name: 'ownGoals'},
                    {data: 'shots', name: 'shots'},
                    {data: 'passes', name: 'passes'},
                    {data: 'fouls', name: 'fouls'},
                    {data: 'yellowCards', name: 'yellowCards'},
                    {data: 'redCards', name: 'redCards'},
                    {data: 'saves', name: 'saves'},
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
            let myChart;

            function fetchChartData(filter, filterText) {
                $.ajax({
                    url: '{{ route('admin.revenue-chart-data') }}',
                    type: 'GET',
                    data: {
                        filter: filter,
                    },
                    success: function (response) {
                        if (myChart) myChart.destroy(); // Destroy previous chart instance
                        myChart = new Chart(revenueChart, {
                            type: 'line',
                            data: response.data.chart,
                            options: {
                                responsive: true,
                            },
                        });
                        $('#filter-type').text(filterText)
                        $('#totalPaidInvoices').text(response.data.totalPaidInvoices + ' Invoices')
                        $('#totalPastDueInvoices').text(response.data.totalPastDueInvoices + ' Invoices')
                        $('#totalOpenInvoices').text(response.data.totalOpenInvoices + ' Invoices')
                        $('#totalUncollectInvoices').text(response.data.totalUncollectInvoices + ' Invoices')
                        $('#sumPaidInvoices').text('Rp. ' + response.data.sumPaidInvoices)
                        $('#sumPastDueInvoices').text('Rp. ' + response.data.sumPastDueInvoices)
                        $('#sumOpenInvoices').text('Rp. ' + response.data.sumOpenInvoices)
                        $('#sumUncollectInvoices').text('Rp. ' + response.data.sumUncollectInvoices)
                        $('#totalRevenue').text('Rp. ' + response.data.totalRevenue)
                    },
                    error: function (err) {
                        console.error(err);
                        alert('Failed to fetch chart data.');
                    },
                });
            }

            $('#filterRevenue .dropdown-item').on('click', function () {
                const filter = $(this).attr('id');
                const filterText = $(this).text();
                fetchChartData(filter, filterText);
            });

            fetchChartData('allTime');
            {{--new Chart(revenueChart, {--}}
            {{--    type: 'line',--}}
            {{--    data: {--}}
            {{--        labels: @json($revenueChart['label']),--}}
            {{--        datasets: [{--}}
            {{--            label: 'Revenue',--}}
            {{--            data: @json($revenueChart['data']),--}}
            {{--            borderColor: '#20F4CB',--}}
            {{--            tension: 0.4,--}}
            {{--        }]--}}
            {{--    },--}}
            {{--    options: {--}}
            {{--        responsive: true,--}}
            {{--    },--}}
            {{--});--}}
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
