@extends('layouts.master')
@section('title')
    Attendance
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

            <div class="row">
                <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                    <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex d-flex align-items-center">
                                <div class="h2 mb-0 mr-3">12</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title">Match Played</div>
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
                                <div class="h2 mb-0 mr-3">12</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title">Goals</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Match
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
                                <div class="h2 mb-0 mr-3">12</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">goals conceded</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Match
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
                                <div class="h2 mb-0 mr-3">12</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">goal difference</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Match
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
                                <div class="h2 mb-0 mr-3">12</div>
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
                                <div class="h2 mb-0 mr-3">12</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">own goals</div>
                                    <p class="card-subtitle text-50">
                                        4
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Match
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
                                <div class="h2 mb-0 mr-3">12</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">wins</div>
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
                                <div class="h2 mb-0 mr-3">12</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">losses</div>
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
                                <div class="h2 mb-0 mr-3">12</div>
                                <div class="ml-auto text-right">
                                    <div class="card-title text-capitalize">draws</div>
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
        </div>
    @endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            {{--const datatable = $('#table').DataTable({--}}
            {{--    processing: true,--}}
            {{--    serverSide: true,--}}
            {{--    ordering: true,--}}
            {{--    ajax: {--}}
            {{--        url: '{!! url()->current() !!}',--}}
            {{--    },--}}
            {{--    columns: [--}}
            {{--        { data: 'name', name: 'name' },--}}
            {{--        { data: 'teams', name: 'teams' },--}}
            {{--        { data: 'totalEvent', name: 'totalEvent' },--}}
            {{--        { data: 'match', name: 'match'},--}}
            {{--        { data: 'training', name: 'training'},--}}
            {{--        { data: 'attended', name: 'attended'},--}}
            {{--        { data: 'absent', name: 'absent'},--}}
            {{--        { data: 'illness', name: 'illness'},--}}
            {{--        { data: 'injured', name: 'injured'},--}}
            {{--        { data: 'others', name: 'others'},--}}
            {{--        {--}}
            {{--            data: 'action',--}}
            {{--            name: 'action',--}}
            {{--            orderable: false,--}}
            {{--            searchable: false,--}}
            {{--            width: '15%'--}}
            {{--        },--}}
            {{--    ]--}}
            {{--});--}}

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
