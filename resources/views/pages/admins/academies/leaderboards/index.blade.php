@extends('layouts.master')
@section('title')
    Leaderboard
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container page__container d-flex flex-column">
                <h2 class="mb-0">@yield('title')</h2>
                <ol class="breadcrumb p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">
                        @yield('title')
                    </li>
                </ol>
            </div>
        </div>

        <div class="container page__container page-section">
            {{--    Overview    --}}
            <div class="page-separator">
                <div class="page-separator__text">Team Leaderboard</div>
                <a href="" id="addTeamScorer" class="btn btn-white btn-outline-secondary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>
            </div>

            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="teamLeaderboardTable">
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
                        <table class="table table-hover mb-0" id="playerLeaderboardTable">
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
        $(document).ready(function() {
            const matchHistory = $('#matchHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    { data: 'team', name: 'team' },
                    { data: 'opponentTeam', name: 'opponentTeam' },
                    { data: 'score', name: 'score'},
                    { data: 'date', name: 'date'},
                    { data: 'place', name: 'place'},
                    { data: 'competition', name: 'competition'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            @foreach($competitions as $competition)
                @foreach($competition->groups as $group)
                    const classTable{{$group->id}} = $('#classTable{{$group->id}}').DataTable({
                        processing: true,
                        serverSide: true,
                        ordering: true,
                        ajax: {
                            url: '{!! route('division-managements.index', ['competition'=>$competition->id,'group'=>$group->id]) !!}',
                        },
                        columns: [
                            { data: 'teams', name: 'teams' },
                            { data: 'pivot.matchPlayed', name: 'pivot.matchPlayed' },
                            { data: 'pivot.won', name: 'pivot.won' },
                            { data: 'pivot.drawn', name: 'pivot.drawn' },
                            { data: 'pivot.lost', name: 'pivot.lost' },
                            { data: 'pivot.goalsFor', name: 'pivot.goalsFor' },
                            { data: 'pivot.goalsAgaints', name: 'pivot.goalsAgaints' },
                            { data: 'pivot.goalsDifference', name: 'pivot.goalsDifference' },
                            { data: 'pivot.redCards', name: 'pivot.redCards' },
                            { data: 'pivot.yellowCards', name: 'pivot.yellowCards' },
                            { data: 'pivot.points', name: 'pivot.points' },
                        ],
                        order: [[10, 'desc']]
                    });
                @endforeach
            @endforeach

            {{--const lineChart = document.getElementById('areaChart');--}}
            {{--const doughnutChart = document.getElementById('doughnutChart');--}}

            {{--new Chart(lineChart, {--}}
            {{--    type: 'line',--}}
            {{--    data: {--}}
            {{--        labels: @json($lineChart['label']),--}}
            {{--        datasets: [{--}}
            {{--            label: 'Attended Player',--}}
            {{--            data: @json($lineChart['attended']),--}}
            {{--            borderColor: '#20F4CB',--}}
            {{--            tension: 0.4,--}}
            {{--        }, {--}}
            {{--            label: 'Didnt Attend Player',--}}
            {{--            data: @json($lineChart['didntAttend']),--}}
            {{--            borderColor: '#E52534',--}}
            {{--            tension: 0.4,--}}
            {{--        }]--}}
            {{--    },--}}
            {{--    options: {--}}
            {{--        responsive: true,--}}
            {{--    },--}}
            {{--});--}}
            {{--new Chart(doughnutChart, {--}}
            {{--    type: 'doughnut',--}}
            {{--    data: {--}}
            {{--        labels: @json($doughnutChart['label']),--}}
            {{--        datasets: [{--}}
            {{--            label: '# of Player',--}}
            {{--            data: @json($doughnutChart['data']),--}}
            {{--            backgroundColor: ['#20F4CB', '#E52534', '#F9B300', '#00122A']--}}
            {{--        }]--}}
            {{--    },--}}
            {{--    options: {--}}
            {{--        responsive: true,--}}
            {{--    },--}}
            {{--});--}}
        });
    </script>
@endpush
