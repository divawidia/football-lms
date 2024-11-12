@extends('layouts.master')
@section('title')
    Team {{ $team->teamName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container">
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
        <div class="container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($team->logo) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-3 mb-md-0 ml-md-4">
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
                    <a class="dropdown-item" href="{{ route('opponentTeam-managements.edit', $team->id) }}"><span class="material-icons">edit</span> Edit Team Profile</a>
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

    <div class="container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>
        <div class="row card-group-row mb-4">
            @include('components.stats-card', ['title' => 'Match Played','data' => $overview['matchPlayed'], 'dataThisMonth' => $overview['matchPlayedThisMonth']])
            @include('components.stats-card', ['title' => 'Goals','data' => $overview['teamScore'], 'dataThisMonth' => $overview['teamScoreThisMonth']])
            @include('components.stats-card', ['title' => 'Goals Conceded','data' => $overview['goalsConceded'], 'dataThisMonth' => $overview['goalsConcededThisMonth']])
            @include('components.stats-card', ['title' => 'Goals Difference','data' => $overview['goalsDifference'], 'dataThisMonth' => $overview['goalDifferenceThisMonth']])
            @include('components.stats-card', ['title' => 'Clean Sheets','data' => $overview['cleanSheets'], 'dataThisMonth' => $overview['cleanSheetsThisMonth']])
            @include('components.stats-card', ['title' => 'Own Goals','data' => $overview['teamOwnGoal'], 'dataThisMonth' => $overview['teamOwnGoalThisMonth']])
            @include('components.stats-card', ['title' => 'Wins','data' => $overview['Win'], 'dataThisMonth' => $overview['WinThisMonth']])
            @include('components.stats-card', ['title' => 'losses','data' => $overview['Lose'], 'dataThisMonth' => $overview['LoseThisMonth']])
            @include('components.stats-card', ['title' => 'Draws','data' => $overview['Draw'], 'dataThisMonth' => $overview['DrawThisMonth']])
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
                            <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y. h:i A', strtotime($team->created_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y. h:i A', strtotime($team->updated_at)) }}</div>
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
    </div>
    <x-delete-data-confirmation deleteBtnClass=".delete-team"
                                :destroyRoute="route('opponentTeam-managements.destroy', ':id')"
                                :routeAfterDelete="route('team-managements.index')"
                                confirmationText="Are you sure to delete this team {{$team->teamName}}?"
                                successText="Successfully deleted team {{$team->teamName}}!"
                                errorText="Something went wrong when deleting team {{$team->teamName}}!"/>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
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
        });
    </script>
@endpush
