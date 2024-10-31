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

            {{--    Overview    --}}
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
{{--                <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>--}}
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-header">
                            <h4 class="card-title">Player Attendance</h4>
                        </div>
                        <div class="card-body">
                            <x-attendance-line-chart chartId="attendanceLineChart" :datas="$lineChart"/>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-flex align-items-stretch">
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-header">
                            <h4 class="card-title">Player Attendance</h4>
                        </div>
                        <div class="card-body">
                            <x-attendance-doughnut-chart chartId="attendanceDoughnutChart" :datas="$doughnutChart"/>
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
                        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                            <div class="card-body d-flex align-items-center flex-row text-left">
                                <img src="{{ Storage::url($mostAttended->user->foto) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="player-photo">
                                <div class="flex ml-3">
                                    <h5 class="mb-0">{{ $mostAttended->user->firstName }} {{ $mostAttended->user->lastName }}</h5>
                                    <p class="text-50 lh-1 mb-0">{{ $mostAttended->position->name }}</p>
                                </div>
                                <div class="h2 mb-0 mr-3">{{ $mostAttended->attended_count }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title">Event Attended</div>
                                    <p class="card-subtitle text-50">
                                        {{ $mostAttendedPercentage }}% of total event
                                    </p>
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
                                <img src="{{ Storage::url($mostDidntAttend->user->foto) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="player-photo">
                                <div class="flex ml-3">
                                    <h5 class="mb-0">{{ $mostDidntAttend->user->firstName }} {{ $mostDidntAttend->user->lastName }}</h5>
                                    <p class="text-50 lh-1 mb-0">{{ $mostDidntAttend->position->name }}</p>
                                </div>
                                <div class="h2 mb-0 mr-3">{{ $mostDidntAttend->didnt_attended_count }}</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title">Event Absent</div>
                                    <p class="card-subtitle text-50">
                                        {{ $mostDidntAttendPercentage }}% of total event
                                    </p>
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
                <x-player-training-histories-table tableId="trainingHistoriesTable" :tableRoute="route('attendance-report.trainingTable', $data['player']->id)"/>

                <div class="page-separator">
                    <div class="page-separator__text">Match Histories</div>
                </div>
                <x-player-match-histories-table tableId="matchHistoriesTable" :tableRoute="route('attendance-report.matchDatatable', $data['player']->id)"/>
            @endif
        </div>
    @endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            @if(isAllAdmin() || isCoach())
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
            @endif
        });
    </script>
@endpush
