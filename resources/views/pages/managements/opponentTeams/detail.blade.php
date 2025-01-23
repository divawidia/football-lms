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
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Action
                    <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('opponentTeam-managements.edit', $team->hash) }}"><span
                                class="material-icons">edit</span> Edit Team Profile</a>
                    @if($team->status == '1')
                        <button type="submit" class="dropdown-item setDeactivate" id="{{$team->id}}">
                            <span class="material-icons text-danger">check_circle</span>
                            Deactivate Team
                        </button>
                    @else
                        <button type="submit" class="dropdown-item setActivate" id="{{$team->id}}">
                            <span class="material-icons text-success">check_circle</span>
                            Activate Team
                        </button>
                    @endif
                    <button type="button" class="dropdown-item delete-team" id="{{$team->id}}">
                        <span class="material-icons">delete</span> Delete Team
                    </button>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-light border-bottom border-top py-3">
        <div class="container">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#overview-tab">Overview & Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#latestMatch-tab">Latest Match</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#competitions-tab">Competitions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#upcomingMatch-tab">Upcoming Matches</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#matchHistories-tab">Matches Histories</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container page-section">
        <div class="tab-content">

            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row card-group-row mb-4">
                    @include('components.cards.stats-card', ['title' => 'Match Played','data' => $overview['matchPlayed'], 'dataThisMonth' => $overview['matchPlayedThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Goals','data' => $overview['teamScore'], 'dataThisMonth' => $overview['teamScoreThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Goals Conceded','data' => $overview['goalsConceded'], 'dataThisMonth' => $overview['goalsConcededThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Goals Difference','data' => $overview['goalsDifference'], 'dataThisMonth' => $overview['goalDifferenceThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Clean Sheets','data' => $overview['cleanSheets'], 'dataThisMonth' => $overview['cleanSheetsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Own Goals','data' => $overview['teamOwnGoal'], 'dataThisMonth' => $overview['teamOwnGoalThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Wins','data' => $overview['Win'], 'dataThisMonth' => $overview['WinThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'losses','data' => $overview['Lose'], 'dataThisMonth' => $overview['LoseThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Draws','data' => $overview['Draw'], 'dataThisMonth' => $overview['DrawThisMonth']])
                </div>
                <div class="page-separator">
                    <div class="page-separator__text">Team Profile</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                            <div class="ml-auto p-2 text-muted">
                                @if ($team->status == '1')
                                    <span class="badge badge-pill badge-success">Active</span>
                                @elseif($team->status == '0')
                                    <span class="badge badge-pill badge-danger">Non-active</span>
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

            {{-- Latest Match --}}
            <div class="tab-pane fade" id="latestMatch-tab" role="tabpanel">
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

            {{-- Competitions/Events --}}
            <div class="tab-pane fade" id="competitions-tab" role="tabpanel">
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
            </div>

            {{-- Upcoming Matches --}}
            <div class="tab-pane fade" id="upcomingMatch-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Matches</div>
                </div>
                @if(count($upcomingMatches) == 0)
                    <x-warning-alert text="There are no matches scheduled at this time"/>
                @endif
                @foreach($upcomingMatches as $match)
                    <x-match-card :match="$match" :latestMatch="false"/>
                @endforeach
            </div>

            {{-- Match Histories --}}
            <div class="tab-pane fade" id="matchHistories-tab" role="tabpanel">
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
        </div>
    </div>

    @if(isAllAdmin())
        <x-process-data-confirmation btnClass=".setDeactivate"
                                     :processRoute="route('deactivate-team', ':id')"
                                     :routeAfterProcess="route('team-managements.show', $team->hash)"
                                     method="PATCH"
                                     confirmationText="Are you sure to deactivate this team {{ $team->teamName }}?"
                                     errorText="Something went wrong when deactivating this team {{ $team->teamName }}!"/>

        <x-process-data-confirmation btnClass=".setActivate"
                                     :processRoute="route('activate-team', ':id')"
                                     :routeAfterProcess="route('team-managements.show', $team->hash)"
                                     method="PATCH"
                                     confirmationText="Are you sure to activate this team {{ $team->teamName }}?"
                                     errorText="Something went wrong when activating this team {{ $team->teamName }}!"/>

        <x-process-data-confirmation btnClass=".delete-team"
                                     :processRoute="route('opponentTeam-managements.destroy', ':id')"
                                     :routeAfterProcess="route('team-managements.index')"
                                     method="DELETE"
                                     confirmationText="Are you sure to delete this team {{ $team->teamName }}?"
                                     errorText="Something went wrong when deleting this team {{ $team->teamName }}!"/>
    @endif
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#competitionsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('team-managements.teamCompetitions', $team->hash) !!}',
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
                    },
                ],
                order: [[3, 'desc']],
            });

            $('#matchHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('team-managements.match-histories', $team->hash) !!}',
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
                    },
                ],
                order: [[2, 'desc']],
            });
        });
    </script>
@endpush
