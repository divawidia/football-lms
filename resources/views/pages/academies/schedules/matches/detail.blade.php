@extends('layouts.master')
@section('title')
    @if($schedule->matchType == 'External Match')
        Match {{ $homeTeam->teamName }} Vs {{ $schedule->externalTeam->teamName }}
    @else
        Match {{ $homeTeam->teamName }} Vs {{ $awayTeam->teamName }}
    @endif

@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.matches.edit-player-attendance :schedule="$schedule"/>

    <x-modal.matches.edit-coach-attendance :schedule="$schedule"/>

    <x-modal.matches.create-schedule-note :schedule="$schedule"/>
    <x-modal.matches.edit-schedule-note :eventSchedule="$schedule"/>

    <x-skill-assessments-modal/>
    <x-edit-skill-assessments-modal/>

    <x-modal.matches.add-performance-review/>
    <x-modal.matches.edit-performance-review/>

    <!-- Modal add team scorer -->
    <x-modal.matches.add-team-scorer :eventSchedule="$schedule"/>

    <!-- Modal edit external team score -->
    <x-modal.matches.edit-external-team-score :eventSchedule="$schedule"/>

    <!-- Modal add team own goal scorer -->
    <x-modal.matches.add-team-own-goal :eventSchedule="$schedule"/>

    <!-- Modal add team match stats -->
    <x-modal.matches.edit-team-match-stats :eventSchedule="$schedule"/>

    <!-- Modal update player match stats -->
    <x-edit-player-match-stats-modal :eventSchedule="$schedule"/>

    <x-modal.matches.edit-match :competition="$schedule->competition"/>
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
        <div
            class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <div class="flex mb-3 mb-md-0">
                <h2 class="text-white mb-0">
                    @yield('title')
                </h2>
                <p class="lead text-white-50">
                    {{ $schedule->matchType }} ~ {{$schedule->competition->name}}
                </p>
            </div>
            @if(isAllAdmin())
                <x-buttons.dropdown>
                    @if($schedule->status == 'Scheduled')
                        <x-buttons.basic-button icon="edit" text="Edit Match" additionalClass="edit-match-btn"
                                                :dropdownItem="true" :id="$schedule->hash" color=""/>
                        <x-buttons.basic-button icon="block" text="Cancel Match" additionalClass="cancelBtn"
                                                :dropdownItem="true" :id="$schedule->hash" color="" iconColor="danger"/>
                    @elseif($schedule->status == 'Cancelled')
                        <x-buttons.basic-button icon="edit" text="Edit Match" additionalClass="edit-match-btn"
                                                :dropdownItem="true" :id="$schedule->hash" color=""/>
                        <x-buttons.basic-button icon="check_circle" text="Set Match to Scheduled"
                                                additionalClass="scheduled-btn" :dropdownItem="true"
                                                :id="$schedule->hash" color="" iconColor="warning"/>
                    @endif
                    <x-buttons.basic-button icon="delete" text="Delete Match" additionalClass="delete"
                                            :dropdownItem="true" :id="$schedule->hash" color="" iconColor="danger"/>
                </x-buttons.dropdown>
            @endif
        </div>
    </div>

    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">
                    Status :
                    @php
                        $statusClasses = [
                            'Scheduled' => 'badge-warning',
                            'Ongoing' => 'badge-info',
                            'Completed' => 'badge-success',
                        ];
                        $statusClass = $statusClasses[$schedule->status] ?? 'badge-danger';
                    @endphp

                    <span class="badge badge-pill {{ $statusClass }}">{{ $schedule->status }}</span>
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">event</i>
                    {{ convertToDate($schedule->date) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                    {{ convertToTime($schedule->startTime) }}
                    - {{ convertToTime($schedule->endTime) }}
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

    <x-tabs.navbar>
        <x-tabs.item title="Overview" link="overview" :active="true"/>
        <x-tabs.item title="Match Stats" link="matchStats"/>

        @if ($schedule->matchType == 'Internal Match')
            @if(in_array($homeTeam->id, $userTeams))
                <x-tabs.item title="{{ $homeTeam->teamName }} Player Stats" link="playerStats"/>
                <x-tabs.item title="{{ $homeTeam->teamName }} Attendance" link="attendance"/>
                <x-tabs.item title="{{ $homeTeam->teamName }} Session Notes" link="notes"/>
                <x-tabs.item title="{{ $homeTeam->teamName }} skills evaluation" link="skills"/>
                <x-tabs.item title="{{ $homeTeam->teamName }} performance review" link="performance"/>
            @endif

            @if(in_array($awayTeam->id, $userTeams))
                <x-tabs.item title="{{ $awayTeam->teamName }} Player Stats" link="team{{ $awayTeam->id }}PlayerStats"/>
                <x-tabs.item title="{{ $awayTeam->teamName }} Attendance" link="team{{ $awayTeam->id }}Attendance"/>
                <x-tabs.item title="{{ $awayTeam->teamName }} Session Notes" link="team{{ $awayTeam->id }}Notes"/>
                <x-tabs.item title="{{ $awayTeam->teamName }} skills evaluation" link="team{{ $awayTeam->id }}Skills"/>
                <x-tabs.item title="{{ $awayTeam->teamName }} performance review"
                             link="team{{ $awayTeam->id }}Performance"/>
            @endif
        @else
            <x-tabs.item title="Player Stats" link="playerStats"/>
            <x-tabs.item title="Attendance" link="attendance"/>
            <x-tabs.item title="Session Notes" link="notes"/>
            <x-tabs.item title="skills evaluation" link="skills"/>
            <x-tabs.item title="performance review" link="performance"/>
        @endif
    </x-tabs.navbar>

    <div class="container page__container page-section">
        <div class="tab-content">

            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Team Score</div>
                    @if(isAllAdmin() and $schedule->status == 'Ongoing' and $schedule->matchType == 'External Match')
                        <x-buttons.basic-button icon="add" text="edit {{ $schedule->externalTeam->teamName }} score"
                                                size="sm" margin="ml-auto" additionalClass="edit-team-score-btn"/>
                    @endif
                </div>

                {{--    Team Match Score    --}}
                <div class="card px-lg-5">
                    <div class="card-body">
                        <div class="row d-flex">
                            <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                                <img src="{{ Storage::url($homeTeam->logo) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="instructor">
                                <div class="ml-md-3 text-center text-md-left">
                                    <h5 class="mb-0">{{ $homeTeam->teamName }}</h5>
                                    <p class="text-50 lh-1 mb-0">Own Goal : {{ $homeTeam->pivot->teamOwnGoal }}</p>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <h2 class="mb-0">
                                    @if($schedule->matchType == 'Internal Match')
                                        {{ $homeTeam->pivot->teamScore }} - {{ $awayTeam->pivot->teamScore }}
                                    @else
                                        {{ $homeTeam->pivot->teamScore }} - {{ $schedule->externalTeam->teamScore }}
                                    @endif

                                </h2>
                            </div>
                            <div
                                class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                                @if($schedule->matchType == 'Internal Match')
                                    <div class="mr-md-3 text-center text-md-right">
                                        <h5 class="mb-0">{{ $awayTeam->teamName }}</h5>
                                        <p class="text-50 lh-1 mb-0">Own Goal : {{ $awayTeam->pivot->teamOwnGoal }}</p>
                                    </div>
                                    <img src="{{ Storage::url($awayTeam->logo) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="instructor">
                                @else
                                    <div class="mr-md-3 text-center text-md-right">
                                        <h5 class="mb-0">{{ $schedule->externalTeam->teamName }}</h5>
                                        <p class="text-50 lh-1 mb-0">Own Goal
                                            : {{ $schedule->externalTeam->teamOwnGoal }}</p>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                {{--    Match Scorer    --}}
                <div class="page-separator">
                    <div class="page-separator__text">{{ $homeTeam->teamName }} Scorer(s)</div>

                    @if(isAllAdmin() and $schedule->status == 'Ongoing')
                        <x-buttons.basic-button icon="add" text="Add team scorer" size="sm" margin="ml-auto"
                                                additionalClass="addTeamScorer" id="homeTeam"/>
                        <x-buttons.basic-button icon="add" text="Add own goal" size="sm" margin="ml-2"
                                                additionalClass="addOwnGoal" id="homeTeam"/>
                    @endif
                </div>

                @if(count($homeTeamMatchScorers)==0)
                    <x-warning-alert text="You haven't added any team scorer yet"/>
                @endif
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
                                    @if(isAllAdmin() || isCoach() and $schedule->status == 'Ongoing')
                                        <button
                                            class="btn btn-sm btn-outline-secondary @if($matchScore->isOwnGoal == 1) delete-own-goal @else delete-scorer @endif"
                                            type="button"
                                            id="{{ $matchScore->id }}"
                                            data-toggle="tooltip"
                                            data-placement="bottom"
                                            title="Delete scorer">
                                            <span class="material-icons">close</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($schedule->matchType == 'Internal Match')
                    <div class="page-separator">
                        <div class="page-separator__text">{{ $awayTeam->teamName }} Scorer(s)</div>
                        @if(isAllAdmin() and $schedule->status == 'Ongoing')
                            <x-buttons.basic-button icon="add" text="Add team scorer" size="sm" margin="ml-auto"
                                                    additionalClass="addTeamScorer" id="awayTeam"/>
                            <x-buttons.basic-button icon="add" text="Add own goal" size="sm" margin="ml-2"
                                                    additionalClass="addOwnGoal" id="awayTeam"/>
                        @endif
                    </div>

                    @if(count($awayTeamMatchScorers)==0)
                        <x-warning-alert text="You haven't added any team scorer yet"/>
                    @endif

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
                                                <p class="text-50 ml-2 mb-0">({{ $matchScore->minuteScored }}')</p>
                                            </h5>
                                            <p class="text-50 lh-1 mb-0">{{ $matchScore->player->position->name }}</p>
                                            @if($matchScore->isOwnGoal == 1)
                                                <p class="text-50 lh-1 mb-0"><strong>Own Goal</strong></p>
                                            @elseif($matchScore->assistPlayer)
                                                <p class="text-50 lh-1 mb-0">
                                                    Assist: {{ $matchScore->assistPlayer->user->firstName }} {{ $matchScore->assistPlayer->user->lastName }}</p>
                                            @endif
                                        </div>
                                        @if(isAllAdmin() || isCoach() and $schedule->status == 'Ongoing')
                                            <button
                                                class="btn btn-sm btn-outline-secondary @if($matchScore->isOwnGoal == 1) delete-own-goal @else delete-scorer @endif"
                                                type="button"
                                                id="{{ $matchScore->id }}"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="Delete scorer">
                                                <span class="material-icons">close</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{--    Match Stats    --}}
            <div class="tab-pane fade" id="matchStats-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Match Stats</div>

                    @if(isAllAdmin() and $schedule->status == 'Ongoing' or $schedule->status == 'Completed' )
                        <x-buttons.basic-button icon="add" text="Update {{ $homeTeam->teamName }} match stats"
                                                id="homeTeam" size="sm" margin="ml-auto"
                                                additionalClass="update-team-match-stats-btn"/>
                        @if($schedule->matchType == 'Internal Match')
                            <x-buttons.basic-button icon="add" text="Update {{ $awayTeam->teamName }} match stats"
                                                    id="awayTeam" size="sm" margin="ml-2"
                                                    additionalClass="update-team-match-stats-btn"/>
                        @else
                            <x-buttons.basic-button icon="add"
                                                    text="Update {{ $schedule->externalTeam->teamName }} match stats"
                                                    id="externalTeam" size="sm" margin="ml-2"
                                                    additionalClass="update-team-match-stats-btn"/>
                        @endif
                    @endif
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <img src="{{ Storage::url($homeTeam->logo) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="instructor">
                                <div class="flex ml-3">
                                    <h5 class="mb-0">{{ $homeTeam->teamName }}</h5>
                                </div>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                @if($schedule->matchType == 'Internal Match')
                                    <div class="mr-3">
                                        <h5 class="mb-0">{{ $awayTeam->teamName }}</h5>
                                    </div>
                                    <img src="{{ Storage::url($awayTeam->logo) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="instructor">
                                @else
                                    <div class="mr-3">
                                        <h5 class="mb-0">{{ $schedule->externalTeam->teamName }}</h5>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamPossesion }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Possession %</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamPossesion }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamPossesion }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamShotOnTarget }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Shots on target</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamShotOnTarget }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamShotOnTarget }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamShots }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Shots</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamShots }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamShots }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamTouches }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Touches</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamTouches }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamTouches }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamPasses }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Passes</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamPasses }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamPasses }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamTackles }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Tackles</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamTackles }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamTackles }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamClearances }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Clearances</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamClearances }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamClearances }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamCorners }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Corners</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamCorners }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamCorners }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamOffsides }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Offsides</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamOffsides }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamOffsides }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamYellowCards }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Yellow cards</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamYellowCards }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamYellowCards }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong class="flex">{{ $homeTeam->pivot->teamRedCards }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Red cards</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamRedCards }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamRedCards }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row text-center">
                                <div class="col-4">
                                    <strong
                                        class="flex">{{ $homeTeam->pivot->teamFoulsConceded }}</strong>
                                </div>
                                <div class="col-4">
                                    <strong class="flex">Fouls conceded</strong>
                                </div>
                                <div class="col-4">
                                    @if($schedule->matchType == 'Internal Match')
                                        <strong class="flex">{{ $awayTeam->pivot->teamFoulsConceded }}</strong>
                                    @else
                                        <strong class="flex">{{ $schedule->externalTeam->teamFoulsConceded }}</strong>
                                    @endif
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{--    Player Stats    --}}
            <div class="tab-pane fade" id="playerStats-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Player Stats</div>
                </div>
                <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                    <div class="card-body">
                        <x-table
                            :headers="['Name', 'Minutes Played', 'Goals', 'Assists', 'Own Goals', 'Shots', 'Passes', 'Fouls', 'Yellow Cards', 'Red Cards', 'Saves', 'Last Updated', 'Action']"
                            tableId="playerStatsTable"/>
                    </div>
                </div>
            </div>

            {{--     Attendance    --}}
            <div class="tab-pane fade" id="attendance-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Attendance Overview</div>
                </div>
                <div class="row card-group-row">
                    @include('components.cards.stats-card', ['title' => 'Total Participants', 'data'=>$homeTeamAttendance['totalParticipant'], 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => 'Attended', 'data'=>$homeTeamAttendance['totalAttend'], 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => "Didn't Attended", 'data'=>$homeTeamAttendance['totalDidntAttend'], 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => "Illness", 'data'=>$homeTeamAttendance['totalIllness'], 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => "Injured", 'data'=>$homeTeamAttendance['totalInjured'], 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => "Others", 'data'=>$homeTeamAttendance['totalOthers'], 'dataThisMonth'=>null])
                </div>

                {{--    Player Attendance    --}}
                <div class="page-separator">
                    <div class="page-separator__text">Player Attendance</div>
                </div>
                <div class=".player-attendance">
                    @include('pages.academies.schedules.player-attendance-data', ['players' => $homePlayers])
                </div>

                {{--    Coach Attendance    --}}
                <div class="page-separator">
                    <div class="page-separator__text">Coach Attendance</div>
                </div>
                <div class=".coach-attendance">
                    @include('pages.academies.schedules.coach-attendance-data', ['coaches' => $homeCoaches])
                </div>
            </div>

            {{--    Match Note    --}}
            <div class="tab-pane fade" id="notes-tab" role="tabpanel">

                <div class="page-separator">
                    <div class="page-separator__text">Match Note</div>
                    @if(isAllAdmin() || isCoach() and $schedule->status == 'Ongoing' || $schedule->status == "Completed")
                        <x-buttons.basic-button icon="add" text="add new note" size="sm" margin="ml-auto"
                                                :id="$homeTeam->id" additionalClass="add-new-note-btn"/>
                    @endif
                </div>

                @if(count($schedule->notes)==0)
                    <x-warning-alert text="Match session note haven't created yet by coach"/>
                @endif

                @foreach($schedule->notes as $note)
                    <x-cards.event-note :note="$note" :schedule="$schedule"/>
                @endforeach

            </div>

            {{--    PLAYER SKILLS EVALUATION SECTION    --}}
            <div class="tab-pane fade" id="skills-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">player skills evaluation</div>
                </div>
                @if(isAllAdmin() || isCoach())
                    <x-tables.player-skill-event :eventSchedule="$schedule" :teamId="$homeTeam->id"
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
                    <x-tables.player-performance-review-event :eventSchedule="$schedule" :teamId="$homeTeam->id"
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

            @if ($schedule->matchType == 'Internal Match')
                {{--    Player Stats    --}}
                <div class="tab-pane fade" id="team{{ $awayTeam->id }}PlayerStats-tab"
                     role="tabpanel">
                    <div class="page-separator">
                        <div class="page-separator__text">Player Stats</div>
                    </div>
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-body">
                            <x-table
                                :headers="['Name', 'Minutes Played', 'Goals', 'Assists', 'Own Goals', 'Shots', 'Passes', 'Fouls', 'Yellow Cards', 'Red Cards', 'Saves', 'Last Updated', 'Action']"
                                tableId="{{ $awayTeam->id }}PlayerStatsTable"/>
                        </div>
                    </div>
                </div>

                {{--     Attendance    --}}
                <div class="tab-pane fade" id="team{{ $awayTeam->id }}Attendance-tab" role="tabpanel">
                    <div class="page-separator">
                        <div class="page-separator__text">Attendance Overview</div>
                    </div>
                    <div class="row card-group-row">
                        @include('components.cards.stats-card', ['title' => 'Total Participants', 'data'=>$awayTeamAttendance['totalParticipant'], 'dataThisMonth'=>null])
                        @include('components.cards.stats-card', ['title' => 'Attended', 'data'=>$awayTeamAttendance['totalAttend'], 'dataThisMonth'=>null])
                        @include('components.cards.stats-card', ['title' => "Didn't Attended", 'data'=>$awayTeamAttendance['totalDidntAttend'], 'dataThisMonth'=>null])
                        @include('components.cards.stats-card', ['title' => "Illness", 'data'=>$awayTeamAttendance['totalIllness'], 'dataThisMonth'=>null])
                        @include('components.cards.stats-card', ['title' => "Injured", 'data'=>$awayTeamAttendance['totalInjured'], 'dataThisMonth'=>null])
                        @include('components.cards.stats-card', ['title' => "Others", 'data'=>$awayTeamAttendance['totalOthers'], 'dataThisMonth'=>null])
                    </div>

                    {{--    Player Attendance    --}}
                    <div class="page-separator">
                        <div class="page-separator__text">Player Attendance</div>
                    </div>
                    <div class=".player-attendance">
                        @include('pages.academies.schedules.player-attendance-data', ['players' => $awayPlayers])
                    </div>

                    {{--    Coach Attendance    --}}
                    <div class="page-separator">
                        <div class="page-separator__text">Coach Attendance</div>
                    </div>
                    <div class=".coach-attendance">
                        @include('pages.academies.schedules.coach-attendance-data', ['coaches' => $awayCoaches])
                    </div>
                </div>

                {{--    Match Note    --}}
                <div class="tab-pane fade" id="team{{ $awayTeam->id }}Notes-tab" role="tabpanel">
                    <div class="page-separator">
                        <div class="page-separator__text">Match Note</div>
                        @if(isAllAdmin() || isCoach() and $schedule->status == 'Ongoing' || $schedule->status == "Completed")
                            <x-buttons.basic-button icon="add" text="add new note" size="sm" margin="ml-auto"
                                                    :id="$awayTeam->id" additionalClass="add-new-note-btn"/>
                        @endif
                    </div>
                    @if(count($awayTeamNotes)==0)
                        <x-warning-alert text="Match session note haven't created yet by coach"/>
                    @endif
                    @foreach($awayTeamNotes as $note)
                        <x-cards.event-note :note="$note" :schedule="$schedule"/>
                    @endforeach
                </div>

                {{--    PLAYER SKILLS EVALUATION SECTION    --}}
                <div class="tab-pane fade" id="team{{ $awayTeam->id }}Skills-tab" role="tabpanel">
                    <div class="page-separator">
                        <div class="page-separator__text">player skills evaluation
                        </div>
                    </div>
                    @if(isAllAdmin() || isCoach())
                        <x-tables.player-skill-event :eventSchedule="$schedule"
                                                     tableId="team{{ $awayTeam->id }}PlayerSkillsTable"
                                                     :teamId="$awayTeam->id"/>
                    @elseif(isPlayer())
                        <x-cards.player-skill-stats-card :allSkills="$data['allSkills']"/>
                    @endif
                </div>

                {{--    PLAYER PERFORMANCE REVIEW SECTION   --}}
                <div class="tab-pane fade" id="team{{ $awayTeam->id }}Performance-tab" role="tabpanel">
                    <div class="page-separator">
                        <div class="page-separator__text">player performance review</div>
                    </div>
                    @if(isAllAdmin() || isCoach())
                        <x-tables.player-performance-review-event :eventSchedule="$schedule"
                                                                  tableId="team{{ $awayTeam->id }}PlayerPerformanceReviewTable"
                                                                  :teamId="$awayTeam->id"/>
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
        </div>
    </div>
