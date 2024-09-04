@extends('layouts.master')
@section('title')
    {{ $team->teamName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($team->logo) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-32pt mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-32pt mb-md-0">
                <h2 class="text-white mb-0">{{ $team->teamName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $team->ageGroup }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                            <span class="material-icons ml-3">
                                keyboard_arrow_down
                            </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('team-managements.edit', $team->id) }}"><span class="material-icons">edit</span> Edit Team Profile</a>
                    @if($team->status == '1')
                        <form action="{{ route('deactivate-team', $team->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">block</span> Deactivate Team
                            </button>
                        </form>
                    @else
                        <form action="{{ route('activate-team', $team->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">check_circle</span> Activate Team
                            </button>
                        </form>
                    @endif
                    <button type="button" class="dropdown-item delete-team" id="{{$team->id}}">
                        <span class="material-icons">delete</span> Delete Team
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>
        <div class="row card-group-row mb-4">
            <div class="col-lg-4 card-group-row__col flex-column">
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
            <div class="col-lg-4 card-group-row__col flex-column">
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
            <div class="col-lg-4 card-group-row__col flex-column">
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
        </div>
        <div class="row card-group-row mb-4">
            <div class="col-lg-4 card-group-row__col flex-column">
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
            <div class="col-lg-4 card-group-row__col flex-column">
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
            <div class="col-lg-4 card-group-row__col flex-column">
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
        </div>
        <div class="row card-group-row mb-4">
            <div class="col-lg-4 card-group-row__col flex-column">
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
            <div class="col-lg-4 card-group-row__col flex-column">
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
            <div class="col-lg-4 card-group-row__col flex-column">
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
        <div class="row card-group-row">
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Team Profile</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Age Group :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $team->ageGroup }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Total Players :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ count($team->players) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Total Staffs/Coaches :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ count($team->coaches) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($team->created_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($team->updated_at)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Latest Match</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">

                    </div>
                </div>
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Players</div>
            <a href="{{ route('team-managements.editPlayerTeam', $team->id) }}" class="btn btn-primary ml-auto btn-sm">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="playersTable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Strong Foot</th>
                            <th>Age</th>
                            <th>Appearances</th>
                            <th>Goals</th>
                            <th>Assists</th>
                            <th>Clean Sheets</th>
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
            <div class="page-separator__text">Coaches/Staffs</div>
            <a href="{{ route('team-managements.editCoachesTeam', $team->id) }}" class="btn btn-primary ml-auto btn-sm">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="coachesTable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Joined Date</th>
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
            <div class="page-separator__text">Competitions/Events</div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Upcoming Matches</div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Upcoming Training</div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Match History</div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Training History</div>
        </div>
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
            const playersTable = $('#playersTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('team-managements.teamPlayers', $team->id) !!}',
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'strongFoot', name: 'strongFoot' },
                    { data: 'age', name: 'age'},
                    { data: 'appearance', name: 'appearance' },
                    { data: 'goals', name: 'goals' },
                    { data: 'assists', name: 'assists' },
                    { data: 'cleanSheets', name: 'cleanSheets' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });
            const coachesTable = $('#coachesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('team-managements.teamCoaches', $team->id) !!}',
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'age', name: 'age' },
                    { data: 'gender', name: 'gender' },
                    { data: 'joinedDate', name: 'joinedDate'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            $('.delete-team').on('click', function() {
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
                            url: "{{ route('team-managements.destroy', ['team' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Team successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('team-managements.index') }}";
                                    }
                                });
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

            $('body').on('click', '.remove-player', function() {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, remove it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('team-managements.removePlayer', ['team' => $team->id, 'player' => ':id']) }}".replace(':id', id),
                            type: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Player successfully removed!",
                                });
                                playersTable.ajax.reload();
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

            $('body').on('click', '.remove-coach', function() {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, remove it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('team-managements.removeCoach', ['team' => $team->id, 'coach' => ':id']) }}".replace(':id', id),
                            type: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Coach successfully removed!",
                                });
                                coachesTable.ajax.reload();
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
        });
    </script>
@endpush
