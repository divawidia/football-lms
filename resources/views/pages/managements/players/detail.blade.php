@extends('layouts.master')
@section('title')
    {{ $data->user->firstName  }} {{ $data->user->lastName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-change-password-modal :route="route('player-managements.change-password', ['player' => ':id'])"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="@if(isAllAdmin())
                    {{ route('player-managements.index') }}
                    @elseif(isCoach())
                    {{ route('coach.player-managements.index') }}
                    @endif" class="nav-link text-70">
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
                    <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                        <span class="material-icons ml-3">
                            keyboard_arrow_down
                        </span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('player-managements.edit', $data->id) }}"><span class="material-icons">edit</span> Edit Player</a>
                        @if($data->user->status == '1')
                            <form action="{{ route('deactivate-player', $data->id) }}" method="POST">
                                @method("PATCH")
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <span class="material-icons text-danger">block</span> Deactivate Player
                                </button>
                            </form>
                        @else
                            <form action="{{ route('activate-player', $data->id) }}" method="POST">
                                @method("PATCH")
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <span class="material-icons text-success">check_circle</span> Activate Player
                                </button>
                            </form>
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

    <div class="container page__container page-section">
        {{--        Overview Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>
        <div class="row card-group-row">
            @include('components.stats-card', ['title' => 'Match Played','data' => $overview['matchPlayed'], 'dataThisMonth' => $overview['thisMonthMatchPlayed']])
            @include('components.stats-card', ['title' => 'Minutes Played','data' => $overview['minutesPlayed'], 'dataThisMonth' => $overview['thisMonthMinutesPlayed']])
            @include('components.stats-card', ['title' => 'Fouls','data' => $overview['fouls'], 'dataThisMonth' => $overview['thisMonthFouls']])
            @if($data->position == 'Goalkeeper (GK)')
                @include('components.stats-card', ['title' => 'Saves','data' => $overview['saves'], 'dataThisMonth' => $overview['thisMonthSaves']])
            @else
                @include('components.stats-card', ['title' => 'Goals','data' => $overview['goals'], 'dataThisMonth' => $overview['thisMonthGoals']])
            @endif
            @include('components.stats-card', ['title' => 'Assists','data' => $overview['assists'], 'dataThisMonth' => $overview['thisMonthAssists']])
            @include('components.stats-card', ['title' => 'Own Goals','data' => $overview['ownGoals'], 'dataThisMonth' => $overview['thisMonthOwnGoals']])
            @include('components.stats-card', ['title' => 'Wins','data' => $overview['wins'], 'dataThisMonth' => $overview['thisMonthWins']])
            @include('components.stats-card', ['title' => 'Losses','data' => $overview['losses'], 'dataThisMonth' => $overview['thisMonthLosses']])
            @include('components.stats-card', ['title' => 'Draws','data' => $overview['draws'], 'dataThisMonth' => $overview['thisMonthDraws']])
        </div>

        <div class="row card-group-row">
            <div class="col-sm-6 card-group-row__col flex-column">
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
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col flex-column">
                {{--Teams Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Teams</div>
                </div>
                <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="teamsTable">
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

                {{--Teams Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats</div>
                    <a href="@if(isAllAdmin())
                    {{ route('player-managements.skill-stats', $data->id) }}
                    @elseif(isCoach())
                    {{ route('coach.player-managements.skill-stats', $data->id) }}
                    @endif" class="btn btn-white border btn-sm ml-auto">
                        View More
                        <span class="material-icons ml-2 icon-16pt">chevron_right</span>
                    </a>
                </div>
                <div class="card">
                    <x-player-skill-stats-radar-chart :labels="$playerSkillStats['label']"
                                                      :datas="$playerSkillStats['data']" chartId="skillStatsChart"/>
                </div>

            </div>
        </div>

        {{--Parents/Guardians Section--}}
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
        <x-player-parents-tables :player="$data->id"/>

        {{--Upcoming Matches Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Upcoming Matches</div>
            <a href="@if(isAllAdmin())
                    {{ route('player-managements.upcoming-matches', $data->id) }}
                    @elseif(isCoach())
                    {{ route('coach.player-managements.upcoming-matches', $data->id) }}
                    @endif" class="btn btn-white border btn-sm ml-auto">
                View More
                <span class="material-icons ml-2 icon-16pt">chevron_right</span>
            </a>
        </div>
        @if(count($overview['upcomingMatches']) == 0)
            @include('components.alerts.warning', ['text' => 'There are no matches scheduled at this time'])
        @endif
        @foreach($overview['upcomingMatches'] as $match)
            <a class="card" href="@if(isAllAdmin())
                    {{ route('match-schedules.show', $match->id) }}
                    @elseif(isCoach())
                    {{ route('coach.match-schedules.show', $match->id) }}
                    @endif">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                            <img src="{{ Storage::url($match->teams[0]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="team-logo">
                            <div class="ml-md-3 text-center text-md-left">
                                <h5 class="mb-0">{{$match->teams[0]->teamName}}</h5>
                                <p class="text-50 lh-1 mb-0">{{$match->teams[0]->ageGroup}}</p>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h2 class="mb-0">Vs.</h2>
                        </div>
                        <div
                            class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                            <div class="mr-md-3 text-center text-md-right">
                                <h5 class="mb-0">{{ $match->teams[1]->teamName }}</h5>
                                <p class="text-50 lh-1 mb-0">{{$match->teams[1]->ageGroup}}</p>
                            </div>
                            <img src="{{ Storage::url($match->teams[1]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="team-logo">
                        </div>
                    </div>

                    <div class="row justify-content-center mt-3">
                        <div class="mr-2">
                            <i class="material-icons text-danger icon--left icon-16pt">event</i>
                            {{ date('D, M d Y', strtotime($match->date)) }}
                        </div>
                        <div class="mr-2">
                            <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                            {{ date('h:i A', strtotime($match->startTime)) }}
                            - {{ date('h:i A', strtotime($match->endTime)) }}
                        </div>
                        <div>
                            <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                            {{ $match->place }}
                        </div>
                    </div>
                </div>
            </a>
        @endforeach

        {{--Upcoming Trainings Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Upcoming Trainings</div>
            <a href="@if(isAllAdmin())
                    {{ route('player-managements.upcoming-trainings', $data->id) }}
                    @elseif(isCoach())
                    {{ route('coach.player-managements.upcoming-trainings', $data->id) }}
                    @endif" class="btn btn-white border btn-sm ml-auto">
                View More
                <span class="material-icons ml-2 icon-16pt">chevron_right</span>
            </a>
        </div>
        @if(count($overview['upcomingTrainings']) == 0)
            @include('components.alerts.warning', ['text' => 'There are no trainings scheduled at this time'])
        @endif
        <div class="row">
            @foreach($overview['upcomingTrainings'] as $training)
                <div class="col-lg-6">
                    <a class="card" href="@if(isAllAdmin())
                    {{ route('training-schedules.show', $training->id) }}
                    @elseif(isCoach())
                    {{ route('coach.training-schedules.show', $training->id) }}
                    @endif">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 d-flex flex-column flex-md-row align-items-center">
                                    <img src="{{ Storage::url($training->teams[0]->logo) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="team-logo">
                                    <div class="ml-md-3 text-center text-md-left">
                                        <h5 class="mb-0">{{$training->teams[0]->teamName}}</h5>
                                        <p class="text-50 lh-1 mb-0">{{$training->teams[0]->ageGroup}}</p>
                                    </div>
                                </div>
                                <div class="col-6 d-flex flex-column">
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">event</i>
                                        {{ date('D, M d Y', strtotime($training->date)) }}
                                    </div>
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                                        {{ date('h:i A', strtotime($training->startTime)) }}
                                        - {{ date('h:i A', strtotime($training->endTime)) }}
                                    </div>
                                    <div>
                                        <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                        {{ $training->place }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        {{--Training Histories Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Training Histories</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="trainingHistoryTable">
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

        {{--Match Histories Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Match Histories</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="matchHistoryTable">
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

        {{--performance review Section--}}
        <div class="page-separator">
            <div class="page-separator__text">performance review</div>
        </div>
        @if(count($performanceReviews)==0)
            @include('components.alerts.warning', ['text' => "You haven't added any note performance review to this player yet"])
        @endif
        @foreach($performanceReviews as $review)
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div class="flex">
                        <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($review->created_at)) }}</h4>
                        <div class="card-subtitle text-50">Last updated
                            at {{ date('D, M d Y h:i A', strtotime($review->updated_at)) }}</div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="material-icons">
                            more_vert
                        </span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit-note" id="{{ $review->id }}" href="">
                                <span class="material-icons">edit</span>
                                Edit Note
                            </a>
                            <button type="button" class="dropdown-item delete-note" id="{{ $review->id }}">
                                <span class="material-icons">delete</span>
                                Delete Note
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        echo $review->performanceReview
                    @endphp
                </div>
            </div>
        @endforeach
    </div>

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
                    @if(isAllAdmin())
                    url: '{!! route('player-managements.playerTeams', $data->id) !!}',
                    @elseif(isCoach())
                    url: '{!! route('coach.player-managements.playerTeams', $data->id) !!}',
                    @endif
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
                    @if(isAllAdmin())
                    url: '{!! route('attendance-report.trainingTable', $data->id) !!}',
                    @elseif(isCoach())
                    url: '{!! route('coach.attendance-report.trainingTable', $data->id) !!}',
                    @endif
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
                order: [[2, 'desc']],
            });

            $('#matchHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    @if(isAllAdmin())
                    url: '{!! route('attendance-report.matchDatatable', $data->id) !!}',
                    @elseif(isCoach())
                    url: '{!! route('coach.attendance-report.matchDatatable', $data->id) !!}',
                    @endif
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
                order: [[2, 'desc']],
            });

            $('.delete-user').on('click', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure to delete this player?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('player-managements.destroy', ['player' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: "Player's account successfully deleted!",
                                    icon: 'success',
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('player-managements.index') }}";
                                    }
                                });
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: errorThrown
                                });
                            }
                        });
                    }
                });
            });

            body.on('click', '.delete-team', function () {
                const idTeam = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert after delete this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, remove team!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('player-managements.removeTeam', ['player' => $data->id, 'team' => ':idTeam']) }}".replace(':idTeam', idTeam),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                Swal.fire({
                                    title: "Team successfully removed from player!",
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
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

            $('#add-team').on('click', function (e) {
                e.preventDefault();
                $('#addTeamModal').modal('show');
            });

            $('#formAddTeam').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('player-managements.updateTeams', ['player' => $data->id]) }}",
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#addTeamModal').modal('hide');
                        Swal.fire({
                            title: "Team successfully added to player!",
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        const response = JSON.parse(xhr.responseText);
                        $.each(response.errors, function (key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("select#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });
        });
    </script>
@endpush
