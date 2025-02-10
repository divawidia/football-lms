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

    <div class="container page-section">
        <div class="card">
            <div class="nav-tabs-container">
                <ul class="nav nav-pills text-capitalize">
                    <x-tabs.item title="Overview" link="overview" :active="true"/>
                    <x-tabs.item title="Upcoming Matches" link="matches"/>
                    <x-tabs.item title="Upcoming Trainings" link="trainings"/>
                    <x-tabs.item title="Team Leaderboard" link="teams"/>
                    <x-tabs.item title="Player Leaderboard" link="players"/>
                </ul>
            </div>
        </div>

        <div class="tab-content mt-3">
            {{--    Overview    --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row">
                    <x-stats-with-icon-card  title="Total Players" :datas="$dataOverview['totalPlayers']" :dataThisMonth="$dataOverview['thisMonthTotalPlayers']" icon="fa fa-user"/>
                    <x-stats-with-icon-card  title="Total Coaches" :datas="$dataOverview['totalCoaches']" :dataThisMonth="$dataOverview['thisMonthTotalPlayers']" icon="fa fa-user-tie"/>
                    <x-stats-with-icon-card  title="Total Teams" :datas="$dataOverview['totalTeams']" :dataThisMonth="$dataOverview['thisMonthTotalTeams']" icon="fa fa-users"/>
                    <x-stats-with-icon-card  title="Total Admins" :datas="$totalAdmins" :dataThisMonth="$totalAdminsThisMonth" icon="fa fa-user"/>
                    <x-stats-with-icon-card  title="Competitions Joined" :datas="$dataOverview['totalCompetitions']" :dataThisMonth="$dataOverview['thisMonthTotalCompetitions']" icon="fa fa-trophy"/>
                    <x-stats-with-icon-card  title="Match Schedule" :datas="$dataOverview['totalUpcomingMatches']" icon="fa fa-calendar-day"/>
                    <x-stats-with-icon-card  title="Training Schedules" :datas="$dataOverview['totalUpcomingTrainings']" icon="fa fa-calendar-day"/>
                    <x-stats-with-icon-card  title="Total Revenue" :datas="'Rp. '.$dataOverview['totalRevenues']" :dataThisMonth="$dataOverview['thisMonthTotalRevenues']" icon="fa fa-money-bill"/>
                    <x-stats-with-icon-card  title="Revenue Growth" :datas="$dataOverview['revenueGrowth']" icon="bx bx-line-chart" subtitle="From last month"/>
                </div>

                <div class="row card-group-row">
                    <div class="col-md-7">
                        <x-charts.academy-revenue-chart :revenueGrowth="$dataOverview['revenueGrowth']"/>
                    </div>
                    <div class="col-md-5 d-flex">
                        <div class="card">
                            <div class="card-body d-flex flex-row align-items-center flex-0">
                                <div class="card-title h5">Team Age Groups</div>
                            </div>
                            <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                                <canvas id="teamAgeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{--    Upcoming Matches    --}}
            <div class="tab-pane fade show" id="matches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Matches</div>
                    <x-buttons.link-button size="sm" color="white border" margin="ml-auto" :href="route('match-schedules.index')" icon="chevron_right" text="View More"/>
                </div>
                @if(count($upcomingMatches) == 0)
                    <x-warning-alert text="There are no team matches scheduled at this time"/>
                @endif
                @foreach($upcomingMatches as $match)
                    <x-cards.match-card :match="$match" :latestMatch="false"/>
                @endforeach
            </div>

            {{--    Upcoming Trainings    --}}
            <div class="tab-pane fade show" id="trainings-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Trainings</div>
                    <x-buttons.link-button size="sm" color="white border" margin="ml-auto" :href="route('training-schedules.index')" icon="chevron_right" text="View More"/>
                </div>
                @if(count($upcomingTrainings) == 0)
                    <x-warning-alert text="There are no team trainings scheduled at this time"/>
                @endif
                @foreach($upcomingTrainings as $training)
                    <x-cards.training-card :training="$training"/>
                @endforeach
            </div>

            {{--    Team Leaderboard    --}}
            <div class="tab-pane fade show" id="teams-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Team Leaderboard</div>
                </div>
                <x-tables.team-leaderboard :teamsLeaderboardRoute="route('leaderboards.teams')"/>
            </div>

            {{--    Player Leaderboard    --}}
            <div class="tab-pane fade show" id="players-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Player Leaderboard</div>
                </div>
                <x-tables.player-leaderboard :playersLeaderboardRoute="route('leaderboards.players')"/>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            const teamAgeChart = document.getElementById('teamAgeChart');

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
