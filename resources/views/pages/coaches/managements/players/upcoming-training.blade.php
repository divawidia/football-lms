@extends('layouts.master')
@section('title')
    {{ $data->user->firstName  }} {{ $data->user->lastName  }} Upcoming Trainings
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('coach.player-managements.show', $data->id) }}" class="nav-link text-70">
                        <i class="material-icons icon--left">keyboard_backspace</i>
                        Back to Player Profile
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="player-photo">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $data->user->firstName  }} {{ $data->user->lastName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player - {{ $data->position->name }}</p>
            </div>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="table">
                        <thead>
                        <tr>
                            <th>Training/Practice</th>
                            <th>Team</th>
                            <th>training date</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-header">
                <h4 class="card-title">Calendar</h4>
            </div>
            <div class="card-body">
                <div id='calendar'></div>
            </div>
        </div>
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
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    { data: 'eventName', name: 'eventName' },
                    { data: 'team', name: 'team' },
                    { data: 'date', name: 'date' },
                    { data: 'place', name: 'place'},
                    { data: 'status', name: 'status' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[2, 'asc']]
            });

            const calendarEl = document.getElementById('calendar');

            function getInitialView() {
                if (window.innerWidth >= 768 && window.innerWidth < 1200) {
                    return 'timeGridWeek';
                } else if (window.innerWidth <= 768) {
                    return 'listMonth';
                } else {
                    return 'dayGridMonth';
                }
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'local',
                editable: false,
                droppable: false,
                selectable: true,
                initialView: getInitialView(),
                themeSystem: 'bootstrap',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                // responsive
                windowResize: function (view) {
                    const newView = getInitialView();
                    calendar.changeView(newView);
                },
                events: @json($trainingCalendar)
            });
            calendar.render();
        });
    </script>
@endpush
