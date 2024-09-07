@extends('layouts.master')
@section('title')
    Competition {{ $competition->teamName  }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($competition->logo) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-32pt mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-32pt mb-md-0">
                <h2 class="text-white mb-0">{{ $competition->name  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $competition->type }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                    <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('competition-managements.edit', $competition->id) }}"><span class="material-icons">edit</span> Edit Competition Info</a>
                    @if($competition->status == '1')
                        <form action="{{ route('deactivate-competition', $competition->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">block</span> Deactivate Competition
                            </button>
                        </form>
                    @else
                        <form action="{{ route('activate-competition', $competition->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">check_circle</span> Activate Competition
                            </button>
                        </form>
                    @endif
                    <button type="button" class="dropdown-item delete-data" id="{{$competition->id}}">
                        <span class="material-icons">delete</span> Delete Competition
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
                    <div class="page-separator__text">Competition Info</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->status }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Start Date :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->startDate }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">End Date :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->endDate }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Location :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->location }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Contact Name :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->contactName }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Contact Phone :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->contactPhone }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Description :</p></div>
                            <div class="ml-auto p-2 text-muted">@php echo $competition->description @endphp</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($competition->created_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($competition->updated_at)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Match</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">

                    </div>
                </div>
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Group Divisions</div>
            <a href="" class="btn btn-primary ml-auto btn-sm">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
        </div>
        @foreach($competition->groups as $group)
            <div class="page-separator">
                <div class="page-separator__text">{{ $group->groupName }}</div>
                <a href="" class="btn btn-primary ml-auto btn-sm">
                <span class="material-icons mr-2">
                    add
                </span>
                    Add New Team
                </a>
            </div>
            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="groupTable{{$group->id}}">
                            <thead>
                            <tr>
                                <th>Team Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="page-separator">
            <div class="page-separator__text">Coaches/Staffs</div>
            <a href="" class="btn btn-primary ml-auto btn-sm">
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
            @foreach($competition->group as $group)
                const groupTable{{$group->id}} = $('#groupTable{{$group->id}}').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! route('team-managements.teamPlayers', $competition->id) !!}',
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

            @endforeach
            {{--const coachesTable = $('#coachesTable').DataTable({--}}
            {{--    processing: true,--}}
            {{--    serverSide: true,--}}
            {{--    ordering: true,--}}
            {{--    ajax: {--}}
            {{--        url: '{!! route('team-managements.teamCoaches', $competition->id) !!}',--}}
            {{--    },--}}
            {{--    columns: [--}}
            {{--        { data: 'name', name: 'name' },--}}
            {{--        { data: 'age', name: 'age' },--}}
            {{--        { data: 'gender', name: 'gender' },--}}
            {{--        { data: 'joinedDate', name: 'joinedDate'},--}}
            {{--        {--}}
            {{--            data: 'action',--}}
            {{--            name: 'action',--}}
            {{--            orderable: false,--}}
            {{--            searchable: false,--}}
            {{--            width: '15%'--}}
            {{--        },--}}
            {{--    ]--}}
            {{--});--}}

            {{--$('.delete-team').on('click', function() {--}}
            {{--    let id = $(this).attr('id');--}}

            {{--    Swal.fire({--}}
            {{--        title: "Are you sure?",--}}
            {{--        text: "You won't be able to revert this!",--}}
            {{--        icon: "warning",--}}
            {{--        showCancelButton: true,--}}
            {{--        confirmButtonColor: "#1ac2a1",--}}
            {{--        cancelButtonColor: "#E52534",--}}
            {{--        confirmButtonText: "Yes, delete it!"--}}
            {{--    }).then((result) => {--}}
            {{--        if (result.isConfirmed) {--}}
            {{--            $.ajax({--}}
            {{--                url: "{{ route('team-managements.destroy', ['team' => ':id']) }}".replace(':id', id),--}}
            {{--                type: 'DELETE',--}}
            {{--                data: {--}}
            {{--                    _token: "{{ csrf_token() }}"--}}
            {{--                },--}}
            {{--                success: function(response) {--}}
            {{--                    Swal.fire({--}}
            {{--                        icon: "success",--}}
            {{--                        title: "Team successfully deleted!",--}}
            {{--                    });--}}
            {{--                    playersTable.ajax.reload();--}}
            {{--                },--}}
            {{--                error: function(error) {--}}
            {{--                    Swal.fire({--}}
            {{--                        icon: "error",--}}
            {{--                        title: "Oops...",--}}
            {{--                        text: "Something went wrong when deleting data!",--}}
            {{--                    });--}}
            {{--                }--}}
            {{--            });--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}

            {{--$('body').on('click', '.remove-player', function() {--}}
            {{--    let id = $(this).attr('id');--}}

            {{--    Swal.fire({--}}
            {{--        title: "Are you sure?",--}}
            {{--        text: "You won't be able to revert this!",--}}
            {{--        icon: "warning",--}}
            {{--        showCancelButton: true,--}}
            {{--        confirmButtonColor: "#1ac2a1",--}}
            {{--        cancelButtonColor: "#E52534",--}}
            {{--        confirmButtonText: "Yes, remove it!"--}}
            {{--    }).then((result) => {--}}
            {{--        if (result.isConfirmed) {--}}
            {{--            $.ajax({--}}
            {{--                url: "{{ route('team-managements.removePlayer', ['team' => $competition->id, 'player' => ':id']) }}".replace(':id', id),--}}
            {{--                type: 'PUT',--}}
            {{--                data: {--}}
            {{--                    _token: "{{ csrf_token() }}"--}}
            {{--                },--}}
            {{--                success: function(response) {--}}
            {{--                    Swal.fire({--}}
            {{--                        icon: "success",--}}
            {{--                        title: "Player successfully removed!",--}}
            {{--                    });--}}
            {{--                    datatable.ajax.reload();--}}
            {{--                },--}}
            {{--                error: function(error) {--}}
            {{--                    Swal.fire({--}}
            {{--                        icon: "error",--}}
            {{--                        title: "Oops...",--}}
            {{--                        text: "Something went wrong when deleting data!",--}}
            {{--                    });--}}
            {{--                }--}}
            {{--            });--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}

            {{--$('body').on('click', '.remove-coach', function() {--}}
            {{--    let id = $(this).attr('id');--}}

            {{--    Swal.fire({--}}
            {{--        title: "Are you sure?",--}}
            {{--        text: "You won't be able to revert this!",--}}
            {{--        icon: "warning",--}}
            {{--        showCancelButton: true,--}}
            {{--        confirmButtonColor: "#1ac2a1",--}}
            {{--        cancelButtonColor: "#E52534",--}}
            {{--        confirmButtonText: "Yes, remove it!"--}}
            {{--    }).then((result) => {--}}
            {{--        if (result.isConfirmed) {--}}
            {{--            $.ajax({--}}
            {{--                url: "{{ route('team-managements.removeCoach', ['team' => $competition->id, 'coach' => ':id']) }}".replace(':id', id),--}}
            {{--                type: 'PUT',--}}
            {{--                data: {--}}
            {{--                    _token: "{{ csrf_token() }}"--}}
            {{--                },--}}
            {{--                success: function(response) {--}}
            {{--                    Swal.fire({--}}
            {{--                        icon: "success",--}}
            {{--                        title: "Coach successfully removed!",--}}
            {{--                    });--}}
            {{--                    coachesTable.ajax.reload();--}}
            {{--                },--}}
            {{--                error: function(error) {--}}
            {{--                    Swal.fire({--}}
            {{--                        icon: "error",--}}
            {{--                        title: "Oops...",--}}
            {{--                        text: "Something went wrong when deleting data!",--}}
            {{--                    });--}}
            {{--                }--}}
            {{--            });--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}
        });
    </script>
@endpush
