@extends('layouts.master')

@section('title')
    Attendance Report
@endsection

@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        {{--    Filter    --}}
        <div class="page-separator">
            <div class="page-separator__text">Attendance Report Filter</div>
        </div>
        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-form__body card-body-form-group flex">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group mb-0 mb-lg-3">
                            <label class="form-label mb-0" for="startDateFilter">Filter by date range</label>
                            <input id="startDateFilter"
                                   type="text"
                                   class="form-control"
                                   placeholder="Start Date"
                                   onfocus="(this.type='date')"
                                   onblur="(this.type='text')"/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label mb-0" for="endDateFilter"></label>
                            <input id="endDateFilter"
                                   type="text"
                                   class="form-control"
                                   placeholder="End Date"
                                   onfocus="(this.type='date')"
                                   onblur="(this.type='text')"/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label mb-0" for="team">Filter by team</label>
                            <select class="form-control form-select" id="team" data-toggle="select">
                                <option selected disabled>Select team</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}"
                                            data-avatar-src="{{ Storage::url($team->logo) }}">{{ $team->teamName }}</option>
                                @endforeach
                                <option value="{{ null }}">All teams</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn bg-alt border-left border-top border-top-sm-0 rounded-0" type="button" id="filterBtn">
                <i class="material-icons text-primary icon-20pt">refresh</i>
            </button>
        </div>

        <div class="card">
            <div class="nav-tabs-container">
                <ul class="nav nav-pills text-capitalize">
                    <x-tabs.item title="Training Attendance Overview" link="training-attendance" :active="true"/>
                    <x-tabs.item title="Match Attendance Overview" link="match-attendance"/>
                </ul>
            </div>
        </div>

        <div class="tab-content mt-3">\
            <div class="tab-pane fade show active" id="training-attendance-tab" role="tabpanel">
                {{--   Training Overview    --}}
                <div class="page-separator">
                    <div class="page-separator__text">Player's Training Attendance Overview</div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Attendance Status Trend</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="trainingAttendanceHistoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 d-flex align-items-stretch">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Total Players by Attendance Status</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="trainingAttendanceStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="page-separator">
                            <div class="page-separator__text">most attended player</div>
                        </div>
                        <div class="card">
                            <div class="card-body d-flex align-items-center flex-row text-left">
                                <img src="" width="50" height="50" class="rounded-circle img-object-fit-cover" alt="player-photo" id="trainingMostAttendedImg">
                                <div class="flex ml-3">
                                    <h5 class="mb-0" id="trainingMostAttendedName"></h5>
                                    <p class="text-50 lh-1 mb-0" id="trainingMostAttendedPosition"></p>
                                </div>
                                <div class="h2 mb-0 mr-3" id="trainingMostAttendedCount"></div>
                                <div class="ml-auto text-right">
                                    <div class="card-title" id="trainingAttendedTitle">Training Attended</div>
                                    <p class="card-subtitle text-50" id="trainingMostAttendedTotalEvent"></p>
                                    <p class="card-subtitle text-50" id="trainingMostAttendedPercentage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="page-separator">
                            <div class="page-separator__text">most absent player</div>
                        </div>
                        <div class="card">
                            <div class="card-body d-flex align-items-center flex-row text-left">
                                <img src=""
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="player-photo"
                                     id="trainingMostDidntAttendImg">
                                <div class="flex ml-3">
                                    <h5 class="mb-0" id="trainingMostDidntAttendName"></h5>
                                    <p class="text-50 lh-1 mb-0" id="trainingMostDidntAttendPosition"></p>
                                </div>
                                <div class="h2 mb-0 mr-3" id="trainingMostDidntAttendCount"></div>
                                <div class="ml-auto text-right">
                                    <div class="card-title" id="trainingAbsentTitle">Training Absent</div>
                                    <p class="card-subtitle text-50" id="trainingMostDidntAttendTotalEvent"></p>
                                    <p class="card-subtitle text-50" id="trainingMostDidntAttendPercentage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">Training Sessions</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table :headers="['#', 'Team', 'Training/Practice', 'Date', 'Status', 'Total Players', 'Total Attended', 'Total Ill', 'Total Injured', 'Total Others', 'Total Required Action', 'Action']" tableId="trainingTable"/>
                    </div>
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">player attendance</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table :headers="['#', 'Name', 'Teams', 'Total Sessions', 'Attended', 'Absent', 'Injured', 'Illness', 'Others', 'Required Action', 'Action']" tableId="trainingPlayersAttendanceTable"/>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="match-attendance-tab" role="tabpanel">
                {{--   Match Overview    --}}
                <div class="page-separator">
                    <div class="page-separator__text">Player's Match Attendance Overview</div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Attendance Status Trend</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="matchAttendanceHistoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 d-flex align-items-stretch">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Total Players by Attendance Status</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="matchAttendanceStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="page-separator">
                            <div class="page-separator__text">most attended player</div>
                        </div>
                        <div class="card">
                            <div class="card-body d-flex align-items-center flex-row text-left">
                                <img src=""
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="player-photo"
                                     id="matchMostAttendedImg">
                                <div class="flex ml-3">
                                    <h5 class="mb-0" id="matchMostAttendedName"></h5>
                                    <p class="text-50 lh-1 mb-0" id="matchMostAttendedPosition"></p>
                                </div>
                                <div class="h2 mb-0 mr-3" id="matchMostAttendedCount"></div>
                                <div class="ml-auto text-right">
                                    <div class="card-title" id="matchAttendedTitle">match Attended</div>
                                    <p class="card-subtitle text-50" id="matchMostAttendedTotalEvent"></p>
                                    <p class="card-subtitle text-50" id="matchMostAttendedPercentage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="page-separator">
                            <div class="page-separator__text">most absent player</div>
                        </div>
                        <div class="card">
                            <div class="card-body d-flex align-items-center flex-row text-left">
                                <img src=""
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="player-photo"
                                     id="mostDidntAttendImg">
                                <div class="flex ml-3">
                                    <h5 class="mb-0" id="matchMostDidntAttendName"></h5>
                                    <p class="text-50 lh-1 mb-0" id="matchMostDidntAttendPosition"></p>
                                </div>
                                <div class="h2 mb-0 mr-3" id="matchMostDidntAttendCount"></div>
                                <div class="ml-auto text-right">
                                    <div class="card-title" id="matchAbsentTitle">match Absent</div>
                                    <p class="card-subtitle text-50" id="matchMostDidntAttendTotalEvent"></p>
                                    <p class="card-subtitle text-50" id="matchMostDidntAttendPercentage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">match Sessions</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table :headers="['#', 'Teams', 'Date', 'Status', 'Total Players', 'Total Attended', 'Total Ill', 'Total Injured', 'Total Others', 'Total Required Action', 'Action']" tableId="matchTable"/>
                    </div>
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">player attendance</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table :headers="['#', 'Name', 'Teams', 'Total Sessions', 'Attended', 'Absent', 'Injured', 'Illness', 'Others', 'Required Action', 'Action']" tableId="matchPlayersAttendanceTable"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            const trainingAttendanceHistoryChart = $('#trainingAttendanceHistoryChart');
            const trainingAttendanceStatusChart = $('#trainingAttendanceStatusChart');
            const matchAttendanceHistoryChart = $('#matchAttendanceHistoryChart');
            const matchAttendanceStatusChart = $('#matchAttendanceStatusChart');
            let trainingHistoryChart;
            let matchHistoryChart;
            let trainingStatusChart;
            let matchStatusChart;

            function trainingPlayersAttendanceTable(startDate = null, endDate = null, team = null) {
                $('#trainingPlayersAttendanceTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{{ route('attendance-report.training-players') }}',
                        type: "GET",
                        data: function (d) {
                            d.startDate = startDate;
                            d.endDate = endDate;
                            d.team = team;
                            return d;
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'teams', name: 'teams'},
                        {data: 'totalTraining', name: 'totalTraining'},
                        {data: 'attended', name: 'attended'},
                        {data: 'absent', name: 'absent'},
                        {data: 'illness', name: 'illness'},
                        {data: 'injured', name: 'injured'},
                        {data: 'others', name: 'others'},
                        {data: 'requiredAction', name: 'requiredAction'},
                        {data: 'action', name: 'action', orderable: false, searchable: false,},
                    ],
                    bDestroy: true
                });
            }

            function trainingTable(startDate = null, endDate = null, team = null) {
                return $('#trainingTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! url()->route('attendance-report.trainings') !!}',
                        type: "GET",
                        data: function (d) {
                            d.startDate = startDate;
                            d.endDate = endDate;
                            d.team = team;
                            return d;
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'team', name: 'team'},
                        {data: 'eventName', name: 'eventName'},
                        {data: 'date', name: 'date'},
                        {data: 'status', name: 'status'},
                        {data: 'totalPlayers', name: 'totalPlayers'},
                        {data: 'playerAttended', name: 'playerAttended'},
                        {data: 'playerIllness', name: 'playerIllness'},
                        {data: 'playerInjured', name: 'playerInjured'},
                        {data: 'playerOther', name: 'playerOther'},
                        {data: 'playerRequiredAction', name: 'playerRequiredAction'},
                        {data: 'action', name: 'action', orderable: false, searchable: false,},
                    ],
                    order: [[2, 'desc']],
                    bDestroy: true
                });
            }

            function matchPlayersAttendanceTable(startDate = null, endDate = null, team = null) {
                $('#matchPlayersAttendanceTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{{ route('attendance-report.match-players') }}',
                        type: "GET",
                        data: function (d) {
                            d.startDate = startDate;
                            d.endDate = endDate;
                            d.team = team;
                            return d;
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'teams', name: 'teams'},
                        {data: 'totalMatch', name: 'totalMatch'},
                        {data: 'attended', name: 'attended'},
                        {data: 'absent', name: 'absent'},
                        {data: 'illness', name: 'illness'},
                        {data: 'injured', name: 'injured'},
                        {data: 'others', name: 'others'},
                        {data: 'requiredAction', name: 'requiredAction'},
                        {data: 'action', name: 'action', orderable: false, searchable: false,},
                    ],
                    bDestroy: true
                });
            }

            function matchTable(startDate = null, endDate = null, team = null) {
                return $('#matchTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! url()->route('attendance-report.matches') !!}',
                        type: "GET",
                        data: function (d) {
                            d.startDate = startDate;
                            d.endDate = endDate;
                            d.team = team;
                            return d;
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'eventName', name: 'eventName'},
                        {data: 'date', name: 'date'},
                        {data: 'status', name: 'status'},
                        {data: 'totalPlayers', name: 'totalPlayers'},
                        {data: 'playerAttended', name: 'playerAttended'},
                        {data: 'playerIllness', name: 'playerIllness'},
                        {data: 'playerInjured', name: 'playerInjured'},
                        {data: 'playerOther', name: 'playerOther'},
                        {data: 'playerRequiredAction', name: 'playerRequiredAction'},
                        {data: 'action', name: 'action', orderable: false, searchable: false,},
                    ],
                    order: [[2, 'desc']],
                    bDestroy: true
                });
            }


            function fetchAttendanceData(startDate = null, endDate = null, team = null) {
                $.ajax({
                    url: '{{ route('attendance-report.attendance') }}',
                    type: 'GET',
                    data: {
                        startDate: startDate,
                        endDate: endDate,
                        team: team,
                    },
                    success: function (response) {
                        if (trainingHistoryChart) trainingHistoryChart.destroy(); // Destroy previous chart instance
                        trainingHistoryChart = new Chart(trainingAttendanceHistoryChart, {
                            type: 'line',
                            data: response.data.trainingAttendanceHistoryChart,
                            options: {
                                responsive: true,
                            },
                        })
                        if (trainingStatusChart) trainingStatusChart.destroy(); // Destroy previous chart instance
                        trainingStatusChart = new Chart(trainingAttendanceStatusChart, {
                            type: 'doughnut',
                            data: response.data.trainingAttendanceStatusChart,
                            options: {
                                responsive: true,
                            },
                        })

                        if (matchHistoryChart) matchHistoryChart.destroy(); // Destroy previous chart instance
                        matchHistoryChart = new Chart(matchAttendanceHistoryChart, {
                            type: 'line',
                            data: response.data.matchAttendanceHistoryChart,
                            options: {
                                responsive: true,
                            },
                        })
                        if (matchStatusChart) matchStatusChart.destroy(); // Destroy previous chart instance
                        matchStatusChart = new Chart(matchAttendanceStatusChart, {
                            type: 'doughnut',
                            data: response.data.matchAttendanceStatusChart,
                            options: {
                                responsive: true,
                            },
                        })

                        if (response.data.trainingMostAttendedPlayer.mostAttendedPercentage === null) {
                            $('#trainingMostAttendedImg').attr('src', '').hide()
                            $('#trainingMostAttendedName').text('No data found')
                            $('#trainingMostAttendedPosition').text('')
                            $('#trainingMostAttendedCount').text('')
                            $('#trainingMostAttendedTotalEvent').text('')
                            $('#trainingMostAttendedPercentage').text('')
                            $('#trainingAttendedTitle').text('')
                        } else {
                            $('#trainingMostAttendedImg').attr('src', '{{ Storage::url('') }}' + response.data.trainingMostAttendedPlayer.results.user.foto).show()
                            $('#trainingMostAttendedName').text(response.data.trainingMostAttendedPlayer.results.user.firstName + ' ' + response.data.trainingMostAttendedPlayer.results.user.lastName)
                            $('#trainingMostAttendedPosition').text(response.data.trainingMostAttendedPlayer.results.position.name)
                            $('#trainingMostAttendedCount').text(response.data.trainingMostAttendedPlayer.results.attended_count)
                            $('#trainingAttendedTitle').text('Training Attended')
                            $('#trainingMostAttendedTotalEvent').text('From ' + response.data.trainingMostAttendedPlayer.results.schedules_count + ' total training(s)')
                            $('#trainingMostAttendedPercentage').text(response.data.trainingMostAttendedPlayer.mostAttendedPercentage + '% attendance rate')
                        }

                        if (response.data.trainingMostDidntAttendPlayer.mostDidntAttendPercentage === null) {
                            $('#trainingMostDidntAttendImg').attr('src', '').hide()
                            $('#trainingMostDidntAttendName').text('No data found')
                            $('#trainingMostDidntAttendPosition').text('')
                            $('#trainingMostDidntAttendCount').text('')
                            $('#trainingMostDidntAttendTotalEvent').text('')
                            $('#trainingMostDidntAttendPercentage').text('')
                            $('#trainingAbsentTitle').text('')
                        } else {
                            $('#trainingMostDidntAttendImg').attr('src', '{{ Storage::url('') }}' + response.data.trainingMostDidntAttendPlayer.results.user.foto).show()
                            $('#trainingMostDidntAttendName').text(response.data.trainingMostDidntAttendPlayer.results.user.firstName + ' ' + response.data.trainingMostDidntAttendPlayer.results.user.lastName)
                            $('#trainingMostDidntAttendPosition').text(response.data.trainingMostDidntAttendPlayer.results.position.name)
                            $('#trainingMostDidntAttendCount').text(response.data.trainingMostDidntAttendPlayer.results.didnt_attended_count)
                            $('#trainingAbsentTitle').text('Training Absent')
                            $('#trainingMostDidntAttendTotalEvent').text('From ' + response.data.trainingMostDidntAttendPlayer.results.schedules_count + ' total training(s)')
                            $('#trainingMostDidntAttendPercentage').text(response.data.trainingMostDidntAttendPlayer.mostDidntAttendPercentage + '% absent rate')
                        }

                        if (response.data.matchMostAttendedPlayer.mostAttendPercentage === null) {
                            $('#matchMostAttendedImg').attr('src', '').hide()
                            $('#matchMostAttendedName').text('No data found')
                            $('#matchMostAttendedPosition').text('')
                            $('#matchMostAttendedCount').text('')
                            $('#matchMostAttendedTotalEvent').text('')
                            $('#matchMostAttendedPercentage').text('')
                            $('#matchAttendedTitle').text('')
                        } else {
                            $('#matchMostAttendedImg').attr('src', '{{ Storage::url('') }}' + response.data.matchMostAttendedPlayer.results.user.foto).show()
                            $('#matchMostAttendedName').text(response.data.matchMostAttendedPlayer.results.user.firstName + ' ' + response.data.matchMostAttendedPlayer.results.user.lastName)
                            $('#matchMostAttendedPosition').text(response.data.matchMostAttendedPlayer.results.position.name)
                            $('#matchMostAttendedCount').text(response.data.matchMostAttendedPlayer.results.attended_count)
                            $('#matchAttendedTitle').text('Match Attended')
                            $('#matchMostAttendedTotalEvent').text('From ' + response.data.matchMostAttendedPlayer.results.schedules_count + ' total match(es)')
                            $('#matchMostAttendedPercentage').text(response.data.matchMostAttendedPlayer.mostAttendedPercentage + '% absent rate')
                        }

                        if (response.data.matchMostDidntAttendPlayer.mostDidntAttendPercentage === null) {
                            $('#mostDidntAttendImg').attr('src', '').hide()
                            $('#matchMostDidntAttendName').text('No data found')
                            $('#matchMostDidntAttendPosition').text('')
                            $('#matchMostDidntAttendCount').text('')
                            $('#matchMostDidntAttendTotalEvent').text('')
                            $('#matchMostDidntAttendPercentage').text('')
                            $('#matchAbsentTitle').text('')
                        } else {

                            $('#mostDidntAttendImg').attr('src', '{{ Storage::url('') }}' + response.data.matchMostDidntAttendPlayer.results.user.foto).show()
                            $('#matchMostDidntAttendName').text(response.data.matchMostDidntAttendPlayer.results.user.firstName + ' ' + response.data.matchMostDidntAttendPlayer.results.user.lastName)
                            $('#matchMostDidntAttendPosition').text(response.data.matchMostDidntAttendPlayer.results.position.name)
                            $('#matchMostDidntAttendCount').text(response.data.matchMostDidntAttendPlayer.results.didnt_attended_count)
                            $('#matchAbsentTitle').text('Match Absent')
                            $('#matchMostDidntAttendTotalEvent').text('From ' + response.data.matchMostDidntAttendPlayer.results.schedules_count + ' total match(es)')
                            $('#matchMostDidntAttendPercentage').text(response.data.matchMostDidntAttendPlayer.mostDidntAttendPercentage + '% absent rate')
                        }
                    },
                    error: function (err) {
                        console.error(err);
                        alert('Failed to fetch chart data.');
                    },
                });
            }

            $('#filterBtn').on('click', function () {
                const startDate = $('#startDateFilter').val();
                const endDate = $('#endDateFilter').val();
                const team = $('#team').val();

                trainingPlayersAttendanceTable(startDate, endDate, team);
                trainingTable(startDate, endDate, team);
                matchPlayersAttendanceTable(startDate, endDate, team);
                matchTable(startDate, endDate, team);
                fetchAttendanceData(startDate, endDate, team);
            });

            fetchAttendanceData();
            trainingPlayersAttendanceTable();
            trainingTable();
            matchPlayersAttendanceTable();
            matchTable();
        });
    </script>
@endpush
