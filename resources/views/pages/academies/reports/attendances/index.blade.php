@extends('layouts.master')
@section('title')
    Attendance
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
                    <li class="breadcrumb-item active">
                        @yield('title')
                    </li>
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
                        <div class="col-lg-3">
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
                        <div class="col-lg-3">
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
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label mb-0" for="team">Filter by team</label>
                                <select class="form-control form-select" id="team" data-toggle="select">
                                    <option selected disabled>Select team</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" data-avatar-src="{{ Storage::url($team->logo) }}">{{ $team->teamName }}</option>
                                    @endforeach
                                    <option value="{{ null }}">All teams</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label mb-0" for="eventType">Filter by event type</label>
                                <select class="form-control form-select" id="eventType" data-toggle="select">
                                    <option selected disabled>Select event type</option>
                                    <option value="Match">Match</option>
                                    <option value="Training">Training</option>
                                    <option value="{{ null }}">Both event type</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn bg-alt border-left border-top border-top-sm-0 rounded-0" type="button" id="filterBtn"><i class="material-icons text-primary icon-20pt">refresh</i></button>
            </div>

            {{--    Overview    --}}
            <div class="page-separator">
                <div class="page-separator__text">Player Attendance Overview</div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-header">
                            <h4 class="card-title">Attendance Status Trend</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceHistoryChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-flex align-items-stretch">
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-header">
                            <h4 class="card-title">Total Players by Attendance Status</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            @if(isAllAdmin() || isCoach())
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
                                    id="mostAttendedImg">
                                <div class="flex ml-3">
                                    <h5 class="mb-0" id="mostAttendedName"></h5>
                                    <p class="text-50 lh-1 mb-0" id="mostAttendedPosition"></p>
                                </div>
                                <div class="h2 mb-0 mr-3" id="mostAttendedCount"></div>
                                <div class="ml-auto text-right">
                                    <div class="card-title" id="attendedTitle">Event Attended</div>
                                    <p class="card-subtitle text-50" id="mostAttendedTotalEvent"></p>
                                    <p class="card-subtitle text-50" id="mostAttendedPercentage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="page-separator">
                            <div class="page-separator__text">most absent player</div>
                        </div>
                        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                            <div class="card-body d-flex align-items-center flex-row text-left">
                                <img src=""
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="player-photo"
                                     id="mostDidntAttendImg">
                                <div class="flex ml-3">
                                    <h5 class="mb-0" id="mostDidntAttendName"></h5>
                                    <p class="text-50 lh-1 mb-0" id="mostDidntAttendPosition"></p>
                                </div>
                                <div class="h2 mb-0 mr-3" id="mostDidntAttendCount"></div>
                                <div class="ml-auto text-right">
                                    <div class="card-title" id="absentTitle">Event Absent</div>
                                    <p class="card-subtitle text-50" id="mostDidntAttendTotalEvent"></p>
                                    <p class="card-subtitle text-50" id="mostDidntAttendPercentage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">Events</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="eventsTable">
                                <thead>
                                <tr>
                                    <th>Team</th>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Event Type</th>
                                    <th>Status</th>
                                    <th>Total Players</th>
                                    <th>Total Attended</th>
                                    <th>Total Ill</th>
                                    <th>Total Injured</th>
                                    <th>Total Others</th>
                                    <th>Total Required Action</th>
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
                    <div class="page-separator__text">player attendance</div>
                </div>
                <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Teams</th>
                                    <th>Total Events</th>
                                    <th>Match</th>
                                    <th>Training</th>
                                    <th>Attended</th>
                                    <th>Absent</th>
                                    <th>Injured</th>
                                    <th>Illness</th>
                                    <th>Others</th>
                                    <th>Required Action</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif(isPlayer())
                <div class="row card-group-row mb-4">
                    @include('components.stats-card', ['title' => 'Total Attended','data' => $data['totalAttended'], 'dataThisMonth' => $data['thisMonthTotalAttended']])
                    @include('components.stats-card', ['title' => 'Total Illness','data' => $data['totalIllness'], 'dataThisMonth' => $data['thisMonthTotalIllness']])
                    @include('components.stats-card', ['title' => 'Total Injured','data' => $data['totalInjured'], 'dataThisMonth' => $data['thisMonthTotalInjured']])
                    @include('components.stats-card', ['title' => 'Total Other','data' => $data['totalOther'], 'dataThisMonth' => $data['thisMonthTotalOther']])
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">Training Histories</div>
                </div>
{{--                <x-player-training-histories-table tableId="trainingHistoriesTable" :tableRoute="route('attendance-report.trainingTable', $data['player']->id)"/>--}}

                <div class="page-separator">
                    <div class="page-separator__text">Match Histories</div>
                </div>
{{--                <x-player-match-histories-table tableId="matchHistoriesTable" :tableRoute="route('attendance-report.matchDatatable', $data['player']->id)"/>--}}
            @endif
        </div>
    @endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            function playersTable(startDate = null, endDate = null, team = null, eventType = null){
                $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! $playerAttendanceDatatablesRoute !!}',
                        type: "GET",
                        data: function(d) {
                            d.startDate = startDate;
                            d.endDate = endDate;
                            d.team = team;
                            d.eventType = eventType;
                            return d;
                        }
                    },
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'teams', name: 'teams' },
                        { data: 'totalEvent', name: 'totalEvent' },
                        { data: 'match', name: 'match'},
                        { data: 'training', name: 'training'},
                        { data: 'attended', name: 'attended'},
                        { data: 'absent', name: 'absent'},
                        { data: 'illness', name: 'illness'},
                        { data: 'injured', name: 'injured'},
                        { data: 'others', name: 'others'},
                        { data: 'requiredAction', name: 'requiredAction'},
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    bDestroy: true
                });
            }

            function eventsTable(startDate = null, endDate = null, team = null, eventType = null){
                return $('#eventsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! url()->route('attendance-report.events-index') !!}',
                        type: "GET",
                        data: function(d) {
                            d.startDate = startDate;
                            d.endDate = endDate;
                            d.team = team;
                            d.eventType = eventType;
                            return d;
                        }
                    },
                    columns: [
                        { data: 'team', name: 'team' },
                        { data: 'eventName', name: 'eventName' },
                        { data: 'date', name: 'date' },
                        { data: 'eventType', name: 'eventType'},
                        { data: 'status', name: 'status'},
                        { data: 'totalPlayers', name: 'totalPlayers'},
                        { data: 'playerAttended', name: 'playerAttended'},
                        { data: 'playerIllness', name: 'playerIllness'},
                        { data: 'playerInjured', name: 'playerInjured'},
                        { data: 'playerOther', name: 'playerOther'},
                        { data: 'playerRequiredAction', name: 'playerRequiredAction'},
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    order: [[2, 'desc']],
                    bDestroy: true
                });
            }

            const attendanceHistoryChart = $('#attendanceHistoryChart');
            const attendanceStatusChart = $('#attendanceStatusChart');
            let lineChart;
            let doughnutChart;

            function fetchAttendanceData(startDate = null, endDate = null, team = null, eventType = null) {
                $.ajax({
                    url: '{{ url()->current() }}',
                    type: 'GET',
                    data: {
                        startDate: startDate,
                        endDate: endDate,
                        team: team,
                        eventType: eventType,
                    },
                    success: function (response) {
                        if (lineChart) lineChart.destroy(); // Destroy previous chart instance
                        lineChart = new Chart(attendanceHistoryChart, {
                            type: 'line',
                            data: response.data.lineChart,
                            options: {
                                responsive: true,
                            },
                        })

                        if (doughnutChart) doughnutChart.destroy(); // Destroy previous chart instance
                        doughnutChart = new Chart(attendanceStatusChart, {
                            type: 'doughnut',
                                data: response.data.doughnutChart,
                            options: {
                                responsive: true,
                            },
                        })

                        if (response.data.mostAttendedPlayer.mostAttendedPercentage === null) {
                            $('#mostAttendedImg').attr('src', '').hide()
                            $('#mostAttendedName').text('No data found')
                            $('#mostAttendedPosition').text('')
                            $('#mostAttendedCount').text('')
                            $('#mostAttendedTotalEvent').text('')
                            $('#mostAttendedPercentage').text('')
                            $('#attendedTitle').text('')
                        } else {
                            $('#mostAttendedImg').attr('src', '{{ Storage::url('') }}'+response.data.mostAttendedPlayer.results.user.foto).show()
                            $('#mostAttendedName').text(response.data.mostAttendedPlayer.results.user.firstName+' '+response.data.mostAttendedPlayer.results.user.lastName)
                            $('#mostAttendedPosition').text(response.data.mostAttendedPlayer.results.position.name)
                            $('#mostAttendedCount').text(response.data.mostAttendedPlayer.results.attended_count)
                            $('#attendedTitle').text('Event Attended')
                            $('#mostAttendedTotalEvent').text('From '+response.data.mostAttendedPlayer.results.schedules_count+' total events')
                            $('#mostAttendedPercentage').text(response.data.mostAttendedPlayer.mostAttendedPercentage+'% attendance rate')
                        }
                        
                        if (response.data.mostDidntAttendPlayer.mostDidntAttendPercentage === null) {
                            $('#mostDidntAttendImg').attr('src', '').hide()
                            $('#mostDidntAttendName').text('No data found')
                            $('#mostDidntAttendPosition').text('')
                            $('#mostDidntAttendCount').text('')
                            $('#mostDidntAttendTotalEvent').text('')
                            $('#mostDidntAttendPercentage').text('')
                            $('#absentTitle').text('')
                        } else {
                            $('#mostDidntAttendImg').attr('src', '{{ Storage::url('') }}'+response.data.mostDidntAttendPlayer.results.user.foto).show()
                            $('#mostDidntAttendName').text(response.data.mostDidntAttendPlayer.results.user.firstName+' '+response.data.mostAttendedPlayer.results.user.lastName)
                            $('#mostDidntAttendPosition').text(response.data.mostDidntAttendPlayer.results.position.name)
                            $('#mostDidntAttendCount').text(response.data.mostDidntAttendPlayer.results.didnt_attended_count)
                            $('#absentTitle').text('Event Absent')
                            $('#mostDidntAttendTotalEvent').text('From '+response.data.mostDidntAttendPlayer.results.schedules_count+' total events')
                            $('#mostDidntAttendPercentage').text(response.data.mostDidntAttendPlayer.mostDidntAttendPercentage+'% absent rate')
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
                const eventType = $('#eventType').val();
                fetchAttendanceData(startDate, endDate, team, eventType);
                eventsTable(startDate, endDate, team, eventType);
                playersTable(startDate, endDate, team, eventType);
            });

            fetchAttendanceData();
            eventsTable();
            playersTable();
        });
    </script>
@endpush