@endsection

@push('addon-script')
    <script type="module">
        import {processWithConfirmation} from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;

        $(document).ready(function () {
            processWithConfirmation(
                '.delete',
                "{{ route('match-schedules.destroy', ['schedule' => ':id']) }}",
                "{{ route('match-schedules.index') }}",
                'DELETE',
                "Are you sure to delete this match?",
                "Something went wrong when deleting this match!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.delete-scorer',
                "{{ route('match-schedules.destroy-match-scorer', ['schedule' => $schedule->hash, 'scorer'=>':id']) }}",
                "{{ route('match-schedules.show', ['schedule' => $schedule->hash]) }}",
                'DELETE',
                "Are you sure to delete this match scorer?",
                "Something went wrong when deleting this match scorer!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.delete-own-goal',
                "{{ route('match-schedules.destroy-own-goal', ['schedule' => $schedule->hash, 'scorer'=>':id']) }}",
                "{{ route('match-schedules.show', ['schedule' => $schedule->hash]) }}",
                'DELETE',
                "Are you sure to delete this match own goal?",
                "Something went wrong when deleting this match own goal!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.cancelBtn',
                "{{ route('match-schedules.cancel', ['schedule' => $schedule->hash]) }}",
                "{{ route('match-schedules.show', ['schedule' => $schedule->hash]) }}",
                'PATCH',
                "Are you sure to cancel this match?",
                "Something went wrong when cancelling this match!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.scheduled-btn',
                "{{ route('match-schedules.scheduled', ['schedule' => $schedule->hash]) }}",
                "{{ route('match-schedules.show', ['schedule' => $schedule->hash]) }}",
                'PATCH',
                "Are you sure to set this match to scheduled?",
                "Something went wrong when set this match to scheduled!",
                "{{ csrf_token() }}"
            );

            $('#playerStatsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('match-schedules.index-player-match-stats', $schedule->hash) !!}',
                    type: "GET",
                    data: {
                        teamId: {{ $homeTeam->id }}
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
                        searchable: false
                    },
                ]
            });

            @if ($schedule->matchType == 'Internal Match')
            $('#{{ $awayTeam->id }}PlayerStatsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{{ route('match-schedules.index-player-match-stats', $schedule->hash) }}',
                    type: "GET",
                    data: {
                        teamId: {{ $awayTeam->id }}
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
                        searchable: false
                    },
                ]
            });
            @endif

        });
    </script>
@endpush
