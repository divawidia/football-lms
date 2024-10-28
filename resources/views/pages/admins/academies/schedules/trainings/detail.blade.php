@extends('layouts.master')
@section('title')
    Training {{ $data['dataSchedule']->eventName  }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    @if(isAllAdmin() || isCoach())
        <x-edit-player-attendance-modal
            :routeGet="route('training-schedules.player', ['schedule' => $data['dataSchedule']->id, 'player' => ':id'])"
            :routeUpdate="route('training-schedules.update-player', ['schedule' => $data['dataSchedule']->id, 'player' => ':id'])"/>

        <x-edit-coach-attendance-modal
            :routeGet="route('training-schedules.coach', ['schedule' => $data['dataSchedule']->id, 'coach' => ':id'])"
            :routeUpdate="route('training-schedules.update-coach', ['schedule' => $data['dataSchedule']->id, 'coach' => ':id'])"/>

        <x-create-schedule-note-modal :routeCreate="route('training-schedules.create-note', $data['dataSchedule']->id)"
                                      :eventName="$data['dataSchedule']->eventName"/>

        <x-edit-schedule-note-modal
            :routeEdit="route('training-schedules.edit-note', ['schedule' => $data['dataSchedule']->id, 'note' => ':id'])"
            :routeUpdate="route('training-schedules.update-note', ['schedule' => $data['dataSchedule']->id, 'note' => ':id'])"
            :eventName="$data['dataSchedule']->eventName"/>

        <x-skill-assessments-modal/>
        <x-edit-skill-assessments-modal/>

        <x-add-performance-review-modal :routeCreate="route('coach.performance-reviews.store', ['player'=> ':id'])"/>
    @endif
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('training-schedules.index') }}" class="nav-link text-70">
                        <i class="material-icons icon--left">keyboard_backspace</i>
                        Back to Training Schedules
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div
            class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <div class="flex">
                <h2 class="text-white mb-0">{{ $data['dataSchedule']->eventName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $data['dataSchedule']->eventType }}
                    ~ {{ $data['dataSchedule']->teams[0]->teamName }}</p>
            </div>
            @if(isAllAdmin() || isCoach())
                <div class="dropdown">
                    <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        Action
                        <span class="material-icons ml-3">
                            keyboard_arrow_down
                        </span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"
                           href="{{ route('training-schedules.edit', $data['dataSchedule']->id) }}">
                            <span class="material-icons">edit</span>
                            Edit Training Schedule
                        </a>

                        @if($data['dataSchedule']->status == '1')
                            <form action="{{ route('deactivate-training', $data['dataSchedule']->id) }}" method="POST">
                                @method("PATCH")
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <span class="material-icons">block</span> End Training
                                </button>
                            </form>
                        @else
                            <form action="{{ route('activate-training', $data['dataSchedule']->id) }}" method="POST">
                                @method("PATCH")
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <span class="material-icons">check_circle</span> Start Training
                                </button>
                            </form>
                        @endif
                        <button type="button" class="dropdown-item delete" id="{{$data['dataSchedule']->id}}">
                            <span class="material-icons">delete</span> Delete Training
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">
                    @if($data['dataSchedule']->status == '1')
                        Status : <span class="badge badge-pill badge-success ml-1">Active</span>
                    @else
                        Status : <span class="badge badge-pill badge-danger ml-1">Ended</span>
                    @endif
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">event</i>
                    {{ date('D, M d Y', strtotime($data['dataSchedule']->date)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                    {{ date('h:i A', strtotime($data['dataSchedule']->startTime)) }}
                    - {{ date('h:i A', strtotime($data['dataSchedule']->endTime)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                    {{ $data['dataSchedule']->place }}
                </li>
                <li class="nav-item navbar-list__item">
                    <div class="media align-items-center">
                        <span class="media-left mr-16pt">
                            <img src="{{Storage::url($data['dataSchedule']->user->foto) }}"
                                 width="30"
                                 alt="avatar"
                                 class="rounded-circle">
                        </span>
                        <div class="media-body">
                            <a class="card-title m-0"
                               href="">Created
                                by {{$data['dataSchedule']->user->firstName}} {{$data['dataSchedule']->user->lastName}}</a>
                            <p class="text-50 lh-1 mb-0">Admin</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    {{--    Attendance Overview    --}}
    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>
        <div class="row card-group-row">
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalParticipant'] }}</div>
                            <div class="flex">
                                <div class="card-title">Total Participants</div>
                            </div>
                        </div>
                        <i class='bx bxs-group icon-32pt text-danger ml-8pt'></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalAttend'] }}</div>
                            <div class="flex">
                                <div class="card-title">Attended</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-check icon-32pt text-danger ml-8pt'></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalDidntAttend'] }}</div>
                            <div class="flex">
                                <div class="card-title">Didn't Attended</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt'></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row card-group-row mb-4">
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalIllness'] }}</div>
                            <div class="flex">
                                <div class="card-title">Illness</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt'></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalInjured'] }}</div>
                            <div class="flex">
                                <div class="card-title">Injured</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt'></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data['totalOthers'] }}</div>
                            <div class="flex">
                                <div class="card-title">Others</div>
                            </div>
                        </div>
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt'></i>
                    </div>
                </div>
            </div>
        </div>

        {{--    Player Attendance    --}}
        <div class="page-separator">
            <div class="page-separator__text">Player Attendance</div>
        </div>
        <div class=".player-attendance">
            @include('pages.admins.academies.schedules.player-attendance-data')
        </div>

        {{--    Coach Attendance    --}}
        <div class="page-separator">
            <div class="page-separator__text">Coach Attendance</div>
        </div>
        <div class=".coach-attendance">
            @include('pages.admins.academies.schedules.coach-attendance-data')
        </div>

        {{--    Training Note    --}}
        <div class="page-separator">
            <div class="page-separator__text">Training Note</div>
            @if(isAllAdmin() || isCoach())
                <a href="#" id="addNewNote" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add new note</a>
            @endif
        </div>
        @if(count($data['dataSchedule']->notes)==0)
            @include('components.alerts.warning', ['text' => "You haven't created any note for this training session", 'createRoute' => null])
        @endif
        <div class="row">
            @foreach($data['dataSchedule']->notes as $note)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="flex">
                                <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($note->created_at)) }}</h4>
                                <div class="card-subtitle text-50">Last updated at {{ date('D, M d Y h:i A', strtotime($note->updated_at)) }}</div>
                            </div>
                            @if(isAllAdmin() || isCoach())
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="material-icons">more_vert</span>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item edit-note" id="{{ $note->id }}" href="">
                                            <span class="material-icons">edit</span>Edit Note
                                        </a>
                                        <button type="button" class="dropdown-item delete-note" id="{{ $note->id }}">
                                            <span class="material-icons">delete</span>Delete Note
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            @php
                                echo $note->note
                            @endphp
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(isAllAdmin() || isCoach())
            <div class="page-separator">
                <div class="page-separator__text">player skill evaluation</div>
            </div>
            <x-player-skill-event-tables
                :route="route('training-schedules.player-skills', ['schedule' => $data['dataSchedule']->id])"
                tableId="playerSkillsTable"/>
        @endif

