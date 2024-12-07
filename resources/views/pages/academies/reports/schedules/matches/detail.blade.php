@extends('layouts.master')
@section('title')
    Match {{ $schedule->teams[0]->teamName }} Vs {{ $schedule->teams[1]->teamName }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-edit-player-attendance-modal
            :routeGet="route('match-schedules.player', ['schedule' => $schedule->id, 'player' => ':id'])"
            :routeUpdate="route('match-schedules.update-player', ['schedule' => $schedule->id, 'player' => ':id'])"/>

    <x-edit-coach-attendance-modal
            :routeGet="route('match-schedules.coach', ['schedule' => $schedule->id, 'coach' => ':id'])"
            :routeUpdate="route('match-schedules.update-coach', ['schedule' => $schedule->id, 'coach' => ':id'])"/>

    <x-create-schedule-note-modal :routeCreate="route('match-schedules.create-note', $schedule->id)"
                                  :eventName="$schedule->eventType"/>
    <x-edit-schedule-note-modal
            :routeEdit="route('match-schedules.edit-note', ['schedule' => $schedule->id, 'note' => ':id'])"
            :routeUpdate="route('match-schedules.update-note', ['schedule' => $schedule->id, 'note' => ':id'])"
            :eventName="$schedule->eventType"
            :routeAfterProcess="route('match-schedules.show', $schedule->id)"/>

    <x-skill-assessments-modal/>
    <x-edit-skill-assessments-modal/>

    <x-add-performance-review-modal :routeCreate="route('coach.performance-reviews.store', ['player'=> ':id'])"/>
    <x-edit-performance-review-modal :routeAfterProcess="route('match-schedules.show', $schedule->id)"/>

    <!-- Modal add team scorer -->
    <x-add-team-scorer-modal :eventSchedule="$schedule"/>

    <!-- Modal add team own goal scorer -->
    <x-add-team-own-goal-modal :eventSchedule="$schedule"/>

    <!-- Modal add team match stats -->
    <x-edit-team-match-stats-modal :eventSchedule="$schedule"/>

    <!-- Modal update player match stats -->
    <x-edit-player-match-stats-modal :eventSchedule="$schedule"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ url()->previous() }}" class="nav-link text-70"><i class="material-icons icon--left">keyboard_backspace</i>Back</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <div class="flex mb-3 mb-md-0">
                <h2 class="text-white mb-0">Match {{ $schedule->teams[0]->teamName }}
                    Vs {{ $schedule->teams[1]->teamName }}</h2>
                <p class="lead text-white-50">{{ $schedule->eventType }}
                    ~ {{ $schedule->matchType }} @if($schedule->competition)
                        ~ {{$schedule->competition->name}}
                    @endif</p>
            </div>
            @if(isAllAdmin())
                <div class="dropdown">
                    <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Action<span class="material-icons ml-3">keyboard_arrow_down</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('match-schedules.edit', $schedule->id) }}">
                            <span class="material-icons">edit</span> Edit Match Schedule
                        </a>
                        @if($schedule->status != 'Cancelled' && $schedule->status != 'Completed')
                            <button type="submit" class="dropdown-item cancelBtn" id="{{ $schedule->id }}">
                                <span class="material-icons text-danger">block</span>
                                Cancel Match
                            </button>
                        @endif
                        <button type="button" class="dropdown-item delete" id="{{$schedule->id}}">
                            <span class="material-icons text-danger">delete</span> Delete Match
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">
                    Status :
                    @if ($schedule->status == 'Scheduled')
                        <span class="badge badge-pill badge-warning">{{ $schedule->status }}</span>
                    @elseif($schedule->status == 'Ongoing')
                        <span class="badge badge-pill badge-info">{{ $schedule->status }}</span>
                    @elseif($schedule->status == 'Completed')
                        <span class="badge badge-pill badge-success">{{ $schedule->status }}</span>
                    @else
                        <span class="badge badge-pill badge-danger">{{ $schedule->status }}</span>
                    @endif
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">event</i>
                    {{ date('D, M d Y', strtotime($schedule->date)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                    {{ date('h:i A', strtotime($schedule->startTime)) }}
                    - {{ date('h:i A', strtotime($schedule->endTime)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                    {{ $schedule->place }}
                </li>
                <li class="nav-item navbar-list__item">
                    <div class="media align-items-center">
                        <span class="media-left mr-16pt">
                            <img src="{{Storage::url($schedule->user->foto) }}"
                                 width="30"
                                 alt="avatar"
                                 class="rounded-circle">
                        </span>
                        <div class="media-body">
                            <a class="card-title m-0"
                               href="">Created
                                by {{$schedule->user->firstName}} {{$schedule->user->lastName}}</a>
                            <p class="text-50 lh-1 mb-0">Admin</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <nav class="navbar navbar-light border-bottom border-top py-3">
        <div class="container">
            <ul class="nav nav-pills text-capitalize">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#overview-tab">Match Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#matchStats-tab">Match Stats</a>
                </li>
                @if($schedule->isOpponentTeamMatch == 0)
                    @if ($schedule->matchType == 'Internal Match')
                        @if(in_array($schedule->teams[0]->id, $userTeams))
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[0]->id }}PlayerStats-tab">{{ $schedule->teams[0]->teamName }}
                                    Player Stats</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[0]->id }}Attendance-tab">{{ $schedule->teams[0]->teamName }}
                                    Match Attendance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[0]->id }}Notes-tab">{{ $schedule->teams[0]->teamName }}
                                    Match Session Notes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[0]->id }}Skills-tab">{{ $schedule->teams[0]->teamName }}
                                    skills evaluation</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[0]->id }}Performance-tab">{{ $schedule->teams[0]->teamName }}
                                    performance review</a>
                            </li>
                        @endif
                        @if(in_array($schedule->teams[1]->id, $userTeams))
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[1]->id }}PlayerStats-tab">{{ $schedule->teams[1]->teamName }}
                                    Player Stats</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[1]->id }}Attendance-tab">{{ $schedule->teams[1]->teamName }}
                                    Match Attendance</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[1]->id }}Notes-tab">{{ $schedule->teams[1]->teamName }}
                                    Match Session Notes</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[1]->id }}Skills-tab">{{ $schedule->teams[1]->teamName }}
                                    skills evaluation</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#team{{ $schedule->teams[1]->id }}Performance-tab">{{ $schedule->teams[1]->teamName }}
                                    performance review</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#playerStats-tab">Player Stats</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#attendance-tab">Match Attendance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#notes-tab">Match Session Notes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#skills-tab">skills evaluation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#performance-tab">performance review</a>
                        </li>
                    @endif

                @endif
            </ul>
        </div>
    </nav>

    <div class="container page__container page-section">
        <div class="tab-content">

            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                {{--    Team Match Score    --}}
                <div class="card px-lg-5">
                    <div class="card-body">
                        <div class="row d-flex">
                            <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                                <img src="{{ Storage::url($schedule->teams[0]->logo) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="instructor">
                                <div class="ml-md-3 text-center text-md-left">
                                    <h5 class="mb-0">{{ $schedule->teams[0]->teamName }}</h5>
                                    <p class="text-50 lh-1 mb-0">{{ $schedule->teams[0]->ageGroup }}</p>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <h2 class="mb-0">{{ $schedule->teams[0]->pivot->teamScore }}
                                    - {{ $schedule->teams[1]->pivot->teamScore }}</h2>
                            </div>
                            <div class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                                <div class="mr-md-3 text-center text-md-right">
                                    <h5 class="mb-0">{{ $schedule->teams[1]->teamName }}</h5>
                                    <p class="text-50 lh-1 mb-0">{{ $schedule->teams[1]->ageGroup }}</p>
                                </div>
                                <img src="{{ Storage::url($schedule->teams[1]->logo) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="instructor">
                            </div>
                        </div>
                    </div>
                </div>

                {{--    Match Scorer    --}}
                @if($schedule->isOpponentTeamMatch == 0)

                    <div class="page-separator">
                        <div class="page-separator__text">{{ $schedule->teams[0]->teamName }} Scorer(s)</div>

                        @if(isAllAdmin())
                            <button type="button" data-team="homeTeam"
                                    class="btn btn-primary btn-sm ml-auto addTeamScorer">
                                <span class="material-icons mr-2">add</span>
                                Add team scorer
                            </button>
                            <button type="button" data-team="homeTeam" class="btn btn-primary btn-sm ml-2 addOwnGoal">
                                <span class="material-icons mr-2">add</span>
                                Add own goal
                            </button>
                        @endif
                    </div>

                    @if(count($homeTeamMatchScorers)==0)
                        <x-warning-alert text="You haven't added any team scorer yet"/>
                    @else
                        <div class="row">
                            @foreach($homeTeamMatchScorers as $matchScore)
                                <div class="col-md-6">
                                    <div class="card" id="{{$matchScore->id}}">
                                        <div class="card-body d-flex align-items-center flex-row text-left">
                                            <img src="{{ Storage::url($matchScore->player->user->foto) }}"
                                                 width="50"
                                                 height="50"
                                                 class="rounded-circle img-object-fit-cover"
                                                 alt="instructor">
                                            <div class="flex ml-3">
                                                <h5 class="mb-0 d-flex">{{ $matchScore->player->user->firstName  }} {{ $matchScore->player->user->lastName  }}
                                                    <p class="text-50 ml-2 mb-0">({{ $matchScore->minuteScored }}')</p>
                                                </h5>
                                                <p class="text-50 lh-1 mb-0">{{ $matchScore->player->position->name }}</p>
                                                @if($matchScore->isOwnGoal == 1)
                                                    <p class="text-50 lh-1 mb-0"><strong>Own Goal</strong></p>
                                                @elseif($matchScore->assistPlayer)
                                                    <p class="text-50 lh-1 mb-0">Assist
                                                        : {{ $matchScore->assistPlayer->user->firstName }} {{ $matchScore->assistPlayer->user->lastName }}</p>
                                                @endif
                                            </div>
                                            @if(isAllAdmin() || isCoach())
                                                <button
                                                        class="btn btn-sm btn-outline-secondary @if($matchScore->isOwnGoal == 1) delete-own-goal @else delete-scorer @endif"
                                                        type="button" id="{{ $matchScore->id }}" data-toggle="tooltip"
                                                        data-placement="bottom" title="Delete scorer">
                                                    <span class="material-icons">
                                                        close
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($schedule->matchType == 'Internal Match')
                        <div class="page-separator">
                            <div class="page-separator__text">{{ $schedule->teams[1]->teamName }} Scorer(s)</div>
                            @if(isAllAdmin())
                                <button type="button" data-team="awayTeam"
                                        class="btn btn-primary btn-sm ml-auto addTeamScorer"><span
                                            class="material-icons mr-2">add</span> Add team scorer
                                </button>
                                <button type="button" data-team="awayTeam"
                                        class="btn btn-primary btn-sm ml-2 addOwnGoal">
                                    <span class="material-icons mr-2">add</span>
                                    Add own goal
                                </button>
                            @endif
                        </div>
                        @if(count($awayTeamMatchScorers)==0)
                            <x-warning-alert text="You haven't added any team scorer yet"/>
                        @else
                            <div class="row">
                                @foreach($awayTeamMatchScorers as $matchScore)
                                    <div class="col-md-6">
                                        <div class="card" id="{{$matchScore->id}}">
                                            <div class="card-body d-flex align-items-center flex-row text-left">
                                                <img src="{{ Storage::url($matchScore->player->user->foto) }}"
                                                     width="50"
                                                     height="50"
                                                     class="rounded-circle img-object-fit-cover"
                                                     alt="instructor">
                                                <div class="flex ml-3">
                                                    <h5 class="mb-0 d-flex">{{ $matchScore->player->user->firstName  }} {{ $matchScore->player->user->lastName  }}
                                                        <p class="text-50 ml-2 mb-0">({{ $matchScore->minuteScored }}
                                                            ')</p></h5>
                                                    <p class="text-50 lh-1 mb-0">{{ $matchScore->player->position->name }}</p>
                                                    @if($matchScore->isOwnGoal == 1)
                                                        <p class="text-50 lh-1 mb-0"><strong>Own Goal</strong></p>
                                                    @elseif($matchScore->assistPlayer)
                                                        <p class="text-50 lh-1 mb-0">
                                                            Assist: {{ $matchScore->assistPlayer->user->firstName }} {{ $matchScore->assistPlayer->user->lastName }}</p>
                                                    @endif
                                                </div>
                                                @if(isAllAdmin() || isCoach())
                                                    <button
                                                            class="btn btn-sm btn-outline-secondary @if($matchScore->isOwnGoal == 1) delete-own-goal @else delete-scorer @endif"
                                                            type="button" id="{{ $matchScore->id }}"
                                                            data-toggle="tooltip"
                                                            data-placement="bottom" title="Delete scorer">
                                                    <span class="material-icons">
                                                        close
                                                    </span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                @endif
            </div>

            {{--    Match Stats    --}}
            <div class="tab-pane fade" id="matchStats-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Match Stats</div>
                    @if(isAllAdmin())
                        <a href="" id="updateMatchStats" class="btn btn-primary btn-sm ml-auto"><span
                                    class="material-icons mr-2">add</span>
                            Update match stats</a>
                    @endif
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <img src="{{ Storage::url($schedule->teams[0]->logo) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="instructor">
                                <div class="flex ml-3">
                                    <h5 class="mb-0">{{ $schedule->teams[0]->teamName }}</h5>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <div class="mr-3">
                                    <h5 class="mb-0">{{ $schedule->teams[1]->teamName }}</h5>
                                </div>
                                <img src="{{ Storage::url($schedule->teams[1]->logo) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="instructor">
                            </div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamPossesion }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Possession %</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamPossesion }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamShotOnTarget }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Shots on target</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamShotOnTarget }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamShots }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Shots</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamShots }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamTouches }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Touches</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamTouches }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamPasses }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Passes</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamPasses }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamTackles }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Tackles</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamTackles }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamClearances }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Clearances</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamClearances }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamCorners }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Corners</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamCorners }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamOffsides }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Offsides</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamOffsides }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamYellowCards }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Yellow cards</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamYellowCards }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[0]->pivot->teamRedCards }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Red cards</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">{{ $schedule->teams[1]->pivot->teamRedCards }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong
                                            class="flex">{{ $schedule->teams[0]->pivot->teamFoulsConceded }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Fouls conceded</strong>
                                </div>
                                <div class="col-4">
                                    <strong
                                            class="flex">{{ $schedule->teams[1]->pivot->teamFoulsConceded }}</strong>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            @if($schedule->isOpponentTeamMatch == 0)

                @if ($schedule->matchType == 'Internal Match')

                    @if(in_array($schedule->teams[0]->id, $userTeams))
                        {{--    Player Stats    --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[0]->id }}PlayerStats-tab"
                             role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">Player Stats</div>
                            </div>
                            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0"
                                               id="team{{ $schedule->teams[0]->id }}PlayerStatsTable">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Minutes Played</th>
                                                <th>Goals</th>
                                                <th>Assists</th>
                                                <th>Own Goals</th>
                                                <th>Shots</th>
                                                <th>Passes</th>
                                                <th>Fouls</th>
                                                <th>Yellow Cards</th>
                                                <th>Red Cards</th>
                                                <th>Saves</th>
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
                        {{--     Attendance    --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[0]->id }}Attendance-tab" role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">{{ $schedule->teams[0]->teamName }} Attendance
                                    Overview
                                </div>
                            </div>
                            <div class="row card-group-row">
                                @include('components.stats-card', ['title' => 'Total Participants', 'data'=>$homeTeamAttendance['totalParticipant'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => 'Attended', 'data'=>$homeTeamAttendance['totalAttend'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => "Didn't Attended", 'data'=>$homeTeamAttendance['totalDidntAttend'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => "Illness", 'data'=>$homeTeamAttendance['totalIllness'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => "Injured", 'data'=>$homeTeamAttendance['totalInjured'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => "Others", 'data'=>$homeTeamAttendance['totalOthers'], 'dataThisMonth'=>null])
                            </div>

                            {{--    Player Attendance    --}}
                            <div class="page-separator">
                                <div class="page-separator__text">Player Attendance</div>
                            </div>
                            <div class=".player-attendance">
                                @include('pages.academies.reports.schedules.player-attendance-data', ['players' => $homePlayers])
                            </div>

                            {{--    Coach Attendance    --}}
                            <div class="page-separator">
                                <div class="page-separator__text">Coach Attendance</div>
                            </div>
                            <div class=".coach-attendance">
                                @include('pages.academies.reports.schedules.coach-attendance-data', ['coaches' => $homeCoaches])
                            </div>
                        </div>
                        {{--    Match Note    --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[0]->id }}Notes-tab" role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">{{ $schedule->teams[0]->teamName }} Match Note</div>
                                @if(isAllAdmin() || isCoach())
                                    <a href="" data-team="{{$schedule->teams[0]->id}}"
                                       class="btn btn-primary btn-sm ml-auto addNewNote"><span
                                                class="material-icons mr-2">add</span> Add new note</a>
                                @endif
                            </div>
                            @if(count($homeTeamNotes)==0)
                                <x-warning-alert text="Match session note haven't created yet by coach"/>
                            @endif
                            @foreach($homeTeamNotes as $note)
                                <x-event-note-card :note="$note"
                                                   :deleteRoute="route('match-schedules.destroy-note', ['schedule' => $schedule->id, 'note'=>':id'])"/>
                            @endforeach
                        </div>
                        {{--    PLAYER SKILLS EVALUATION SECTION    --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[0]->id }}Skills-tab" role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">{{ $schedule->teams[0]->teamName }} player skills
                                    evaluation
                                </div>
                            </div>
                            @if(isAllAdmin() || isCoach())
                                <x-player-skill-event-tables
                                        :route="route('match-schedules.player-skills', ['schedule' => $schedule->id])"
                                        tableId="team{{ $schedule->teams[0]->id }}PlayerSkillsTable"
                                        :teamId="$schedule->teams[0]->id"/>
                            @elseif(isPlayer())
                                <x-cards.player-skill-stats-card :allSkills="$data['allSkills']"/>
                            @endif
                        </div>
                        {{--    PLAYER PERFORMANCE REVIEW SECTION   --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[0]->id }}Performance-tab"
                             role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">{{ $schedule->teams[0]->teamName }} player performance
                                    review
                                </div>
                            </div>
                            @if(isAllAdmin() || isCoach())
                                <x-player-performance-review-event-table
                                        :route="route('match-schedules.player-performance-review', ['schedule' => $schedule->id])"
                                        tableId="team{{ $schedule->teams[0]->id }}PlayerPerformanceReviewTable"
                                        :teamId="$schedule->teams[0]->id"/>
                            @elseif(isPlayer())
                                @if(count($data['playerPerformanceReviews'])==0)
                                    <x-warning-alert
                                            text="You haven't get any performance review from your coach for this match session"/>
                                @endif
                                @foreach($data['playerPerformanceReviews'] as $review)
                                    <x-player-event-performance-review :review="$review"/>
                                @endforeach
                            @endif
                        </div>
                    @endif
                    @if(in_array($schedule->teams[1]->id, $userTeams))
                        {{--    Player Stats    --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[1]->id }}PlayerStats-tab"
                             role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">Player Stats</div>
                            </div>
                            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0"
                                               id="team{{ $schedule->teams[1]->id }}PlayerStatsTable">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Minutes Played</th>
                                                <th>Goals</th>
                                                <th>Assists</th>
                                                <th>Own Goals</th>
                                                <th>Shots</th>
                                                <th>Passes</th>
                                                <th>Fouls</th>
                                                <th>Yellow Cards</th>
                                                <th>Red Cards</th>
                                                <th>Saves</th>
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
                        {{--     Attendance    --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[1]->id }}Attendance-tab" role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">{{ $schedule->teams[1]->teamName }} Attendance
                                    Overview
                                </div>
                            </div>
                            <div class="row card-group-row">
                                @include('components.stats-card', ['title' => 'Total Participants', 'data'=>$awayTeamAttendance['totalParticipant'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => 'Attended', 'data'=>$awayTeamAttendance['totalAttend'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => "Didn't Attended", 'data'=>$awayTeamAttendance['totalDidntAttend'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => "Illness", 'data'=>$awayTeamAttendance['totalIllness'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => "Injured", 'data'=>$awayTeamAttendance['totalInjured'], 'dataThisMonth'=>null])
                                @include('components.stats-card', ['title' => "Others", 'data'=>$awayTeamAttendance['totalOthers'], 'dataThisMonth'=>null])
                            </div>

                            {{--    Player Attendance    --}}
                            <div class="page-separator">
                                <div class="page-separator__text">Player Attendance</div>
                            </div>
                            <div class=".player-attendance">
                                @include('pages.academies.reports.schedules.player-attendance-data', ['players' => $awayPlayers])
                            </div>

                            {{--    Coach Attendance    --}}
                            <div class="page-separator">
                                <div class="page-separator__text">Coach Attendance</div>
                            </div>
                            <div class=".coach-attendance">
                                @include('pages.academies.reports.schedules.coach-attendance-data', ['coaches' => $awayCoaches])
                            </div>
                        </div>
                        {{--    Match Note    --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[1]->id }}Notes-tab" role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">{{ $schedule->teams[1]->teamName }} Match Note</div>
                                @if(isAllAdmin() || isCoach())
                                    <a href="" data-team="{{$schedule->teams[1]->id}}"
                                       class="btn btn-primary btn-sm ml-auto addNewNote"><span
                                                class="material-icons mr-2">add</span> Add new note</a>
                                @endif
                            </div>
                            @if(count($awayTeamNotes)==0)
                                <x-warning-alert text="Match session note haven't created yet by coach"/>
                            @endif
                            @foreach($awayTeamNotes as $note)
                                <x-event-note-card :note="$note"
                                                   :deleteRoute="route('match-schedules.destroy-note', ['schedule' => $schedule->id, 'note'=>':id'])"/>
                            @endforeach
                        </div>
                        {{--    PLAYER SKILLS EVALUATION SECTION    --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[1]->id }}Skills-tab" role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">{{ $schedule->teams[0]->teamName }} player skills
                                    evaluation
                                </div>
                            </div>
                            @if(isAllAdmin() || isCoach())
                                <x-player-skill-event-tables
                                        :route="route('match-schedules.player-skills', ['schedule' => $schedule->id])"
                                        tableId="team{{ $schedule->teams[1]->id }}PlayerSkillsTable"
                                        :teamId="$schedule->teams[1]->id"/>
                            @elseif(isPlayer())
                                <x-cards.player-skill-stats-card :allSkills="$data['allSkills']"/>
                            @endif
                        </div>
                        {{--    PLAYER PERFORMANCE REVIEW SECTION   --}}
                        <div class="tab-pane fade" id="team{{ $schedule->teams[1]->id }}Performance-tab"
                             role="tabpanel">
                            <div class="page-separator">
                                <div class="page-separator__text">{{ $schedule->teams[1]->teamName }} player performance
                                    review
                                </div>
                            </div>
                            @if(isAllAdmin() || isCoach())
                                <x-player-performance-review-event-table
                                        :route="route('match-schedules.player-performance-review', ['schedule' => $schedule->id])"
                                        tableId="team{{ $schedule->teams[1]->id }}PlayerPerformanceReviewTable"
                                        :teamId="$schedule->teams[1]->id"/>
                            @elseif(isPlayer())
                                @if(count($data['playerPerformanceReviews'])==0)
                                    <x-warning-alert
                                            text="You haven't get any performance review from your coach for this match session"/>
                                @endif
                                @foreach($data['playerPerformanceReviews'] as $review)
                                    <x-player-event-performance-review :review="$review"/>
                                @endforeach
                            @endif
                        </div>
                    @endif
                @else
                    {{--    Player Stats    --}}
                    <div class="tab-pane fade" id="playerStats-tab" role="tabpanel">
                        <div class="page-separator">
                            <div class="page-separator__text">Player Stats</div>
                        </div>
                        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="playerStatTable">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Minutes Played</th>
                                            <th>Goals</th>
                                            <th>Assists</th>
                                            <th>Own Goals</th>
                                            <th>Shots</th>
                                            <th>Passes</th>
                                            <th>Fouls</th>
                                            <th>Yellow Cards</th>
                                            <th>Red Cards</th>
                                            <th>Saves</th>
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
                    {{--     Attendance    --}}
                    <div class="tab-pane fade" id="attendance-tab" role="tabpanel">
                        <div class="page-separator">
                            <div class="page-separator__text">Attendance Overview</div>
                        </div>
                        <div class="row card-group-row">
                            @include('components.stats-card', ['title' => 'Total Participants', 'data'=>$homeTeamAttendance['totalParticipant'], 'dataThisMonth'=>null])
                            @include('components.stats-card', ['title' => 'Attended', 'data'=>$homeTeamAttendance['totalAttend'], 'dataThisMonth'=>null])
                            @include('components.stats-card', ['title' => "Didn't Attended", 'data'=>$homeTeamAttendance['totalDidntAttend'], 'dataThisMonth'=>null])
                            @include('components.stats-card', ['title' => "Illness", 'data'=>$homeTeamAttendance['totalIllness'], 'dataThisMonth'=>null])
                            @include('components.stats-card', ['title' => "Injured", 'data'=>$homeTeamAttendance['totalInjured'], 'dataThisMonth'=>null])
                            @include('components.stats-card', ['title' => "Others", 'data'=>$homeTeamAttendance['totalOthers'], 'dataThisMonth'=>null])
                        </div>

                        {{--    Player Attendance    --}}
                        <div class="page-separator">
                            <div class="page-separator__text">Player Attendance</div>
                        </div>
                        <div class=".player-attendance">
                            @include('pages.academies.reports.schedules.player-attendance-data', ['players' => $homePlayers])
                        </div>

                        {{--    Coach Attendance    --}}
                        <div class="page-separator">
                            <div class="page-separator__text">Coach Attendance</div>
                        </div>
                        <div class=".coach-attendance">
                            @include('pages.academies.reports.schedules.coach-attendance-data', ['coaches' => $homeCoaches])
                        </div>
                    </div>
                    {{--    Match Note    --}}
                    <div class="tab-pane fade" id="notes-tab" role="tabpanel">
                        <div class="page-separator">
                            <div class="page-separator__text">Match Note</div>
                            @if(isAllAdmin() || isCoach())
                                <a href="" data-team="{{$schedule->teams[0]->id}}"
                                   class="btn btn-primary btn-sm ml-auto addNewNote"><span class="material-icons mr-2">add</span>
                                    Add new note</a>
                            @endif
                        </div>
                        @if(count($schedule->notes)==0)
                            <x-warning-alert text="Match session note haven't created yet by coach"/>
                        @endif
                        @foreach($schedule->notes as $note)
                            <x-event-note-card :note="$note"
                                               :deleteRoute="route('match-schedules.destroy-note', ['schedule' => $schedule->id, 'note'=>':id'])"/>
                        @endforeach
                    </div>
                    {{--    PLAYER SKILLS EVALUATION SECTION    --}}
                    <div class="tab-pane fade" id="skills-tab" role="tabpanel">
                        <div class="page-separator">
                            <div class="page-separator__text">player skills evaluation</div>
                        </div>
                        @if(isAllAdmin() || isCoach())
                            <x-player-skill-event-tables
                                    :route="route('match-schedules.player-skills', ['schedule' => $schedule->id])"
                                    tableId="playerSkillsTable"/>
                        @elseif(isPlayer())
                            <x-cards.player-skill-stats-card :allSkills="$data['allSkills']"/>
                        @endif
                    </div>
                    {{--    PLAYER PERFORMANCE REVIEW SECTION   --}}
                    <div class="tab-pane fade" id="performance-tab" role="tabpanel">
                        <div class="page-separator">
                            <div class="page-separator__text">player performance review</div>
                        </div>
                        @if(isAllAdmin() || isCoach())
                            <x-player-performance-review-event-table
                                    :route="route('match-schedules.player-performance-review', ['schedule' => $schedule->id])"
                                    tableId="playerPerformanceReviewTable"/>
                        @elseif(isPlayer())
                            @if(count($data['playerPerformanceReviews'])==0)
                                <x-warning-alert
                                        text="You haven't get any performance review from your coach for this match session"/>
                            @endif
                            @foreach($data['playerPerformanceReviews'] as $review)
                                <x-player-event-performance-review :review="$review"/>
                            @endforeach
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{--    delete match confirmation   --}}
    <x-process-data-confirmation btnClass=".delete"
                                 :processRoute="route('match-schedules.destroy', ['schedule' => ':id'])"
                                 :routeAfterProcess="route('match-schedules.index')"
                                 method="DELETE"
                                 confirmationText="Are you sure to delete this match {{ $schedule->teams[0]->teamName }} Vs. {{ $schedule->teams[1]->teamName }}?"
                                 errorText="Something went wrong when deleting the match {{ $schedule->teams[0]->teamName }} Vs. {{ $schedule->teams[1]->teamName }}!"/>

    {{--    delete team scorer confirmation   --}}
    <x-process-data-confirmation btnClass=".delete-scorer"
                                 :processRoute="route('match-schedules.destroy-match-scorer', ['schedule' => $schedule->id, 'scorer'=>':id'])"
                                 :routeAfterProcess="route('match-schedules.show', ['schedule' => $schedule->id])"
                                 method="DELETE"
                                 confirmationText="Are you sure to delete this scorer?"
                                 errorText="Something went wrong when deleting match scorer!"/>

    {{--    delete own goal player confirmation   --}}
    <x-process-data-confirmation btnClass=".delete-own-goal"
                                 :processRoute="route('match-schedules.destroy-own-goal', ['schedule' => $schedule->id, 'scorer'=>':id'])"
                                 :routeAfterProcess="route('match-schedules.show', ['schedule' => $schedule->id])"
                                 method="DELETE"
                                 confirmationText="Are you sure to delete this own goal?"
                                 errorText="Something went wrong when deleting own goal scorer!"/>

    {{--   cancel match  --}}
    <x-process-data-confirmation btnClass=".cancelBtn"
                                 :processRoute="route('cancel-match', ['schedule' => $schedule->id])"
                                 :routeAfterProcess="route('match-schedules.show', ['schedule' => $schedule->id])"
                                 method="PATCH"
                                 confirmationText="Are you sure to cancel this match session?"
                                 errorText="Something went wrong when cancelling match session!"/>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            @if ($schedule->matchType == 'Internal Match')
            $('#team{{ $schedule->teams[0]->id }}PlayerStatsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{{ route('match-schedules.index-player-match-stats', $schedule->id) }}',
                    type: "GET",
                    data: {
                        teamId: {{ $schedule->teams[0]->id }}
                    },
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'pivot.minutesPlayed', name: 'pivot.minutesPlayed'},
                    {data: 'pivot.goals', name: 'pivot.goals'},
                    {data: 'pivot.assists', name: 'pivot.assists'},
                    {data: 'pivot.ownGoal', name: 'pivot.ownGoal'},
                    {data: 'pivot.shots', name: 'pivot.shots'},
                    {data: 'pivot.passes', name: 'pivot.passes'},
                    {data: 'pivot.fouls', name: 'pivot.fouls'},
                    {data: 'pivot.yellowCards', name: 'pivot.yellowCards'},
                    {data: 'pivot.redCards', name: 'pivot.redCards'},
                    {data: 'pivot.saves', name: 'pivot.saves'},
                    {data: 'updated_at', name: 'updated_at'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });
            $('#team{{ $schedule->teams[1]->id }}PlayerStatsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{{ route('match-schedules.index-player-match-stats', $schedule->id) }}',
                    type: "GET",
                    data: {
                        teamId: {{ $schedule->teams[1]->id }}
                    },
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'pivot.minutesPlayed', name: 'pivot.minutesPlayed'},
                    {data: 'pivot.goals', name: 'pivot.goals'},
                    {data: 'pivot.assists', name: 'pivot.assists'},
                    {data: 'pivot.ownGoal', name: 'pivot.ownGoal'},
                    {data: 'pivot.shots', name: 'pivot.shots'},
                    {data: 'pivot.passes', name: 'pivot.passes'},
                    {data: 'pivot.fouls', name: 'pivot.fouls'},
                    {data: 'pivot.yellowCards', name: 'pivot.yellowCards'},
                    {data: 'pivot.redCards', name: 'pivot.redCards'},
                    {data: 'pivot.saves', name: 'pivot.saves'},
                    {data: 'updated_at', name: 'updated_at'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });
            @else
            $('#playerStatTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('match-schedules.index-player-match-stats', $schedule->id) !!}',
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'pivot.minutesPlayed', name: 'pivot.minutesPlayed'},
                    {data: 'pivot.goals', name: 'pivot.goals'},
                    {data: 'pivot.assists', name: 'pivot.assists'},
                    {data: 'pivot.ownGoal', name: 'pivot.ownGoal'},
                    {data: 'pivot.shots', name: 'pivot.shots'},
                    {data: 'pivot.passes', name: 'pivot.passes'},
                    {data: 'pivot.fouls', name: 'pivot.fouls'},
                    {data: 'pivot.yellowCards', name: 'pivot.yellowCards'},
                    {data: 'pivot.redCards', name: 'pivot.redCards'},
                    {data: 'pivot.saves', name: 'pivot.saves'},
                    {data: 'updated_at', name: 'updated_at'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });
            @endif

        });
    </script>
@endpush
