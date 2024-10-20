@extends('layouts.master')
@section('title')
    Training {{ $data['dataSchedule']->eventName  }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <!-- Modal edit player attendance -->
    <div class="modal fade" id="editPlayerAttendanceModal" tabindex="-1" aria-labelledby="editPlayerAttendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formEditPlayerAttendanceModal">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="playerName"></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="playerId">
                        <div class="form-group">
                            <label class="form-label" for="add_attendanceStatus">Attendance Status</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select" id="add_attendanceStatus" name="attendanceStatus" required>
                                <option value="null" disabled>Select player's attendance status</option>
                                @foreach(['Attended', 'Illness', 'Injured', 'Other'] AS $type)
                                    <option value="{{ $type }}" @selected(old('attendanceStatus') == $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback attendanceStatus_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_note">Note</label>
                            <small>(Optional)</small>
                            <textarea class="form-control" id="add_note" name="note" placeholder="Input the detailed absent reason (if not attended)">{{ old('note') }}</textarea>
                            <span class="invalid-feedback note_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal edit coach attendance -->
    <div class="modal fade" id="editCoachAttendanceModal" tabindex="-1" aria-labelledby="editCoachAttendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formEditCoachAttendanceModal">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="coachName"></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="coachId">
                        <div class="form-group">
                            <label class="form-label" for="add_attendanceStatus">Attendance Status</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select" id="add_attendanceStatus" name="attendanceStatus" required>
                                <option value="null" disabled>Select player's attendance status</option>
                                @foreach(['Attended', 'Illness', 'Injured', 'Other'] AS $type)
                                    <option value="{{ $type }}" @selected(old('attendanceStatus') == $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback attendanceStatus_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_note">Note</label>
                            <small>(Optional)</small>
                            <textarea class="form-control" id="add_note" name="note" placeholder="Input the detailed absent reason (if not attended)">{{ old('note') }}</textarea>
                            <span class="invalid-feedback note_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal create note -->
    <div class="modal fade" id="createNoteModal" tabindex="-1" aria-labelledby="createNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="
                    @if(Auth::user()->hasRole('admin'))
                        {{ route('training-schedules.create-note', $data['dataSchedule']->id) }}
                    @elseif(Auth::user()->hasRole('coach'))
                        {{ route('coach.training-schedules.create-note', $data['dataSchedule']->id) }}
                    @endif" method="post" id="formCreateNoteModal">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="coachName">Create note for {{ $data['dataSchedule']->eventName }} Session</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="add_note">Note</label>
                            <small class="text-danger">*</small>
                            <textarea class="form-control" id="add_note" name="note" placeholder="Input note here ..." required>{{ old('note') }}</textarea>
                            <span class="invalid-feedback note_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal edit note -->
    <div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formUpdateNoteModal">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="coachName">Update note for {{ $data['dataSchedule']->eventName }} Session</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="noteId">
                        <div class="form-group">
                            <label class="form-label" for="edit_note">Note</label>
                            <small class="text-danger">*</small>
                            <textarea class="form-control" id="edit_note" name="note" placeholder="Input note here ..." required></textarea>
                            <span class="invalid-feedback note_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    @if(Auth::user()->hasRole('admin'))
                        <a href="{{ route('training-schedules.index') }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Training Schedules
                        </a>
                    @elseif(Auth::user()->hasRole('coach'))
                        <a href="{{ route('coach.training-schedules.index') }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Training Schedules
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <div class="flex">
                <h2 class="text-white mb-0">{{ $data['dataSchedule']->eventName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $data['dataSchedule']->eventType }} ~ {{ $data['dataSchedule']->teams[0]->teamName }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                    <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item"
                        href="
                        @if(Auth::user()->hasRole('admin'))
                            {{ route('training-schedules.edit', $data['dataSchedule']->id) }}
                        @elseif(Auth::user()->hasRole('coach'))
                            {{ route('coach.training-schedules.edit', $data['dataSchedule']->id) }}
                        @endif">
                        <span class="material-icons">edit</span>
                        Edit Training Schedule
                    </a>

                    @if($data['dataSchedule']->status == '1')
                        <form action="
                            @if(Auth::user()->hasRole('admin'))
                                {{ route('deactivate-training', $data['dataSchedule']->id) }}
                            @elseif(Auth::user()->hasRole('coach'))
                                {{ route('coach.deactivate-training', $data['dataSchedule']->id) }}
                            @endif" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">block</span> End Training
                            </button>
                        </form>
                    @else
                        <form action="
                            @if(Auth::user()->hasRole('admin'))
                                {{ route('activate-training', $data['dataSchedule']->id) }}
                            @elseif(Auth::user()->hasRole('coach'))
                                {{ route('coach.activate-training', $data['dataSchedule']->id) }}
                            @endif" method="POST">
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
                    {{ date('h:i A', strtotime($data['dataSchedule']->startTime)) }} - {{ date('h:i A', strtotime($data['dataSchedule']->endTime)) }}
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
                               href="">Created by {{$data['dataSchedule']->user->firstName}} {{$data['dataSchedule']->user->lastName}}</a>
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
                        <i class='bx bxs-group icon-32pt text-danger ml-8pt' ></i>
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
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt' ></i>
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
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt' ></i>
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
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt' ></i>
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
                        <i class='bx bxs-user-x icon-32pt text-danger ml-8pt' ></i>
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
            <a href="" id="addNewNote" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add new note</a>
        </div>
        @if(count($data['dataSchedule']->notes)==0)
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">You haven't created any note</small>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($data['dataSchedule']->notes as $note)
                    @if(count($data['dataSchedule']->notes)==1)
                    <div class="col-12">
                        @else
                            <div class="col-md-4">
                        @endif
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <div class="flex">
                                    <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($note->created_at)) }}</h4>
                                    <div class="card-subtitle text-50">Last updated at {{ date('D, M d Y h:i A', strtotime($note->updated_at)) }}</div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="material-icons">
                                more_vert
                            </span>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item edit-note" id="{{ $note->id }}" href="">
                                            <span class="material-icons">edit</span>
                                            Edit Note
                                        </a>
                                        <button type="button" class="dropdown-item delete-note" id="{{ $note->id }}">
                                            <span class="material-icons">delete</span>
                                            Delete Note
                                        </button>
                                    </div>
                                </div>
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

        @endif
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
            const body = $('body');
            $('.playerAttendance').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    @if(Auth::user()->hasRole('admin'))
                        url: "{{ route('training-schedules.player', ['schedule' => $data['dataSchedule']->id, 'player' => ":id"]) }}".replace(':id', id),
                    @elseif(Auth::user()->hasRole('coach'))
                        url: "{{ route('coach.training-schedules.player', ['schedule' => $data['dataSchedule']->id, 'player' => ":id"]) }}".replace(':id', id),
                    @endif
                    type: 'get',
                    success: function(res) {
                        $('#editPlayerAttendanceModal').modal('show');

                        const heading = $('#playerName');
                        heading.textContent = 'Update Player '+res.data.user.firstName+' '+res.data.user.lastName+' Attendance';
                        if (res.data.playerAttendance.attendanceStatus === 'Required Action'){
                            $('#editPlayerAttendanceModal #add_attendanceStatus').val('null');
                        } else {
                            $('#editPlayerAttendanceModal #add_attendanceStatus').val(res.data.playerAttendance.attendanceStatus);
                        }
                        $('#editPlayerAttendanceModal #add_note').val(res.data.playerAttendance.note);
                        $('#playerId').val(res.data.playerAttendance.playerId);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            $('.coachAttendance').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    @if(Auth::user()->hasRole('admin'))
                    url: "{{ route('training-schedules.coach', ['schedule' => $data['dataSchedule']->id, 'coach' => ":id"]) }}".replace(':id', id),
                    @elseif(Auth::user()->hasRole('coach'))
                    url: "{{ route('coach.training-schedules.coach', ['schedule' => $data['dataSchedule']->id, 'coach' => ":id"]) }}".replace(':id', id),
                    @endif
                    type: 'get',
                    success: function(res) {
                        $('#editCoachAttendanceModal').modal('show');

                        const heading = document.getElementById('coachName');
                        heading.textContent = 'Update Coach '+res.data.user.firstName+' '+res.data.user.lastName+' Attendance';
                        if (res.data.coachAttendance.attendanceStatus === 'Required Action'){
                            $('#editCoachAttendanceModal #add_attendanceStatus').val('null');
                        } else {
                            $('#editCoachAttendanceModal #add_attendanceStatus').val(res.data.coachAttendance.attendanceStatus);
                        }
                        $('#editCoachAttendanceModal #add_note').val(res.data.coachAttendance.note);
                        $('#coachId').val(res.data.coachAttendance.coachId);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            $('#addNewNote').on('click', function(e) {
                e.preventDefault();
                $('#createNoteModal').modal('show');
            });

            $('.edit-note').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    @if(Auth::user()->hasRole('admin'))
                    url: "{{ route('training-schedules.edit-note', ['schedule' => $data['dataSchedule']->id, 'note' => ":id"]) }}".replace(':id', id),
                    @elseif(Auth::user()->hasRole('coach'))
                    url: "{{ route('coach.training-schedules.edit-note', ['schedule' => $data['dataSchedule']->id, 'note' => ":id"]) }}".replace(':id', id),
                    @endif
                    type: 'get',
                    success: function(res) {
                        $('#editNoteModal').modal('show');

                        const heading = document.getElementById('edit_note');
                        heading.textContent = res.data.note;
                        $('#noteId').val(res.data.id);
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: error,
                        });
                    }
                });
            });

            // update player attendance data
            $('#formEditPlayerAttendanceModal').on('submit', function(e) {
                e.preventDefault();
                const id = $('#playerId').val();
                $.ajax({
                    @if(Auth::user()->hasRole('admin'))
                    url: "{{ route('training-schedules.update-player', ['schedule' => $data['dataSchedule']->id, 'player' => ":id"]) }}".replace(':id', id),
                    @elseif(Auth::user()->hasRole('coach'))
                    url: "{{ route('coach.training-schedules.update-player', ['schedule' => $data['dataSchedule']->id, 'player' => ":id"]) }}".replace(':id', id),
                    @endif
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#editPlayerAttendanceModal').modal('hide');
                        Swal.fire({
                            title: 'Player attendance successfully updated!',
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
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function(key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            // update coach attendance data
            $('#formEditCoachAttendanceModal').on('submit', function(e) {
                e.preventDefault();
                const id = $('#coachId').val();
                $.ajax({
                    @if(Auth::user()->hasRole('admin'))
                    url: "{{ route('training-schedules.update-coach', ['schedule' => $data['dataSchedule']->id, 'coach' => ":id"]) }}".replace(':id', id),
                    @elseif(Auth::user()->hasRole('coach'))
                    url: "{{ route('coach.training-schedules.update-coach', ['schedule' => $data['dataSchedule']->id, 'coach' => ":id"]) }}".replace(':id', id),
                    @endif
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#editCoachAttendanceModal').modal('hide');
                        Swal.fire({
                            title: 'Coach attendance successfully updated!',
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
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function(key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            // create schedule note data
            $('#formCreateNoteModal').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#createNoteModal').modal('hide');
                        Swal.fire({
                            title: 'Training session note successfully added!',
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
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function(key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            // update schedule note data
            $('#formUpdateNoteModal').on('submit', function(e) {
                e.preventDefault();
                const id = $('#noteId').val();
                $.ajax({
                    @if(Auth::user()->hasRole('admin'))
                    url: "{{ route('training-schedules.update-note', ['schedule' => $data['dataSchedule']->id, 'note' => ":id"]) }}".replace(':id', id),
                    @elseif(Auth::user()->hasRole('coach'))
                    url: "{{ route('coach.training-schedules.update-note', ['schedule' => $data['dataSchedule']->id, 'note' => ":id"]) }}".replace(':id', id),
                    @endif
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#editNoteModal').modal('hide');
                        Swal.fire({
                            title: 'Training session note successfully updated!',
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
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);
                        console.log(response);
                        $.each(response.errors, function(key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#edit_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            // delete competition alert
            body.on('click', '.delete', function() {
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
                            @if(Auth::user()->hasRole('admin'))
                            url: "{{ route('training-schedules.destroy', ['schedule' => ':id']) }}".replace(':id', id),
                            @elseif(Auth::user()->hasRole('coach'))
                            url: "{{ route('coach.training-schedules.destroy', ['schedule' => ':id']) }}".replace(':id', id),
                            @endif
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
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
                            error: function(error) {
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
            body.on('click', '.delete-note', function() {
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
                            @if(Auth::user()->hasRole('admin'))
                            url: "{{ route('training-schedules.destroy-note', ['schedule' => $data['dataSchedule']->id, 'note'=>':id']) }}".replace(':id', id),
                            @elseif(Auth::user()->hasRole('coach'))
                            url: "{{ route('coach.training-schedules.destroy-note', ['schedule' => $data['dataSchedule']->id, 'note'=>':id']) }}".replace(':id', id),
                            @endif
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
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
                            error: function(error) {
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
