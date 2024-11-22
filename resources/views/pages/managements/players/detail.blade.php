@extends('layouts.master')
@section('title')
    {{ $data->user->firstName  }} {{ $data->user->lastName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-add-teams-to-player-coach-modal :route="route('player-managements.updateTeams', ['player' => $data->id])"
                                       :teams="$hasntJoinedTeams"/>
    <x-change-password-modal :route="route('player-managements.change-password', ['player' => ':id'])"/>
    <x-skill-assessments-modal :route="route('skill-assessments.store', $data->id)"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top">
        <div class="container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('player-managements.index') }}" class="nav-link text-70">
                        <i class="material-icons icon--left">keyboard_backspace</i>
                        Back to Player Lists
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div
            class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="player-photo">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $data->user->firstName  }} {{ $data->user->lastName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player - {{ $data->position->name }}</p>
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
                        <a class="dropdown-item" href="{{ route('player-managements.edit', $data->id) }}"><span
                                class="material-icons">edit</span> Edit Player</a>
                        @if($data->user->status == '1')
                            <button type="submit" class="dropdown-item setDeactivate" id="{{$data->id}}">
                                <span class="material-icons text-danger">check_circle</span>
                                Deactivate Player
                            </button>
                        @elseif($data->user->status == '0')
                            <button type="submit" class="dropdown-item setActivate" id="{{$data->id}}">
                                <span class="material-icons text-success">check_circle</span>
                                Activate Player
                            </button>
                        @endif
                        <a class="dropdown-item changePassword" id="{{ $data->id }}"><span class="material-icons">lock</span> Change Player Password</a>
                        <button type="button" class="dropdown-item delete-user" id="{{$data->id}}">
                            <span class="material-icons text-danger">delete</span> Delete Player
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
                    <a class="nav-link active" data-toggle="tab" href="#overview-tab">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#profile-tab">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#teams-tab">Teams</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#skillStats-tab">Skill Stats</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#parents-tab">Parents/Guardians</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#upcomingMatch-tab">Upcoming Matches</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#upcomingTraining-tab">Upcoming Training</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#trainingHistories-tab">Training Histories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#matchHistories-tab">Matches Histories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#performance-tab">Player Performance Review</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container page__container page-section">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">

                {{-- Overview Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row card-group-row">
                    @include('components.stats-card', ['title' => 'Match Played','data' => $overview['matchPlayed'], 'dataThisMonth' => $overview['thisMonthMatchPlayed']])
                    @include('components.stats-card', ['title' => 'Minutes Played','data' => $overview['statsData']['minutesPlayed'], 'dataThisMonth' => $overview['statsData']['minutesPlayedThisMonth']])
                    @include('components.stats-card', ['title' => 'Fouls','data' => $overview['statsData']['fouls'], 'dataThisMonth' => $overview['statsData']['foulsThisMonth']])
                    @if($data->position == 'Goalkeeper (GK)')
                        @include('components.stats-card', ['title' => 'Saves','data' => $overview['statsData']['saves'], 'dataThisMonth' => $overview['statsData']['savesThisMonth']])
                    @else
                        @include('components.stats-card', ['title' => 'Goals','data' => $overview['statsData']['goals'], 'dataThisMonth' => $overview['statsData']['goalsThisMonth']])
                    @endif
                    @include('components.stats-card', ['title' => 'Assists','data' => $overview['statsData']['assists'], 'dataThisMonth' => $overview['statsData']['assistsThisMonth']])
                    @include('components.stats-card', ['title' => 'Own Goals','data' => $overview['statsData']['ownGoal'], 'dataThisMonth' => $overview['statsData']['ownGoalThisMonth']])
                    @include('components.stats-card', ['title' => 'Wins','data' => $overview['statsData']['Win'], 'dataThisMonth' => $overview['statsData']['WinThisMonth']])
                    @include('components.stats-card', ['title' => 'Losses','data' => $overview['statsData']['Lose'], 'dataThisMonth' => $overview['statsData']['LoseThisMonth']])
                    @include('components.stats-card', ['title' => 'Draws','data' => $overview['statsData']['Draw'], 'dataThisMonth' => $overview['statsData']['DrawThisMonth']])
                </div>
            </div>

            {{-- Profile / Contact Histories --}}
            <div class="tab-pane fade" id="profile-tab" role="tabpanel">
                <div class="row card-group-row">
                    <div class="col-sm-6 flex-column">
                        {{--Profile Section--}}
                        <div class="page-separator">
                            <div class="page-separator__text">Profile</div>
                        </div>
                        <div class="card card-sm card-group-row__card">
                            <div class="card-body flex-column">
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                                    @if($data->user->status == '1')
                                        <span class="ml-auto p-2 badge badge-pill badge-success">Aktif</span>
                                    @elseif($data->user->status == '0')
                                        <span class="ml-auto p-2 badge badge-pill badge-danger">Non Aktif</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Player Skill :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->skill }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Strong Foot :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->strongFoot }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Height :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->height }} CM</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Weight :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->weight }} KG</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Date of Birth :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $overview['playerDob'] }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Age :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $overview['playerAge'] }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Gender :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->gender }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Join Date :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $overview['playerJoinDate'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 flex-column">
                        {{--Contact Section--}}
                        <div class="page-separator">
                            <div class="page-separator__text">Contact</div>
                        </div>
                        <div class="card card-sm card-group-row__card">
                            <div class="card-body flex-column">
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Email :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->email }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Phone Number :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->phoneNumber }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Address :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->address }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Country :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->country->name }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">State :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->state->name }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">City :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->city->name }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Zip Code :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->zipCode }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $overview['playerCreatedAt'] }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $overview['playerUpdatedAt'] }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Last Seen :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $overview['playerLastSeen'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Teams Histories --}}
            <div class="tab-pane fade" id="teams-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Teams</div>
                    @if(isAllAdmin())
                        <a href="" class="btn btn-sm btn-primary ml-auto" id="add-team">
                            <span class="material-icons mr-2">
                                add
                            </span>
                            Add Team
                        </a>
                    @endif
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="teamsTable">
                                <thead>
                                <tr>
                                    <th>Team Name</th>
                                    <th>Date Joined</th>
                                    @if(isAllAdmin())
                                        <th>Action</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Skill Stats Histories --}}
            <div class="tab-pane fade" id="skillStats-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats</div>
                    @if(isCoach())
                        <a class="btn btn-white addSkills ml-auto" id="{{ $data->id }}" href="">
                            <span class="material-icons mr-2">edit</span>
                            Update Skills
                        </a>
                    @endif
                </div>
                <div class="card card-body">
                    <x-player-skill-stats-radar-chart :labels="$playerSkillStats['label']" :datas="$playerSkillStats['data']" chartId="skillStatsChart"/>
                </div>
                {{--Skill Stats History Section--}}
                <x-player-skill-history-chart :player="$data"/>

                {{--All Skill Stats Section--}}
                <x-player-skill-stats-card :allSkills="$allSkills"/>
            </div>

            {{-- Parents/Guardians Histories --}}
            <div class="tab-pane fade" id="parents-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Parents/Guardians</div>
                    @if(isAllAdmin())
                        <a href="{{  route('player-parents.create', $data->id) }}" class="btn btn-sm btn-primary ml-auto"
                           id="add-new">
                <span class="material-icons mr-2">
                    add
                </span>
                            Add New
                        </a>
                    @endif
                </div>
                <x-player-parents-tables :player="$data"/>
            </div>

            {{-- Upcoming Matches --}}
            <div class="tab-pane fade" id="upcomingMatch-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Matches</div>
                    <a href="{{ route('player-managements.upcoming-matches', $data->id) }}"
                       class="btn btn-white border btn-sm ml-auto">
                        View More
                        <span class="material-icons ml-2 icon-16pt">chevron_right</span>
                    </a>
                </div>
                @if(count($overview['upcomingMatches']) == 0)
                    <x-warning-alert text="There are no matches scheduled at this time"/>
                @endif
                @foreach($overview['upcomingMatches'] as $match)
                    <x-match-card :match="$match"/>
                @endforeach
            </div>
            <div class="tab-pane fade" id="upcomingTraining-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Trainings</div>
                    <a href="{{ route('player-managements.upcoming-trainings', $data->id) }}"
                       class="btn btn-white border btn-sm ml-auto">
                        View More
                        <span class="material-icons ml-2 icon-16pt">chevron_right</span>
                    </a>
                </div>
                @if(count($overview['upcomingTrainings']) == 0)
                    <x-warning-alert text="There are no trainings scheduled at this time"/>
                @endif
                <div class="row">
                    @foreach($overview['upcomingTrainings'] as $training)
                        <div class="col-lg-6">
                            <x-training-card :training="$training"/>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Training Histories --}}
            <div class="tab-pane fade" id="trainingHistories-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Training Histories</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="trainingHistoryTable">
                                <thead>
                                <tr>
                                    <th>Training/Practice</th>
                                    <th>Team</th>
                                    <th>training date</th>
                                    <th>Location</th>
                                    <th>Training Status</th>
                                    <th>Attendance Status</th>
                                    <th>Note</th>
                                    <th>Last Updated Attendance</th>
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

            {{-- Match Histories --}}
            <div class="tab-pane fade" id="matchHistories-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Match Histories</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="matchHistoryTable">
                                <thead>
                                <tr>
                                    <th>Team</th>
                                    <th>Opponent</th>
                                    <th>Match Date</th>
                                    <th>Location</th>
                                    <th>Competition</th>
                                    <th>Match Type</th>
                                    <th>Match Status</th>
                                    <th>Attendance Status</th>
                                    <th>Note</th>
                                    <th>Last Updated Attendance</th>
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

            {{-- player performance review --}}
            <div class="tab-pane fade" id="performance-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">player performance review</div>
                </div>
                <x-player-performance-review-table
                    :route="route('player-managements.performance-reviews', ['player' => $data->id])"
                    tableId="performanceReviewTable"/>
            </div>
        </div>
    </div>

    @if(isAllAdmin())
        <x-process-data-confirmation btnClass=".setDeactivate"
                                     :processRoute="route('deactivate-player', ':id')"
                                     :routeAfterProcess="route('player-managements.show', $data->id)"
                                     method="PATCH"
                                     confirmationText="Are you sure to deactivate this player {{ getUserFullName($data->user) }}'s account?"
                                     errorText="Something went wrong when deactivating this player {{ getUserFullName($data->user) }}'s account!"/>

        <x-process-data-confirmation btnClass=".setActivate"
                                     :processRoute="route('activate-player', ':id')"
                                     :routeAfterProcess="route('player-managements.show', $data->id)"
                                     method="PATCH"
                                     confirmationText="Are you sure to activate this player {{ getUserFullName($data->user) }}'s account?"
                                     errorText="Something went wrong when activating this player {{ getUserFullName($data->user) }}'s account!"/>

        <x-process-data-confirmation btnClass=".delete-user"
                                     :processRoute="route('player-managements.destroy', ['player' => ':id'])"
                                     :routeAfterProcess="route('player-managements.index')"
                                     method="DELETE"
                                     confirmationText="Are you sure to delete this player {{ getUserFullName($data->user) }}'s account?"
                                     errorText="Something went wrong when deleting this player {{ getUserFullName($data->user) }}'s account!"/>

        <x-process-data-confirmation btnClass=".delete-team"
                                     :processRoute="route('player-managements.removeTeam', ['player' => $data->id, 'team' => ':id'])"
                                     :routeAfterProcess="route('player-managements.show', $data->id)"
                                     method="DELETE"
                                     confirmationText="Are you sure to to remove player {{ getUserFullName($data->user) }} from this team?"
                                     errorText="Something went wrong when removing player {{ getUserFullName($data->user) }} from this team!"/>
    @endif

@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            const body = $('body');

            $('#teamsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('player-managements.playerTeams', $data->id) !!}',
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'date', name: 'date'},
                        @if(isAllAdmin())
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                    @endif
                ],
            });

            $('#trainingHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('attendance-report.trainingTable', $data->id) !!}',
                },
                columns: [
                    {data: 'eventName', name: 'eventName'},
                    {data: 'team', name: 'team'},
                    {data: 'date', name: 'date'},
                    {data: 'place', name: 'place'},
                    {data: 'status', name: 'status'},
                    {data: 'attendanceStatus', name: 'attendanceStatus'},
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
                order: [[2, 'asc']],
            });

            $('#matchHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('attendance-report.matchDatatable', $data->id) !!}',
                },
                columns: [
                    {data: 'team', name: 'team'},
                    {data: 'opponentTeam', name: 'opponentTeam'},
                    {data: 'date', name: 'date'},
                    {data: 'place', name: 'place'},
                    {data: 'competition', name: 'competition'},
                    {data: 'matchType', name: 'matchType'},
                    {data: 'status', name: 'status'},
                    {data: 'attendanceStatus', name: 'attendanceStatus'},
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
                order: [[2, 'asc']],
            });
        });
    </script>
@endpush
