@extends('layouts.master')
@section('title')
    Training {{ $schedule->eventName  }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    @if(isAllAdmin() || isCoach())
        <x-edit-player-attendance-modal
                :routeGet="route('training-schedules.player', ['schedule' => $schedule->hash, 'player' => ':id'])"
                :routeUpdate="route('training-schedules.update-player', ['schedule' => $schedule->hash, 'player' => ':id'])"/>

        <x-edit-coach-attendance-modal
                :routeGet="route('training-schedules.coach', ['schedule' => $schedule->hash, 'coach' => ':id'])"
                :routeUpdate="route('training-schedules.update-coach', ['schedule' => $schedule->hash, 'coach' => ':id'])"/>

        <x-create-schedule-note-modal :routeCreate="route('training-schedules.create-note', $schedule->hash)"
                                      :eventName="$schedule->eventName"/>

        <x-edit-schedule-note-modal
                :routeEdit="route('training-schedules.edit-note', ['schedule' => $schedule->hash, 'note' => ':id'])"
                :routeUpdate="route('training-schedules.update-note', ['schedule' => $schedule->hash, 'note' => ':id'])"
                :eventName="$schedule->eventName"/>

        <x-skill-assessments-modal/>
        <x-edit-skill-assessments-modal/>

        <x-add-performance-review-modal :routeCreate="route('coach.performance-reviews.store', ['player'=> ':id'])"/>
        <x-edit-performance-review-modal/>
    @endif
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('training-schedules.index') }}" class="nav-link text-70">
                        <i class="material-icons icon--left">keyboard_backspace</i>
                        Back to Training Schedules
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <div class="flex">
                <h2 class="text-white mb-0">{{ $schedule->eventName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $schedule->eventType }}
                    ~ {{ $schedule->teams[0]->teamName }}</p>
            </div>
            @if(isAllAdmin() || isCoach())
                <div class="dropdown">
                    <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        Action<span class="material-icons ml-3">keyboard_arrow_down</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"
                           href="{{ route('training-schedules.edit', $schedule->hash) }}">
                            <span class="material-icons">edit</span> Edit Training Schedule
                        </a>
                        @if($schedule->status != 'Cancelled' && $schedule->status != 'Completed')
                            <button type="submit" class="dropdown-item cancelBtn" id="{{ $schedule->hash }}">
                                <span class="material-icons text-danger">block</span>Cancel Training
                            </button>
                        @endif
                        <button type="button" class="dropdown-item delete" id="{{$schedule->hash}}">
                            <span class="material-icons text-danger">delete</span> Delete Training
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
                @if(isAllAdmin())
                    <li class="nav-item navbar-list__item">
                        <div class="media align-items-center">
                        <span class="media-left mr-16pt">
                            <img src="{{Storage::url($schedule->user->foto) }}" width="30" alt="avatar"
                                 class="rounded-circle">
                        </span>
                            <div class="media-body">
                                <a class="card-title m-0" href="">Created
                                    by {{$schedule->user->firstName}} {{$schedule->user->lastName}}</a>
                                <p class="text-50 lh-1 mb-0">Admin</p>
                            </div>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <nav class="navbar navbar-light border-bottom border-top py-3">
        <div class="container">
            <ul class="nav nav-pills text-capitalize">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#overview-tab">Overview & Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#notes-tab">Training Note</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#skills-tab">player skills evaluation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#performance-tab">player performance review</a>
                </li>
            </ul>
        </div>
    </nav>

    {{--    Attendance Overview    --}}
    <div class="container page__container page-section">
        <div class="tab-content">

            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row card-group-row">
                    @include('components.stats-card', ['title' => 'Total Participants', 'data'=>$attendance['totalParticipant'], 'dataThisMonth'=>null])
                    @include('components.stats-card', ['title' => 'Attended', 'data'=>$attendance['totalAttend'], 'dataThisMonth'=>null])
                    @include('components.stats-card', ['title' => "Didn't Attended", 'data'=>$attendance['totalDidntAttend'], 'dataThisMonth'=>null])
                    @include('components.stats-card', ['title' => "Illness", 'data'=>$attendance['totalIllness'], 'dataThisMonth'=>null])
                    @include('components.stats-card', ['title' => "Injured", 'data'=>$attendance['totalInjured'], 'dataThisMonth'=>null])
                    @include('components.stats-card', ['title' => "Others", 'data'=>$attendance['totalOthers'], 'dataThisMonth'=>null])
                </div>

                {{--    Player Attendance    --}}
                <div class="page-separator">
                    <div class="page-separator__text">Player Attendance</div>
                </div>
                <div class=".player-attendance">
                    @include('pages.academies.schedules.player-attendance-data')
                </div>

                {{--    Coach Attendance    --}}
                <div class="page-separator">
                    <div class="page-separator__text">Coach Attendance</div>
                </div>
                <div class=".coach-attendance">
                    @include('pages.academies.schedules.coach-attendance-data')
                </div>
            </div>

            {{--    Training Note    --}}
            <div class="tab-pane fade" id="notes-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Training Note</div>
                    @if(isAllAdmin() || isCoach())
                        <a href="" data-team="{{$schedule->teams[0]->id}}"
                           class="btn btn-primary btn-sm ml-auto addNewNote"><span
                                class="material-icons mr-2">add</span> Add new note</a>
                    @endif
                </div>
                @if(count($schedule->notes)==0)
                    <x-warning-alert text="Training session note haven't created yet by coach"/>
                @endif
                @foreach($schedule->notes as $note)
                    <x-event-note-card :note="$note"
                                       :deleteRoute="route('training-schedules.destroy-note', ['schedule' => $schedule->hash, 'note'=>':id'])"/>
                @endforeach
            </div>

            {{--    PLAYER SKILLS EVALUATION SECTION    --}}
            <div class="tab-pane fade" id="skills-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">player skills evaluation</div>
                </div>
                @if(isAllAdmin() || isCoach())
                    <x-player-skill-event-tables
                            :route="route('training-schedules.player-skills', ['schedule' => $schedule->hash])"
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
                            :route="route('training-schedules.player-performance-review', ['schedule' => $schedule->hash])"
                            tableId="playerPerformanceReviewTable"/>
                @elseif(isPlayer())
                    @if(count($data['playerPerformanceReviews'])==0)
                        <x-warning-alert
                                text="You haven't get any performance review from your coach for this match session"/>
                    @else
                        @foreach($data['playerPerformanceReviews'] as $review)
                            <x-player-event-performance-review :review="$review"/>
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>

    <x-process-data-confirmation btnClass=".delete"
                                 :processRoute="route('training-schedules.destroy', ['schedule' => ':id'])"
                                 :routeAfterProcess="route('training-schedules.index')"
                                 method="DELETE"
                                 confirmationText="Are you sure to delete this training session {{ $schedule->eventName }}?"
                                 errorText="Something went wrong when deleting training session {{ $schedule->eventName }}!"/>

    <x-process-data-confirmation btnClass=".cancelBtn"
                                 :processRoute="route('cancel-training', ['schedule' => ':id'])"
                                 :routeAfterProcess="route('training-schedules.show', $schedule->hash)"
                                 method="PATCH"
                                 confirmationText="Are you sure to cancel competition {{ $schedule->eventName }}?"
                                 errorText="Something went wrong when marking training session {{ $schedule->eventName }} as cancelled!"/>

@endsection
