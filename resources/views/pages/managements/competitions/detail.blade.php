@extends('layouts.master')
@section('title', $competition->name)

@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.matches.add-match :competition="$competition"/>
    <x-modal.matches.edit-match :competition="$competition"/>
    <x-modal.competitions.add-team-into-standing :competition="$competition"/>
    <x-modal.competitions.edit-team-standing :competition="$competition"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    @if(isAllAdmin() || isCoach())
                        <a href="{{ route('competition-managements.index') }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Competition Lists
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
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($competition->logo) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex ml-md-4 mb-3 mb-md-0">
                <h2 class="text-white mb-0">{{ $competition->name  }}</h2>
                <p class="lead text-white-50">{{ $competition->type }} ~
                    @if($competition->isInternal == 1)
                        Internal
                    @else
                        External
                    @endif
                    Competition
                </p>
            </div>
            @if(isAllAdmin())
                <x-buttons.dropdown title="Action" icon="keyboard_arrow_down" btnColor="outline-white" iconMargin="ml-3">
                    <x-buttons.link-button :dropdownItem="true" :href="route('competition-managements.edit', $competition->hash)"
                                           icon="edit" color="white" text="Edit competition info"/>
                    @if($competition->status != 'Cancelled' && $competition->status != 'Completed')
                        <x-buttons.basic-button icon="block" color="white" text="Cancel Competition"
                                                additionalClass="cancelBtn" :dropdownItem="true" :id="$competition->hash"
                                                iconColor="danger"/>
                    @elseif($competition->status == 'Cancelled')
                        <x-buttons.basic-button icon="check_circle" color="white" text="set Competition to scheduled"
                                                additionalClass="scheduled-btn" :dropdownItem="true" :id="$competition->hash"
                                                iconColor="warning"/>
                    @endif
                    <x-buttons.basic-button icon="delete" iconColor="danger" color="white" text="Delete competition"
                                            additionalClass="delete-competition" :dropdownItem="true" :id="$competition->hash"/>
                </x-buttons.dropdown>
            @endif
        </div>
    </div>

    <x-tabs.navbar>
        <x-tabs.item title="Competition Info" link="overview" :active="true"/>
        <x-tabs.item title="Matches" link="matches"/>
        @if($competition->type == 'league')
            <x-tabs.item title="Standing" link="standing"/>
        @endif
    </x-tabs.navbar>

    <div class="container page__container page-section">
        <div class="tab-content">

            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Competition Info</div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                                    <div class="ml-auto p-2 text-muted">
                                        @if ($competition->status == 'Scheduled')
                                            <span class="badge badge-pill badge-warning">{{ $competition->status }}</span>
                                        @elseif($competition->status == 'Ongoing')
                                            <span class="badge badge-pill badge-info">{{ $competition->status }}</span>
                                        @elseif($competition->status == 'Completed')
                                            <span class="badge badge-pill badge-success">{{ $competition->status }}</span>
                                        @else
                                            <span class="badge badge-pill badge-danger">{{ $competition->status }}</span>
                                        @endif
                                    </div>
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
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Created By :</p></div>
                                    <div class="ml-auto p-2 text-muted">
                                        @if($competition->userId)
                                            {{ getUserFullName($competition->user) }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($competition->created_at) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($competition->updated_at) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Matches --}}
            <div class="tab-pane fade" id="matches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Match</div>
                    @if(isAllAdmin())
                        <x-buttons.link-button size="sm" margin="ml-auto" href="#" icon="add" text="Add Match"
                                               id="add-match-btn"/>
                    @endif
                </div>
                <div class="card">
                    <div class="card-body">
                        @if($competition->isInternal == 1)
                            <x-table
                                    :headers="['#','Home Team', 'Score','Away Team',  'Match Date','Venue', 'Status', 'Action']"
                                    tableId="competitionMatchTable"/>
                        @else
                            <x-table
                                    :headers="['#','Team', 'Opposing Team', 'Score', 'Match Date','Venue', 'Status', 'Action']"
                                    tableId="competitionMatchTable"/>
                        @endif
                    </div>
                </div>
            </div>

            {{-- League Standing --}}
            @if($competition->type == 'league')
                <div class="tab-pane fade" id="standing-tab" role="tabpanel">
                    <div class="page-separator">
                        <div class="page-separator__text">League Standing</div>
                        @if(isAllAdmin())
                            <x-buttons.link-button size="sm" margin="ml-auto" href="#" icon="add" text="Add Team" id="add-team-btn"/>
                        @endif
                    </div>
                    <div class="card">
                        <div class="card-body">
                            @if ($competition->status == 'Scheduled' or $competition->status == 'Ongoing')
                                <x-table
                                        :headers="['Pos.', 'Team', 'Match Played', 'Won', 'Draw','Lost', 'Goals For', 'Goals Against', 'Goals Difference', 'Points',  'Action']"
                                        tableId="leagueStandingTable"/>
                            @else
                                <x-table
                                        :headers="['Pos.', 'Team', 'Match Played', 'Won', 'Draw','Lost', 'Goals For', 'Goals Against', 'Goals Difference', 'Points']"
                                        tableId="leagueStandingTable"/>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            processWithConfirmation(
                '.delete-competition',
                "{{ route('competition-managements.destroy', ['competition' => $competition->hash]) }}",
                "{{ route('competition-managements.index') }}",
                'DELETE',
                "Are you sure to delete competition {{ $competition->name }}?",
                "Something went wrong when deleting the competition {{ $competition->name }}!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.cancelBtn',
                "{{ route('competition-managements.cancelled', ['competition' => $competition->hash]) }}",
                "{{ route('competition-managements.show', $competition->hash) }}",
                'PATCH',
                "Are you sure to cancel competition {{ $competition->name }}?",
                "Something went wrong when marking the competition {{ $competition->name }} as cancelled!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.scheduled-btn',
                "{{ route('competition-managements.scheduled', ['competition' => $competition->hash]) }}",
                "{{ route('competition-managements.show', $competition->hash) }}",
                'PATCH',
                "Are you sure to set competition {{ $competition->name }} to scheduled?",
                "Something went wrong when setting the competition {{ $competition->name }} as scheduled!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.delete-match',
                "{{ route('match-schedules.destroy', ['match' => ':id']) }}",
                "{{ route('competition-managements.show', $competition->hash) }}",
                'DELETE',
                "Are you sure to delete this match from competition {{ $competition->name }}?",
                "Something went wrong when deleting this match from competition {{ $competition->name }}!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.delete-team',
                "{{ route('competition-managements.league-standings.destroy', ['competition' => $competition->hash, 'leagueStanding'=>':id']) }}",
                "{{ route('competition-managements.show', $competition->hash) }}",
                'DELETE',
                "Are you sure to delete this team standing from competition {{ $competition->name }}?",
                "Something went wrong when deleting this team standing from competition {{ $competition->name }}!",
                "{{ csrf_token() }}"
            );

            $('#competitionMatchTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('competition-managements.matches', $competition->hash) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'homeTeam', name: 'homeTeam'},
                    {data: 'score', name: 'score'},
                    {data: 'awayTeam', name: 'awayTeam'},
                    {data: 'date', name: 'date'},
                    {data: 'place', name: 'place'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false,},
                ],
            });

            $('#leagueStandingTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('competition-managements.league-standings.index', $competition->hash) !!}',
                },
                columns: [
                    {data: 'standingPositions', name: 'standingPositions'},
                    {data: 'team', name: 'team'},
                    {data: 'matchPlayed', name: 'matchPlayed'},
                    {data: 'won', name: 'won'},
                    {data: 'drawn', name: 'drawn'},
                    {data: 'lost', name: 'lost'},
                    {data: 'goalsFor', name: 'goalsFor'},
                    {data: 'goalsAgainst', name: 'goalsAgainst'},
                    {data: 'goalsDifference', name: 'goalsDifference'},
                    {data: 'points', name: 'points'},
                    @if($competition->status == 'Scheduled' or $competition->status == 'Ongoing')
                    {data: 'action', name: 'action', orderable: false, searchable: false,},
                    @endif
                ],
            });
        });
    </script>
@endpush
