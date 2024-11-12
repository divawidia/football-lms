@extends('layouts.master')
@section('title')
    {{ $team->teamName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    @if(isAllAdmin() || isCoach())
                        <a href="{{ route('team-managements.index') }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Team Lists
                        </a>
                    @elseif(isPlayer())
                        <a href="{{ url()->previous() }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div
            class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($team->logo) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $team->teamName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $team->ageGroup }}</p>
            </div>

            @if(isAllAdmin())
                <div class="dropdown">
                    <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        Action
                        <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('team-managements.edit', $team->id) }}"><span
                                class="material-icons">edit</span> Edit Team Profile</a>
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
            @elseif(isCoach())
                <a class="btn btn-outline-white">
                    <span class="material-icons mr-3">
                        edit
                    </span>
                    Action
                </a>
            @endif
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>
        <div class="row card-group-row mb-4">
            @include('components.stats-card', ['title' => 'Match Played','data' => $overview['matchPlayed'], 'dataThisMonth' => $overview['matchPlayedThisMonth']])
            @include('components.stats-card', ['title' => 'Goals','data' => $overview['teamScore'], 'dataThisMonth' => $overview['teamScoreThisMonth']])
            @include('components.stats-card', ['title' => 'Goals Conceded','data' => $overview['goalsConceded'], 'dataThisMonth' => $overview['goalsConcededThisMonth']])
            @include('components.stats-card', ['title' => 'Goals difference','data' => $overview['goalsDifference'], 'dataThisMonth' => $overview['goalDifferenceThisMonth']])
            @include('components.stats-card', ['title' => 'clean sheets','data' => $overview['cleanSheets'], 'dataThisMonth' => $overview['cleanSheetsThisMonth']])
            @include('components.stats-card', ['title' => 'own goals','data' => $overview['teamOwnGoal'], 'dataThisMonth' => $overview['teamOwnGoalThisMonth']])
            @include('components.stats-card', ['title' => 'Wins','data' => $overview['Win'], 'dataThisMonth' => $overview['WinThisMonth']])
            @include('components.stats-card', ['title' => 'losses','data' => $overview['Lose'], 'dataThisMonth' => $overview['LoseThisMonth']])
            @include('components.stats-card', ['title' => 'draws','data' => $overview['Draw'], 'dataThisMonth' => $overview['DrawThisMonth']])
        </div>
        <div class="row card-group-row">
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Team Profile</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                            <div class="ml-auto p-2 text-muted">
                                @if ($team->status == '1')
                                    <span class="badge badge-pill badge-success">Aktif</span>
                                @elseif($team->status == '0')
                                    <span class="badge badge-pill badge-danger">Non Aktif</span>
                                @endif
                            </div>
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
                            <div
                                class="ml-auto p-2 text-muted">{{ date('M d, Y. h:i A', strtotime($team->created_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div
                                class="ml-auto p-2 text-muted">{{ date('M d, Y. h:i A', strtotime($team->updated_at)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Latest Match</div>
                </div>
                @if(count($latestMatches) == 0)
                    <x-warning-alert text="There are no latest matches record on this team"/>
                @endif
                @foreach($latestMatches as $match)
                    <x-match-card :match="$match" :latestMatch="true"/>
                @endforeach
            </div>
        </div>

        {{-- Players --}}
        <div class="page-separator">
            <div class="page-separator__text">Players</div>
            @if(isAllAdmin())
                <a href="{{ route('team-managements.addPlayerTeam', $team->id) }}"
                   class="btn btn-primary ml-auto btn-sm">
                <span class="material-icons mr-2">
                    add
                </span>
                    Add New
                </a>
            @endif
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="playersTable">
                        <thead>
                        <tr>
                            <th>Pos.</th>
                            <th>Name</th>
                            <th>Strong Foot</th>
                            <th>Age</th>
                            <th>Minutes Played</th>
                            <th>Apps</th>
                            <th>Goals</th>
                            <th>Assists</th>
                            <th>Own Goals</th>
                            <th>Shots</th>
                            <th>Passes</th>
                            <th>Fouls Conceded</th>
                            <th>Yellow Cards</th>
                            <th>Red Cards</th>
                            <th>Saves</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Coaches/Staffs --}}
        <div class="page-separator">
            <div class="page-separator__text">Coaches/Staffs</div>
            @if(isAllAdmin())
            <a href="{{ route('team-managements.addCoachesTeam', $team->id) }}" class="btn btn-primary ml-auto btn-sm">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
            @endif
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="coachesTable">
                        <thead>
                        <tr>
                            <th>#</th>
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

        {{-- Competitions/Events --}}
        <div class="page-separator">
            <div class="page-separator__text">Competitions/Events</div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="competitionsTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Group Division</th>
                            <th>Competition Date</th>
                            <th>Location</th>
                            <th>Contact</th>
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

        {{-- Upcoming Matches --}}
        <div class="page-separator">
            <div class="page-separator__text">Upcoming Matches</div>
        </div>
        @if(count($upcomingMatches) == 0)
            <x-warning-alert text="There are no matches scheduled at this time"/>
        @endif
        @foreach($upcomingMatches as $match)
            <x-match-card :match="$match" :latestMatch="false"/>
        @endforeach

        {{-- Upcoming Trainings --}}
        <div class="page-separator">
            <div class="page-separator__text">Upcoming Training</div>
        </div>
        @if(count($upcomingTrainings) == 0)
            <x-warning-alert text="There are no trainings scheduled at this time"/>
        @endif
        <div class="row">
            @foreach($upcomingTrainings as $training)
                <div class="col-lg-6">
                    <x-training-card :training="$training"/>
                </div>
            @endforeach
        </div>

        {{-- Match Histories --}}
        <div class="page-separator">
            <div class="page-separator__text">Match Histories</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="matchHistoryTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Opponent Team</th>
                            <th>competition</th>
                            <th>Match Date</th>
                            <th>Team Score</th>
                            <th>Opponent Team Score</th>
                            <th>Location</th>
                            <th>Note</th>
                            <th>Match Status</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Training Histories --}}
        <div class="page-separator">
            <div class="page-separator__text">Training Histories</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="trainingHistoryTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Training/Practice</th>
                            <th>training date</th>
                            <th>Location</th>
                            <th>Training Status</th>
                            <th>Note</th>
                            <th>Last Updated</th>
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
        $(document).ready(function () {
            $('#playersTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('team-managements.teamPlayers', $team->id) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'strongFoot', name: 'strongFoot'},
                    {data: 'age', name: 'age'},
                    {data: 'minutesPlayed', name: 'minutesPlayed'},
                    {data: 'apps', name: 'apps'},
                    {data: 'goals', name: 'goals'},
                    {data: 'assists', name: 'assists'},
                    {data: 'ownGoals', name: 'ownGoals'},
                    {data: 'shots', name: 'shots'},
                    {data: 'passes', name: 'passes'},
                    {data: 'fouls', name: 'fouls'},
                    {data: 'yellowCards', name: 'yellowCards'},
                    {data: 'redCards', name: 'redCards'},
                    {data: 'saves', name: 'saves'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[5, 'desc']]
            });
            $('#coachesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('team-managements.teamCoaches', $team->id) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'age', name: 'age'},
                    {data: 'gender', name: 'gender'},
                    {data: 'joinedDate', name: 'joinedDate'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            $('#competitionsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('team-managements.teamCompetitions', $team->id) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'divisions', name: 'divisions'},
                    {data: 'date', name: 'date'},
                    {data: 'location', name: 'location'},
                    {data: 'contact', name: 'contact'},
                    {data: 'status', name: 'status'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[3, 'desc']],
            });

            $('#trainingHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('team-managements.training-histories', $team->id) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'eventName', name: 'eventName'},
                    {data: 'date', name: 'date'},
                    {data: 'place', name: 'place'},
                    {data: 'status', name: 'status'},
                    {data: 'note', name: 'note'},
                    {data: 'last_updated', name: 'last_updated'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[2, 'desc']],
            });

            $('#matchHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('team-managements.match-histories', $team->id) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'opponentTeam', name: 'opponentTeam'},
                    {data: 'competition', name: 'competition'},
                    {data: 'date', name: 'date'},
                    {data: 'teamScore', name: 'teamScore'},
                    {data: 'opponentTeamScore', name: 'opponentTeamScore'},
                    {data: 'place', name: 'place'},
                    {data: 'status', name: 'status'},
                    {data: 'note', name: 'note'},
                    {data: 'last_updated', name: 'last_updated'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[2, 'desc']],
            });

            $('.delete-team').on('click', function () {
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
                            success: function (response) {
                                Swal.fire({
                                    title: 'Team successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText: 'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('team-managements.index') }}";
                                    }
                                });
                            },
                            error: function (error) {
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

            $('body').on('click', '.remove-player', function () {
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
                            success: function (response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Player successfully removed!",
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText: 'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload()
                                    }
                                });
                            },
                            error: function (error) {
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

            $('body').on('click', '.remove-coach', function () {
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
                            success: function (response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Coach successfully removed!",
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText: 'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload()
                                    }
                                });
                            },
                            error: function (error) {
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