{{--        @if(isPlayer())--}}
{{--            --}}{{--    Performance Review    --}}
{{--            <div class="page-separator">--}}
{{--                <div class="page-separator__text">Performance Review</div>--}}
{{--                @if(isAllAdmin() || isCoach())--}}
{{--                    <a href="#" id="addNewNote" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add new note</a>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--            @if(count($data['dataSchedule']->notes)==0)--}}
{{--                @include('components.alerts.warning', ['text' => "You haven't created any note for this training session"])--}}
{{--            @else--}}
{{--                @foreach($data['dataSchedule']->notes as $note)--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-header d-flex align-items-center">--}}
{{--                            <div class="flex">--}}
{{--                                <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($note->created_at)) }}</h4>--}}
{{--                                <div class="card-subtitle text-50">Last updated--}}
{{--                                    at {{ date('D, M d Y h:i A', strtotime($note->updated_at)) }}</div>--}}
{{--                            </div>--}}
{{--                            @if(isAllAdmin() || isCoach())--}}
{{--                                <div class="dropdown">--}}
{{--                                    <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                                        <span class="material-icons">more_vert</span>--}}
{{--                                    </button>--}}
{{--                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">--}}
{{--                                        <a class="dropdown-item edit-note" id="{{ $note->id }}" href="">--}}
{{--                                            <span class="material-icons">edit</span>Edit Note--}}
{{--                                        </a>--}}
{{--                                        <button type="button" class="dropdown-item delete-note" id="{{ $note->id }}">--}}
{{--                                            <span class="material-icons">delete</span>Delete Note--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            @endif--}}
{{--                        </div>--}}
{{--                        <div class="card-body">--}}
{{--                            @php--}}
{{--                                echo $note->note--}}
{{--                            @endphp--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            @endif--}}
{{--       @endif--}}
    </div>
@endsection
    @push('addon-script')
        <script>
            $(document).ready(function () {
                const body = $('body');

                // delete competition alert
                body.on('click', '.delete', function () {
                    let id = $(this).attr('id');

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#1ac2a1",
                        cancelButtonColor: "#E52534",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('training-schedules.destroy', ['schedule' => ':id']) }}".replace(':id', id),
                                type: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function () {
                                    Swal.fire({
                                        title: 'Training schedule successfully deleted!',
                                        icon: 'success',
                                        showCancelButton: false,
                                        allowOutsideClick: false,
                                        confirmButtonColor: "#1ac2a1",
                                        confirmButtonText:
                                            'Ok!'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "{{ route('training-schedules.index') }}";
                                        }
                                    });
                                },
                                error: function (error) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Something went wrong when deleting data!",
                                        text: error,
                                    });
                                }
                            });
                        }
                    });
                });

                // delete schedule note alert
                body.on('click', '.delete-note', function () {
                    let id = $(this).attr('id');

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#1ac2a1",
                        cancelButtonColor: "#E52534",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('training-schedules.destroy-note', ['schedule' => $data['dataSchedule']->id, 'note'=>':id']) }}".replace(':id', id),
                                type: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function () {
                                    Swal.fire({
                                        title: 'Training note successfully deleted!',
                                        icon: 'success',
                                        showCancelButton: false,
                                        allowOutsideClick: false,
                                        confirmButtonColor: "#1ac2a1",
                                        confirmButtonText:
                                            'Ok!'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            location.reload();
                                        }
                                    });
                                },
                                error: function (error) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Something went wrong when deleting data!",
                                        text: error,
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
