@extends('layouts.master')
@section('title')
    Training {{ $data->eventName  }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <!-- Modal edit group modal -->
    <div class="modal fade" id="editPlayerAttendanceModal" tabindex="-1" aria-labelledby="editPlayerAttendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="#" method="post" id="formEditGroupModal">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="playerId">
                        <div class="form-group">
                            <label class="form-label" for="add_attendanceStatus">Attendance Status</label>
                            <small class="text-danger">*</small>
                            <select class="form-control form-select" id="add_attendanceStatus" name="attendanceStatus" required data-toggle="select">
                                <option disabled selected>Select competition type</option>
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
                            <textarea class="form-control" id="add_note" name="note" placeholder="Input the detailed absent reason">{{ old('note') }}</textarea>
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
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <div class="flex">
                <h2 class="text-white mb-0">{{ $data->eventName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $data->eventType }} ~ {{ $data->teams[0]->teamName }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                    <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('training-schedules.edit', $data->id) }}"><span class="material-icons">edit</span> Edit Training Schedule</a>
                    @if($data->status == '1')
                        <form action="{{ route('deactivate-training', $data->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">block</span> Deactivate Competition
                            </button>
                        </form>
                    @else
                        <form action="{{ route('activate-training', $data->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">check_circle</span> Activate Competition
                            </button>
                        </form>
                    @endif
                    <button type="button" class="dropdown-item delete" id="{{$data->id}}">
                        <span class="material-icons">delete</span> Delete Competition
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left icon-16pt">event</i>
                    {{ date('D, M d Y', strtotime($data->date)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left icon-16pt">schedule</i>
                    {{ date('h:i A', strtotime($data->startTime)) }} - {{ date('h:i A', strtotime($data->endTime)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left icon-16pt">location_on</i>
                    {{ $data->place }}
                </li>
                <li class="nav-item navbar-list__item">
                    <div class="media align-items-center">
                        <span class="media-left mr-16pt">
                            <img src="{{Storage::url($data->user->foto) }}"
                                 width="30"
                                 alt="avatar"
                                 class="rounded-circle">
                        </span>
                        <div class="media-body">
                            <a class="card-title m-0"
                               href="">Created by {{$data->user->firstName}} {{$data->user->lastName}}</a>
                            <p class="text-50 lh-1 mb-0">Admin</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>
        <div class="row card-group-row">
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $totalParticipant }}</div>
                            <div class="flex">
                                <div class="card-title">Total Participants</div>
                            </div>
                        </div>
                        <i class="material-icons icon-32pt text-20 ml-8pt">group</i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $totalAttend }}</div>
                            <div class="flex">
                                <div class="card-title">Attended</div>
                            </div>
                        </div>
                        <i class="material-icons icon-32pt text-20 ml-8pt">how_to_reg</i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $totalDidntAttend }}</div>
                            <div class="flex">
                                <div class="card-title">Didn't Attended</div>
                            </div>
                        </div>
                        <i class="material-icons icon-32pt text-20 ml-8pt">person_remove</i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row card-group-row mb-4">
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $totalIllness }}</div>
                            <div class="flex">
                                <div class="card-title">Illness</div>
                            </div>
                        </div>
                        <i class="material-icons icon-32pt text-20 ml-8pt">group_remove</i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $totalInjured }}</div>
                            <div class="flex">
                                <div class="card-title">Injured</div>
                            </div>
                        </div>
                        <i class="material-icons icon-32pt text-20 ml-8pt">group_remove</i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 card-group-row__col">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $totalOthers }}</div>
                            <div class="flex">
                                <div class="card-title">Others</div>
                            </div>
                        </div>
                        <i class="material-icons icon-32pt text-20 ml-8pt">group_remove</i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row card-group-row">
            <div class="col-12 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Player Attendance</div>
                </div>
                <div class="row">
                    @foreach($data->players as $player)
                        <div class="col-md-6">
                            <div class="card @if($player->pivot->attendanceStatus == 'Required Action') border-warning @elseif($player->pivot->attendanceStatus == 'Attended') border-success @else border-danger @endif" id="{{$player->id}}">
                                <div class="card-body d-flex align-items-center flex-row text-left">
                                    <img src="{{ Storage::url($player->user->foto) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="instructor">
                                    <div class="flex ml-3">
                                        <h5 class="mb-0">{{ $player->user->firstName  }} {{ $player->user->lastName  }}</h5>
                                        <p class="text-50 lh-1 mb-0">{{ $player->position->name }}</p>
                                    </div>
                                    <button class="btn @if($player->pivot->attendanceStatus == 'Required Action') btn-outline-warning text-warning @elseif($player->pivot->attendanceStatus == 'Attended') btn-outline-success text-success @else btn-outline-danger text-danger @endif playerAttendance" id="{{$player->id}}" type="button">
                                        <span class="material-icons mr-2">
                                            @if($player->pivot->attendanceStatus == 'Required Action') error
                                            @elseif($player->pivot->attendanceStatus == 'Attended') check_circle
                                            @else cancel
                                            @endif
                                        </span>
                                        {{ $player->pivot->attendanceStatus }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Coach Attendance</div>
{{--            <a href="{{ route('division-managements.create', $data->id) }}" class="btn btn-primary ml-auto btn-sm">--}}
{{--                <span class="material-icons mr-2">--}}
{{--                    add--}}
{{--                </span>--}}
{{--                Add New--}}
{{--            </a>--}}
        </div>
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
            $('body').on('click', '.playerAttendance', function(e) {
                e.preventDefault();
                $('#editPlayerAttendanceModal').modal('show');
            });


            // delete competition alert
            $('body').on('click', '.delete', function() {
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
                            success: function() {
                                Swal.fire({
                                    title: 'Competition successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('competition-managements.index') }}";
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
