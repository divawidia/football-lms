@extends('layouts.master')
@section('title')
    {{ $fullName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-32pt mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-32pt mb-md-0">
                <h2 class="text-white mb-0">{{ $fullName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player - {{ $user->player->position->name }} - {{ $team }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                            <span class="material-icons ml-3">
                                keyboard_arrow_down
                            </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('player-managements.edit', $user->id) }}"><span class="material-icons">edit</span> Edit Player</a>
                    @if($user->status == '1')
                        <form action="{{ route('deactivate-player', $user->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">block</span> Deactivate Player
                            </button>
                        </form>
                    @else
                        <form action="{{ route('activate-player', $user->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">check_circle</span> Activate Player
                            </button>
                        </form>
                    @endif
                    <a class="dropdown-item" href="{{ route('player-managements.change-password-page', $user->id) }}"><span class="material-icons">lock</span> Change Player Password</a>
                    <button type="button" class="dropdown-item delete-user" id="{{$user->id}}">
                        <span class="material-icons">delete</span> Delete Player
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
                                <div class="card-title">Match Appearance</div>
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
                                <div class="card-title">Assists</div>
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
                                <div class="card-title">Wins</div>
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
                                <div class="card-title">Losses</div>
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
                                <div class="card-title">Minutes Played</div>
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
        <div class="row card-group-row">
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Teams</div>
                    <a href="#" class="btn btn-primary ml-auto" id="add-parent" data-toggle="modal" data-target="#exampleModal">
                <span class="material-icons mr-2">
                    add
                </span>
                        Add New
                    </a>
                </div>
                <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="teamsTable">
                                <thead>
                                <tr>
                                    <th>Team Name</th>
                                    <th>Date Joined</th>
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
                    <div class="page-separator__text">Contact</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Email :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->email }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Phone Number :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->phoneNumber }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Address :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->address }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Country :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->country->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">State :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->state->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">City :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->city->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Zip Code :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->zipCode }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Profile</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                            @if($user->status == '1')
                                <span class="ml-auto p-2 badge badge-pill badge-success">Aktif</span>
                            @elseif($user->status == '0')
                                <span class="ml-auto p-2 badge badge-pill badge-danger">Non Aktif</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Player Skill :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->player->skill }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Strong Foot :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->player->strongFoot }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Height :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->player->height }} CM</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Weight :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->player->weight }} KG</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Date of Birth :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($user->dob)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Age :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $age }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Gender :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->gender }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Join Date :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($user->player->joinDate)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($user->created_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($user->updated_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Seen :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($user->lastSeen)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Parents/Guardians</div>
            <a href="{{ route('player-parents.create', $user->id) }}" class="btn btn-primary ml-auto">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="parentsTable">
                        <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Relation</th>
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
            const parentsTable = $('#parentsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('player-parents.index', $user->id) !!}',
                },
                columns: [
                    { data: 'firstName', name: 'firstName' },
                    { data: 'lastName', name: 'lastName' },
                    { data: 'email', name: 'email'},
                    { data: 'phoneNumber', name: 'phoneNumber' },
                    { data: 'relations', name: 'relations' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            $('.delete-user').on('click', function() {
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
                            url: "{{ route('player-managements.destroy', ['player_management' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Player's account successfully deleted!",
                                });
                                window.location.href = "{{ route('player-managements.index') }}";
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

            $('body').on('click', '.delete-parent', function() {
                let idParent = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert after delete this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('player-parents.destroy', ['parent' => ':idParent']) }}".replace(':idParent', idParent),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Player's parent/guardian successfully deleted!",
                                });
                                parentsTable.ajax.reload();
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
