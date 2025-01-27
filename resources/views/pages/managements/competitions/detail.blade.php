@extends('layouts.master')
@section('title')
    {{ $competition->name  }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <!-- Modal edit group modal -->
{{--    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-dialog-centered">--}}
{{--            <div class="modal-content">--}}
{{--                <form action="#" method="post" id="formEditGroupModal">--}}
{{--                    @method('PUT')--}}
{{--                    @csrf--}}
{{--                    <div class="modal-header">--}}
{{--                        <h5 class="modal-title" id="exampleModalLabel">Edit Group Division</h5>--}}
{{--                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">--}}
{{--                            <span aria-hidden="true">&times;</span>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                    <div class="modal-body">--}}
{{--                        <input type="hidden" id="groupId">--}}
{{--                        <div class="form-group ">--}}
{{--                            <label class="form-label" for="add_groupName">Group Division Name</label>--}}
{{--                            <small class="text-danger">*</small>--}}
{{--                            <input type="text"--}}
{{--                                   id="add_groupName"--}}
{{--                                   name="groupName"--}}
{{--                                   value="{{ old('groupName') }}"--}}
{{--                                   class="form-control"--}}
{{--                                   placeholder="Input group's name ...">--}}
{{--                            <span class="invalid-feedback groupName_error" role="alert">--}}
{{--                                <strong></strong>--}}
{{--                            </span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="modal-footer">--}}
{{--                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>--}}
{{--                        <button type="submit" class="btn btn-primary">Submit</button>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <x-add-our-team-match-in-competition-modal :competition="$competition"/>--}}
{{--    <x-add-opponent-team-match-in-competition-modal :competition="$competition"/>--}}
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
                <p class="lead text-white-50">{{ $competition->type }} ~ @if($competition->isInternal == 1)Internal @else External @endif Competition</p>
            </div>
            @if(isAllAdmin())
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                    <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('competition-managements.edit', $competition->hash) }}"><span class="material-icons">edit</span> Edit Competition Info</a>
                    @if($competition->status != 'Cancelled' && $competition->status != 'Completed')
                        <button type="submit" class="dropdown-item cancelBtn" id="{{ $competition->id }}">
                            <span class="material-icons text-danger">block</span>
                            Cancel Competition
                        </button>
                    @endif
                    <button type="button" class="dropdown-item delete-competition" id="{{$competition->id}}">
                        <span class="material-icons text-danger">delete</span> Delete Competition
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    <nav class="navbar navbar-light border-bottom border-top py-3">
        <div class="container">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#overview-tab">Overview & Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#matches-tab">Matches</a>
                </li>
                @if($competition->type == 'league')
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#standing-tab">Standing</a>
                    </li>
                @endif

{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link" data-toggle="tab" href="#tables-tab">Group Tables</a>--}}
{{--                </li>--}}
            </ul>
        </div>
    </nav>

    <div class="container page__container page-section">
        <div class="tab-content">

            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row">
                    @include('components.stats-card', ['title' => 'Total Teams', 'data'=>$overviewStats['totalTeams'], 'dataThisMonth'=>null])
                    @include('components.stats-card', ['title' => 'Total Match', 'data'=>$overviewStats['totalMatch'], 'dataThisMonth'=>null])
                    @include('components.stats-card', ['title' => "Total Wins", 'data'=>$overviewStats['ourTeamsWins'], 'dataThisMonth'=>null])
                    @include('components.stats-card', ['title' => "Total Draws", 'data'=>$overviewStats['ourTeamsDraws'], 'dataThisMonth'=>null])
                    @include('components.stats-card', ['title' => "Total Losses", 'data'=>$overviewStats['ourTeamsLosses'], 'dataThisMonth'=>null])
                </div>

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
{{--                                <div class="d-flex align-items-center">--}}
{{--                                    <div class="p-2"><p class="card-title mb-4pt">Description :</p></div>--}}
{{--                                    <div class="ml-auto p-2 text-muted">@php echo $competition->description @endphp</div>--}}
{{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
{{--                                <div class="d-flex align-items-center">--}}
{{--                                    <div class="p-2"><p class="card-title mb-4pt">Contact Name :</p></div>--}}
{{--                                    <div class="ml-auto p-2 text-muted">{{ $competition->contactName }}</div>--}}
{{--                                </div>--}}
{{--                                <div class="d-flex align-items-center">--}}
{{--                                    <div class="p-2"><p class="card-title mb-4pt">Contact Phone :</p></div>--}}
{{--                                    <div class="ml-auto p-2 text-muted">{{ $competition->contactPhone }}</div>--}}
{{--                                </div>--}}
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Created By :</p></div>
                                    <div class="ml-auto p-2 text-muted">@if($competition->userId){{ getUserFullName($competition->user) }}@else N/A @endif</div>
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
                </div>
            </div>

            {{-- Matches --}}
            <div class="tab-pane fade" id="matches-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Match</div>
                    @if(isAllAdmin())
                        <x-buttons.link-button size="sm" margin="ml-auto" href="#" icon="add" text="Add Match" id="add-match-btn"/>
                    @endif
                </div>
                <div class="card">
                    <div class="card-body">
                        @if($competition->isInternal == 1)
                            <x-table :headers="['#','Home Team', 'Away Team', 'Score', 'Match Date','Venue', 'Status', 'Action']" tableId="competitionMatchTable"/>
                        @else
                            <x-table :headers="['#','Team', 'Opposing Team', 'Score', 'Match Date','Venue', 'Status', 'Action']" tableId="competitionMatchTable"/>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Standing --}}
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
                                <x-table :headers="['Pos.', 'Team', 'Match Played', 'Won', 'Draw','Lost', 'Goals For', 'Goals Against', 'Goals Difference', 'Points',  'Action']" tableId="leagueStandingTable"/>
                            @else
                                <x-table :headers="['Pos.', 'Team', 'Match Played', 'Won', 'Draw','Lost', 'Goals For', 'Goals Against', 'Goals Difference', 'Points']" tableId="leagueStandingTable"/>
                            @endif
                        </div>
                    </div>
                </div>
            @endif


            {{-- Group Divisions --}}
{{--            <div class="tab-pane fade" id="groups-tab" role="tabpanel">--}}
{{--                <div class="page-separator">--}}
{{--                    <div class="page-separator__text">Group Divisions</div>--}}
{{--                    @if(isAllAdmin())--}}
{{--                        <a href="{{ route('division-managements.create', $competition->hash) }}" class="btn btn-primary ml-auto btn-sm">--}}
{{--                            <span class="material-icons mr-2">add</span>Add New--}}
{{--                        </a>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    @foreach($competition->groups as $group)--}}
{{--                        <div class="@if(count($competition->groups) <= 1) col-12 @else col-lg-6 @endif">--}}
{{--                            <div class="page-separator">--}}
{{--                                <div class="page-separator__text">{{ $group->groupName }}</div>--}}
{{--                                @if(isAllAdmin())--}}
{{--                                    <div class="btn-toolbar ml-auto" role="toolbar" aria-label="Toolbar with button groups">--}}
{{--                                        <a class="btn btn-sm btn-white edit-group" id="{{ $group->id }}" href="#" data-toggle="tooltip" data-placement="bottom" title="Edit Group">--}}
{{--                                            <span class="material-icons">edit</span>--}}
{{--                                        </a>--}}
{{--                                        <a href="{{ route('division-managements.addTeam', ['competition' => $competition->hash, 'group' => $group->id]) }}" class="btn btn-sm btn-white ml-1" data-toggle="tooltip" data-placement="bottom" title="Add Team">--}}
{{--                                            <span class="material-icons">add</span>--}}
{{--                                        </a>--}}
{{--                                        <button type="button" class="btn btn-sm btn-white ml-1 delete-group" id="{{ $group->id }}" data-toggle="tooltip" data-placement="bottom" title="Delete Group">--}}
{{--                                            <span class="material-icons">delete</span>--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <div class="card">--}}
{{--                                <div class="card-body">--}}
{{--                                    <div class="table-responsive">--}}
{{--                                        <table class="table table-hover w-100" id="groupTable{{$group->id}}">--}}
{{--                                            <thead>--}}
{{--                                            <tr>--}}
{{--                                                <th>Team Name</th>--}}
{{--                                                <th>Action</th>--}}
{{--                                            </tr>--}}
{{--                                            </thead>--}}
{{--                                            <tbody>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- Group Tables --}}
{{--            <div class="tab-pane fade" id="tables-tab" role="tabpanel">--}}
{{--                <div class="page-separator">--}}
{{--                    <div class="page-separator__text">Group Tables</div>--}}
{{--                </div>--}}
{{--                @foreach($competition->groups as $group)--}}
{{--                    <div class="page-separator">--}}
{{--                        <div class="page-separator__text">{{ $group->groupName }}</div>--}}
{{--                    </div>--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="table-responsive">--}}
{{--                                <table class="table table-hover w-100" id="classTable{{$group->id}}">--}}
{{--                                    <thead>--}}
{{--                                    <tr>--}}
{{--                                        <th>Team</th>--}}
{{--                                        <th>Match Played</th>--}}
{{--                                        <th>won</th>--}}
{{--                                        <th>drawn</th>--}}
{{--                                        <th>lost</th>--}}
{{--                                        <th>goals For</th>--}}
{{--                                        <th>goals Againts</th>--}}
{{--                                        <th>goals Difference</th>--}}
{{--                                        <th>red Cards</th>--}}
{{--                                        <th>yellow Cards</th>--}}
{{--                                        <th>points</th>--}}
{{--                                    </tr>--}}
{{--                                    </thead>--}}
{{--                                    <tbody>--}}
{{--                                    </tbody>--}}
{{--                                </table>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <x-process-data-confirmation btnClass=".delete-group{{ $group->id }}-team"--}}
{{--                                                 :processRoute="route('division-managements.removeTeam', ['competition' => $competition->hash, 'group' => $group->id,'team' => ':id'])"--}}
{{--                                                 :routeAfterProcess="route('competition-managements.show', $competition->hash)"--}}
{{--                                                 method="PUT"--}}
{{--                                                 confirmationText="Are you sure to delete this team from group division {{ $group->groupName }}?"--}}
{{--                                                 errorText="Something went wrong when deleting this team from the group division {{ $group->groupName }}!"/>--}}
{{--                @endforeach--}}
{{--            </div>--}}
        </div>
    </div>

{{--    <x-process-data-confirmation btnClass=".delete-group"--}}
{{--                                 :processRoute="route('division-managements.destroy', ['competition' => $competition->hash, 'group' => ':id'])"--}}
{{--                                 :routeAfterProcess="route('competition-managements.show', $competition->hash)"--}}
{{--                                 method="DELETE"--}}
{{--                                 confirmationText="Are you sure to delete this group division?"--}}
{{--                                 errorText="Something went wrong when deleting the group division!"/>--}}

@endsection
@push('addon-script')
    <script type="module">
        import { processWithConfirmation } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;

        $(document).ready(function() {

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
                "{{ route('cancelled-competition', ['competition' => $competition->hash]) }}",
                "{{ route('competition-managements.show', $competition->hash) }}",
                'PATCH',
                "Are you sure to cancel competition {{ $competition->name }}?",
                "Something went wrong when marking the competition {{ $competition->name }} as cancelled!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.delete-match',
                "{{ route('match-schedules.destroy', ['schedule' => ':id']) }}",
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
                    { data: 'homeTeam', name: 'homeTeam' },
                    { data: 'awayTeam', name: 'awayTeam' },
                    { data: 'score', name: 'score'},
                    { data: 'date', name: 'date'},
                    { data: 'place', name: 'place'},
                    { data: 'status', name: 'status'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    },
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
                    { data: 'standingPositions', name: 'standingPositions' },
                    { data: 'team', name: 'team' },
                    { data: 'matchPlayed', name: 'matchPlayed'},
                    { data: 'won', name: 'won'},
                    { data: 'drawn', name: 'drawn'},
                    { data: 'lost', name: 'lost'},
                    { data: 'goalsFor', name: 'goalsFor' },
                    { data: 'goalsAgainst', name: 'goalsAgainst' },
                    { data: 'goalsDifference', name: 'goalsDifference'},
                    { data: 'points', name: 'points'},
                    @if($competition->status == 'Scheduled' or $competition->status == 'Ongoing')
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    },
                    @endif
                ],
            });

{{--            @foreach($competition->groups as $group)--}}
{{--                $('#groupTable{{$group->id}}').DataTable({--}}
{{--                    processing: true,--}}
{{--                    serverSide: true,--}}
{{--                    ordering: true,--}}
{{--                    ajax: {--}}
{{--                        url: '{!! route('division-managements.index', ['competition'=>$competition->hash,'group'=>$group->id]) !!}',--}}
{{--                    },--}}
{{--                    columns: [--}}
{{--                        { data: 'teams', name: 'teams' },--}}
{{--                        {--}}
{{--                            data: 'action',--}}
{{--                            name: 'action',--}}
{{--                            orderable: false,--}}
{{--                            searchable: false,--}}
{{--                        },--}}
{{--                    ]--}}
{{--                });--}}

{{--            $('#classTable{{$group->id}}').DataTable({--}}
{{--                processing: true,--}}
{{--                serverSide: true,--}}
{{--                ordering: true,--}}
{{--                ajax: {--}}
{{--                    url: '{!! route('division-managements.index', ['competition'=>$competition->hash,'group'=>$group->id]) !!}',--}}
{{--                },--}}
{{--                columns: [--}}
{{--                    { data: 'teams', name: 'teams' },--}}
{{--                    { data: 'pivot.matchPlayed', name: 'pivot.matchPlayed' },--}}
{{--                    { data: 'pivot.won', name: 'pivot.won' },--}}
{{--                    { data: 'pivot.drawn', name: 'pivot.drawn' },--}}
{{--                    { data: 'pivot.lost', name: 'pivot.lost' },--}}
{{--                    { data: 'pivot.goalsFor', name: 'pivot.goalsFor' },--}}
{{--                    { data: 'pivot.goalsAgaints', name: 'pivot.goalsAgaints' },--}}
{{--                    { data: 'pivot.goalsDifference', name: 'pivot.goalsDifference' },--}}
{{--                    { data: 'pivot.redCards', name: 'pivot.redCards' },--}}
{{--                    { data: 'pivot.yellowCards', name: 'pivot.yellowCards' },--}}
{{--                    { data: 'pivot.points', name: 'pivot.points' },--}}
{{--                ]--}}
{{--            });--}}
{{--            @endforeach--}}

{{--            // show modal edit group data--}}
{{--            $('body').on('click', '.edit-group', function(e) {--}}
{{--                const id = $(this).attr('id');--}}
{{--                e.preventDefault();--}}

{{--                $.ajax({--}}
{{--                    method: 'GET',--}}
{{--                    url: "{{ route('division-managements.edit', ['competition' => $competition->hash, 'group' => ':id']) }}".replace(':id', id),--}}
{{--                    success: function(res) {--}}
{{--                        $("#editGroupModal").modal('show');--}}
{{--                        $('#groupId').val(id);--}}
{{--                        $('#add_groupName').val(res.groupName);--}}
{{--                    },--}}
{{--                    error: function(xhr) {--}}
{{--                        const response = JSON.parse(xhr.responseText);--}}
{{--                        Swal.fire({--}}
{{--                            icon: 'error',--}}
{{--                            html: response,--}}
{{--                            allowOutsideClick: true,--}}
{{--                        });--}}
{{--                    }--}}
{{--                });--}}
{{--            });--}}

{{--            // insert data opponent team--}}
{{--            $('#formEditGroupModal').on('submit', function(e) {--}}
{{--                e.preventDefault();--}}
{{--                let id = $('#groupId').val();--}}

{{--                $.ajax({--}}
{{--                    method: $(this).attr('method'),--}}
{{--                    url: "{{ route('division-managements.update', ['competition' => $competition->hash, 'group' => ':id']) }}".replace(':id', id),--}}
{{--                    data: new FormData(this),--}}
{{--                    contentType: false,--}}
{{--                    processData: false,--}}
{{--                    success: function(res) {--}}
{{--                        $('#editGroupModal').modal('hide');--}}
{{--                        Swal.fire({--}}
{{--                            title: 'Group Division successfully added!',--}}
{{--                            icon: 'success',--}}
{{--                            showCancelButton: false,--}}
{{--                            confirmButtonColor: "#1ac2a1",--}}
{{--                            confirmButtonText:--}}
{{--                                'Ok!'--}}
{{--                        }).then((result) => {--}}
{{--                            if (result.isConfirmed) {--}}
{{--                                location.reload();--}}
{{--                            }--}}
{{--                        });--}}
{{--                    },--}}
{{--                    error: function(xhr) {--}}
{{--                        const response = JSON.parse(xhr.responseText);--}}
{{--                        $.each(response.errors, function(key, val) {--}}
{{--                            $('span.' + key + '_error').text(val[0]);--}}
{{--                            $("input#add_" + key).addClass('is-invalid');--}}
{{--                        });--}}
{{--                    }--}}
{{--                });--}}
{{--            });--}}
        });
    </script>
@endpush
