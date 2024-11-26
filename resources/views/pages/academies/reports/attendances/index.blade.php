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
            <div class="page-separator">
                <div class="page-separator__text">Filter Report</div>
                <div class="form-group ml-auto">
                    <label class="form-label mb-0" for="startDateFilter">Filter by date range</label>
                    <input id="startDateFilter"
                           type="text"
                           class="form-control"
                           placeholder="Start Date"
                           onfocus="(this.type='date')"
                           onblur="(this.type='text')"/>
                </div>
                <div class="form-group ml-2">
                    <label class="form-label mb-0" for="endDateFilter"></label>
                    <input id="endDateFilter"
                           type="text"
                           class="form-control"
                           placeholder="End Date"
                           onfocus="(this.type='date')"
                           onblur="(this.type='text')"/>
                </div>
                <div class="form-group ml-2">
                    <label class="form-label mb-0" for="team">Filter by team</label>
                    <select class="form-control form-select" id="team" data-toggle="select">
                        <option selected disabled>Select team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" data-avatar-src="{{ Storage::url($team->logo) }}">{{ $team->teamName }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="filterBtn" class="btn btn-primary btn-sm ml-2"><span class="material-icons mr-2">filter_list_alt</span> Filter</button>
            </div>

            {{--    Overview    --}}
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-header">
                            <h4 class="card-title">Player Attendance</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceHistoryChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-flex align-items-stretch">
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-header">
                            <h4 class="card-title">Player Attendance</h4>
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
{{--                                <img src="{{ Storage::url($mostAttended->user->foto) }}"--}}
{{--                                     width="50"--}}
{{--                                     height="50"--}}
{{--                                     class="rounded-circle img-object-fit-cover"--}}
{{--                                     alt="player-photo">--}}
{{--                                <div class="flex ml-3">--}}
{{--                                    <h5 class="mb-0">{{ $mostAttended->user->firstName }} {{ $mostAttended->user->lastName }}</h5>--}}
{{--                                    <p class="text-50 lh-1 mb-0">{{ $mostAttended->position->name }}</p>--}}
{{--                                </div>--}}
{{--                                <div class="h2 mb-0 mr-3">{{ $mostAttended->attended_count }}</div>--}}
{{--                                <div class="ml-auto text-right">--}}
{{--                                    <div class="card-title">Event Attended</div>--}}
{{--                                    <p class="card-subtitle text-50">--}}
{{--                                        {{ $mostAttendedPercentage }}% of total event--}}
{{--                                    </p>--}}
{{--                                </div>--}}
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
                                    <div class="card-title">Event Attended</div>
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
                                     alt="player-photo">
                                <div class="flex ml-3">
                                    <h5 class="mb-0"></h5>
                                    <p class="text-50 lh-1 mb-0"></p>
                                </div>
                                <div class="h2 mb-0 mr-3"></div>
                                <div class="ml-auto text-right">
                                    <div class="card-title">Event Absent</div>
                                    <p class="card-subtitle text-50"></p>
                                </div>
                            </div>
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
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! $playerAttendanceDatatablesRoute !!}',
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
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            const attendanceHistoryChart = $('#attendanceHistoryChart');
            const attendanceStatusChart = $('#attendanceStatusChart');
            let lineChart;
            let doughnutChart;

            function fetchAttendanceData(startDate = null, endDate = null, team = null) {
                $.ajax({
                    url: '{{ url()->current() }}',
                    type: 'GET',
                    data: {
                        startDate: startDate,
                        endDate: endDate,
                        team: team,
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

                        $('#mostAttendedImg').attr('src', '{{ Storage::url('') }}'+response.data.mostAttendedPlayer.results.user.foto)
                        $('#mostAttendedName').text(response.data.mostAttendedPlayer.results.user.firstName+' '+response.data.mostAttendedPlayer.results.user.lastName)
                        $('#mostAttendedPosition').text(response.data.mostAttendedPlayer.results.position.name)
                        $('#mostAttendedCount').text(response.data.mostAttendedPlayer.results.attended_count)
                        $('#mostAttendedTotalEvent').text('From '+response.data.mostAttendedPlayer.results.schedules_count+' total events')
                        $('#mostAttendedPercentage').text(response.data.mostAttendedPlayer.mostAttendedPercentage+'% attendance rate')
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
                fetchAttendanceData(startDate, endDate, team);
            });

            fetchAttendanceData();
        });
    </script>
@endpush
