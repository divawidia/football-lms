@extends('layouts.master')
@section('title')
    Match Schedules
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="container page__container d-flex flex-column pt-32pt">
        <h2 class="mb-0">@yield('title')</h2>
        <ol class="breadcrumb p-0 m-0">
            <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
            <li class="breadcrumb-item active">
                @yield('title')
            </li>
        </ol>
    </div>

    <div class="container page__container page-section">
        @if(isAllAdmin())
            <a href="{{  route('match-schedules.create') }}" class="btn btn-primary mb-3" id="add-new">
                    <span class="material-icons mr-2">
                        add
                    </span>
                Add New
            </a>
        @endif
        <x-match-tables :route="$tableRoute" tableId="tables"/>

        <div class="card">
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
        $(document).ready(function () {
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
                events: @json($events)
            });
            calendar.render();
        });
    </script>
@endpush
