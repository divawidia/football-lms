@extends('layouts.master')
@section('title')
    {{ $team->teamName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.add-players-to-team :route="route('team-managements.update-player', ['team' => $team->hash])"
                                 :players="$players"/>
    <x-modal.add-coaches-to-team :route="route('team-managements.update-coach', ['team' => $team->hash])"
                                 :coaches="$coaches"/>
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
                <x-buttons.dropdown title="Action" icon="keyboard_arrow_down" btnColor="outline-white" iconMargin="ml-3">
                    <x-buttons.link-button :dropdownItem="true" :href="route('team-managements.edit', $team->hash)"
                                           icon="edit" color="white" text="Edit Team Profile"/>

                    @if($team->status == '1')
                        <x-buttons.basic-button icon="check_circle" color="white" text="Deactivate Team"
                                                additionalClass="setDeactivate" :dropdownItem="true" :id="$team->id"
                                                iconColor="danger"/>
                    @else
                        <x-buttons.basic-button icon="check_circle" color="white" text="Activate Team"
                                                additionalClass="setActivate" :dropdownItem="true" :id="$team->id"
                                                iconColor="success"/>
                    @endif

                    <x-buttons.basic-button icon="delete" iconColor="danger" color="white" text="Delete Team"
                                            additionalClass="delete-team" :dropdownItem="true" :id="$team->id"/>
                </x-buttons.dropdown>
            @endif
        </div>
    </div>

    <x-tabs.navbar>
        <x-tabs.item title="Overview" link="overview" :active="true"/>
        <x-tabs.item title="Players" link="players"/>
        <x-tabs.item title="Coaches/Staffs" link="coaches"/>
        <x-tabs.item title="Latest Matches" link="latest-match"/>
        <x-tabs.item title="Upcoming Matches" link="upcoming-matches"/>
        <x-tabs.item title="Upcoming Trainings" link="upcoming-trainings"/>
        <x-tabs.item title="Matches Histories" link="matches-histories"/>
        <x-tabs.item title="Trainings Histories" link="trainings-histories"/>
    </x-tabs.navbar>

    <div class="container page__container page-section">
        <div class="tab-content">

            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row card-group-row">
                    @include('components.cards.stats-card', ['title' => 'Matches','data' => $matchPlayed, 'dataThisMonth' => $matchPlayedThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Wins','data' => $wins, 'dataThisMonth' => $winsThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Losses','data' => $losses, 'dataThisMonth' => $lossesThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Draws','data' => $draws, 'dataThisMonth' => $drawsThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Win Rate (%)','data' => $winRate, 'dataThisMonth' => $winRateThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Goals For','data' => $teamScore, 'dataThisMonth' => $teamScoreThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Goal Against','data' => $goalsConceded, 'dataThisMonth' => $goalsConcededThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Goal Differences','data' => $goalsDifference, 'dataThisMonth' => $goalsDifferenceThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Clean Sheets','data' => $cleanSheets, 'dataThisMonth' => $cleanSheetsThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Own Goal','data' => $teamOwnGoal, 'dataThisMonth' => $teamOwnGoalThisMonth])
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

            {{-- Latest Match --}}
            <div class="tab-pane fade" id="latest-matches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Latest Match</div>
                </div>
                @if(count($latestMatches) == 0)
                    <x-warning-alert text="There are no latest matches record on this team"/>
                @endif
                @foreach($latestMatches as $match)
                    <x-cards.match-card :match="$match" :latestMatch="true"/>
                @endforeach
            </div>

            {{-- Players --}}
            <div class="tab-pane fade" id="players-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Players</div>
                    @if(isAllAdmin())
                        <button type="button" class="btn btn-primary ml-auto btn-sm" id="add-players">
                            <span class="material-icons mr-2">add</span>Add players
                        </button>
                    @endif
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table
                            :headers="['Pos.', 'Name', 'Strong Foot', 'Age', 'Minutes Played', 'Apps', 'Goals', 'Assists', 'Own Goals', 'Shots', 'Passes', 'Fouls Conceded', 'Yellow Cards', 'Red Cards', 'Saves', 'Action']"
                            tableId="playersTable"
                        />
                    </div>
                </div>
            </div>

            {{-- Coaches/Staffs --}}
            <div class="tab-pane fade" id="coaches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Coaches/Staffs</div>
                    @if(isAllAdmin())
                        <button type="button" class="btn btn-primary ml-auto btn-sm" id="add-coaches">
                            <span class="material-icons mr-2">add</span>Add coaches
                        </button>
                    @endif
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table
                            :headers="['#', 'Name', 'Age', 'Gender', 'Joined Date', 'Action']"
                            tableId="coachesTable"
                        />
                    </div>
                </div>
            </div>

            {{-- Upcoming Matches --}}
            <div class="tab-pane fade" id="upcoming-matches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Matches</div>
                </div>
                @if(count($upcomingMatches) == 0)
                    <x-warning-alert text="There are no matches scheduled at this time"/>
                @endif
                @foreach($upcomingMatches as $match)
                    <x-cards.match-card :match="$match" :latestMatch="false"/>
                @endforeach
            </div>

            {{-- Upcoming Training --}}
            <div class="tab-pane fade" id="upcoming-trainings-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Training</div>
                </div>
                @if(count($upcomingTrainings) == 0)
                    <x-warning-alert text="There are no trainings scheduled at this time"/>
                @endif
                <div class="row">
                    @foreach($upcomingTrainings as $training)
                        <div class="col-lg-6">
                            <x-cards.training-card :training="$training"/>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Training Histories --}}
            <div class="tab-pane fade" id="trainings-histories-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Training Histories</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table
                            :headers="['#', 'Training/Practice', 'Training Date', 'Location', 'Training Status', 'Note', 'Last Updated', 'Action']"
                            tableId="trainingHistoryTable"
                        />
                    </div>
                </div>
            </div>

            {{-- Match Histories --}}
            <div class="tab-pane fade" id="matches-histories-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Match Histories</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table
                            :headers="['#', 'Home Team', 'Score','Away Team', 'Competition', 'Match Date', 'Location', 'Note', 'Match Status', 'Last Updated', 'Action']"
                            tableId="matchHistoryTable"
                        />
                    </div>
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
                    url: '{!! url()->route('team-managements.team-players', $team->hash) !!}',
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
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[5, 'desc']]
            });
            $('#coachesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('team-managements.team-coaches', $team->hash) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'age', name: 'age'},
                    {data: 'gender', name: 'gender'},
                    {data: 'joinedDate', name: 'joinedDate'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $('#trainingHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('team-managements.training-histories', $team->hash) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'eventName', name: 'eventName'},
                    {data: 'date', name: 'date'},
                    {data: 'place', name: 'place'},
                    {data: 'status', name: 'status'},
                    {data: 'note', name: 'note'},
                    {data: 'last_updated', name: 'last_updated'},
                    {data: 'action', name: 'action', orderable: false, searchable: false,},
                ],
                order: [[2, 'desc']],
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
                    {data: 'homeTeam', name: 'homeTeam'},
                    {data: 'teamScore', name: 'teamScore'},
                    {data: 'awayTeam', name: 'awayTeam'},
                    {data: 'competition', name: 'competition'},
                    {data: 'date', name: 'date'},
                    {data: 'place', name: 'place'},
                    {data: 'note', name: 'note'},
                    {data: 'status', name: 'status'},
                    {data: 'last_updated', name: 'last_updated'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                // order: [[2, 'desc']],
            });

            processWithConfirmation(
                ".setDeactivate",
                "{{ route('team-managements.deactivate', ':id') }}",
                "{{ route('team-managements.show', $team->hash) }}",
                "PATCH",
                "Are you sure to deactivate this team {{ $team->teamName }}?",
                "Something went wrong when deactivating this team {{ $team->teamName }}!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".setActivate",
                "{{ route('team-managements.activate', ':id') }}",
                "{{ route('team-managements.show', $team->hash) }}",
                "PATCH",
                "Are you sure to activate this team {{ $team->teamName }}?",
                "Something went wrong when activating this team {{ $team->teamName }}!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".delete-team",
                "{{ route('team-managements.destroy', ['team' => ':id']) }}",
                "{{ route('team-managements.index') }}",
                "DELETE",
                "Are you sure to delete this team {{ $team->teamName }}?",
                "Something went wrong when deleting this team {{ $team->teamName }}!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".remove-player",
                "{{ route('team-managements.remove-player', ['team' => $team->hash, 'player' => ':id']) }}",
                "{{ route('team-managements.show', $team->hash) }}",
                "PUT",
                "Are you sure to remove this player from team {{ $team->teamName }}?",
                "Something went wrong when removing this player from team {{ $team->teamName }}!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".remove-coach",
                "{{ route('team-managements.remove-coach', ['team' => $team->hash, 'coach' => ':id']) }}",
                "{{ route('team-managements.show', $team->hash) }}",
                "PUT",
                "Are you sure to remove this coach from team {{ $team->teamName }}?",
                "Something went wrong when removing this coach from team {{ $team->teamName }}!",
                "{{ csrf_token() }}"
            );

        });
    </script>
@endpush
