@extends('layouts.master')
@section('title')
    Performance Report
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
                <div class="page-separator__text">Overview</div>
                <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>
            </div>

            <div class="row mb-3">
                <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                    <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex d-flex align-items-center">
                                <div class="h2 mb-0 mr-3">{{ $overviewStats['wins'] }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title">Wins</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
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
                                <div class="h2 mb-0 mr-3">{{ $overviewStats['losses'] }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title">Losses</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
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
                                <div class="h2 mb-0 mr-3">{{ $overviewStats['draws'] }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">Draws</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
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
                                <div class="h2 mb-0 mr-3">{{ $overviewStats['matchPlayed'] }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">match played</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
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
                                <div class="h2 mb-0 mr-3">{{ $overviewStats['goals'] }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">goals</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
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
                                <div class="h2 mb-0 mr-3">{{ $overviewStats['goalsConceded'] }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">goals conceded</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
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
                                <div class="h2 mb-0 mr-3">{{ $overviewStats['goalsDifference'] }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">goal difference</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
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
                                <div class="h2 mb-0 mr-3">{{ $overviewStats['cleanSheets'] }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">clean sheets</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
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
                                <div class="h2 mb-0 mr-3">{{ $overviewStats['ownGoals'] }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">own goals</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
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
                @foreach($latestMatches as $latestMatch)
                    <div class="col-lg-6">
                        <a class="card" href="{{ route('match-schedules.show', $latestMatch->id) }}">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                                        <img src="{{ Storage::url($latestMatch->teams[0]->logo) }}"
                                             width="50"
                                             height="50"
                                             class="rounded-circle img-object-fit-cover"
                                             alt="team-logo">
                                        <div class="ml-md-3 text-center text-md-left">
                                            <h6 class="mb-0">{{ $latestMatch->teams[0]->teamName }}</h6>
                                            <p class="text-50 lh-1 mb-0">{{ $latestMatch->teams[0]->ageGroup }}</p>
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <h2 class="mb-0">{{ $latestMatch->teams[0]->pivot->teamScore }} - {{ $latestMatch->teams[1]->pivot->teamScore }}</h2>
                                    </div>
                                    <div class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                                        <div class="mr-md-3 text-center text-md-right">
                                            <h6 class="mb-0">{{ $latestMatch->teams[1]->teamName }}</h6>
                                            <p class="text-50 lh-1 mb-0">{{ $latestMatch->teams[1]->ageGroup }}</p>
                                        </div>
                                        <img src="{{ Storage::url($latestMatch->teams[1]->logo) }}"
                                             width="50"
                                             height="50"
                                             class="rounded-circle img-object-fit-cover"
                                             alt="team-logo">
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-3">
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">event</i>
                                        {{ date('D, M d Y', strtotime($latestMatch->date)) }}
                                    </div>
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                                        {{ date('h:i A', strtotime($latestMatch->startTime)) }} - {{ date('h:i A', strtotime($latestMatch->endTime)) }}
                                    </div>
                                    <div>
                                        <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                        {{ $latestMatch->place }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="page-separator">
                <div class="page-separator__text">Match History</div>
            </div>

            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="matchHistoryTable">
                            <thead>
                            <tr>
                                <th>Teams</th>
                                <th>Opponent Teams</th>
                                <th>Score</th>
                                <th>Match Date</th>
                                <th>Location</th>
                                <th>Competition</th>
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
                <div class="page-separator__text">Competition Leaderboard</div>
                <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>
            </div>

            @foreach($competitions as $competition)
                <div class="page-separator">
                    <div class="page-separator__text">{{ $competition->name }}</div>
                </div>
                @foreach($competition->groups as $group)
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-header">
                            <h5 class="mb-0">{{ $group->groupName }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="classTable{{ $group->id }}">
                                    <thead>
                                    <tr>
                                        <th>Team</th>
                                        <th>Match Played</th>
                                        <th>won</th>
                                        <th>drawn</th>
                                        <th>lost</th>
                                        <th>goals For</th>
                                        <th>goals Againts</th>
                                        <th>goals Difference</th>
                                        <th>red Cards</th>
                                        <th>yellow Cards</th>
                                        <th>points</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach


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
