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
                @if(Auth::user()->hasRole('admin'))
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                @elseif(Auth::user()->hasRole('coach'))
                    <li class="breadcrumb-item"><a href="{{ route('coach.dashboard') }}">Home</a></li>
                @endif
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>

        <div class="container page__container page-section">
            @if(Auth::user()->hasRole('admin'))
                <a href="{{  route('match-schedules.create') }}" class="btn btn-primary mb-3" id="add-new">
                    <span class="material-icons mr-2">
                        add
                    </span>
                    Add New
                </a>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="table">
                            <thead>
                            <tr>
                                <th>Team</th>
                                <th>Opponent</th>
                                <th>Match Date</th>
                                <th>Location</th>
                                <th>Competition</th>
                                <th>Match Type</th>
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
            $(document).ready(function() {
                const datatable = $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! url()->current() !!}',
                    },
                    columns: [
                        { data: 'team', name: 'team' },
                        { data: 'opponentTeam', name: 'opponentTeam' },
                        { data: 'date', name: 'date' },
                        { data: 'place', name: 'place'},
                        { data: 'competition', name: 'competition'},
                        { data: 'matchType', name: 'matchType'},
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

                $('body').on('click', '.delete', function() {
                    let id = $(this).attr('id');

                    Swal.fire({
                        title: "Are you sure to delete this match?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#1ac2a1",
                        cancelButtonColor: "#E52534",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                @if(Auth::user()->hasRole('admin'))
                                url: "{{ route('match-schedules.destroy', ['schedule' => ':id']) }}".replace(':id', id),
                                @endif
                                type: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Match successfully deleted!",
                                    });
                                    datatable.ajax.reload();
                                },
                                error: function(error) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Oops...",
                                        text: "Something went wrong when deleting data!",
                                    });
                                }
                            });
                        }
                    });
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
                    events: @json($events)
                });
                calendar.render();
            });
        </script>
    @endpush
