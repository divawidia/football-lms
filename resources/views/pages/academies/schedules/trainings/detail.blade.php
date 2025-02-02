@extends('layouts.master')
@section('title')
    Training {{ $schedule->topic  }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    @if(isAllAdmin() || isCoach())
        <x-modal.trainings.edit-player-attendance :training="$schedule"/>

        <x-modal.trainings.edit-coach-attendance :training="$schedule"/>

        <x-modal.trainings.create-training-note :training="$schedule"/>
        <x-modal.trainings.edit-training-note :training="$schedule"/>


        <x-modal.players-coaches.skill-assessments-modal/>
        <x-modal.players-coaches.edit-skill-assessments-modal/>

        <x-modal.players-coaches.add-performance-review/>
        <x-modal.players-coaches.edit-performance-review/>

        <x-modal.trainings.edit-training/>
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
        <div
            class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <div class="flex">
                <h2 class="text-white mb-0">{{ $schedule->topic  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">
                    Training Session ~ {{ $schedule->team->teamName }}
                </p>
            </div>
            @if(isAllAdmin() || isCoach())
                <x-buttons.dropdown>
                    @if($schedule->status == 'Scheduled')
                        <x-buttons.basic-button icon="edit" text="Edit Training" additionalClass="edit-training-btn"
                                                :dropdownItem="true" :id="$schedule->hash" color=""/>
                        <x-buttons.basic-button icon="block" text="Cancel Training" additionalClass="cancelBtn"
                                                :dropdownItem="true" :id="$schedule->hash" color="" iconColor="danger"/>
                    @elseif($schedule->status == 'Cancelled')
                        <x-buttons.basic-button icon="edit" text="Edit Training" additionalClass="edit-training-btn"
                                                :dropdownItem="true" :id="$schedule->hash" color=""/>
                        <x-buttons.basic-button icon="check_circle" text="Set Training to Scheduled"
                                                additionalClass="scheduled-btn" :dropdownItem="true"
                                                :id="$schedule->hash" color="" iconColor="warning"/>
                    @endif
                    <x-buttons.basic-button icon="delete" text="Delete Training" additionalClass="delete"
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
                    {{ convertToDate($schedule->date) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                    {{ convertToTime($schedule->startTime) }}
                    - {{ convertToTime($schedule->endTime) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                    {{ $schedule->location }}
                </li>
                @if(isAllAdmin())
                    <li class="nav-item navbar-list__item">
                        <div class="media align-items-center">
                        <span class="media-left mr-16pt">
                            <img src="{{Storage::url($schedule->user->foto) }}" width="30" alt="avatar"
                                 class="rounded-circle">
                        </span>
                            <div class="media-body">
                                <a class="card-title m-0" href="">
                                    Created by {{ $schedule->user->firstName}} {{$schedule->user->lastName}}</a>
                                <p class="text-50 lh-1 mb-0">Admin</p>
                            </div>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <x-tabs.navbar>
        <x-tabs.item title="Overview" link="overview" :active="true"/>
        <x-tabs.item title="Session Notes" link="notes"/>
        <x-tabs.item title="skills evaluation" link="skills"/>
        <x-tabs.item title="performance review" link="performance"/>
    </x-tabs.navbar>

    {{--    Attendance Overview    --}}
    <div class="container page__container page-section">
        <div class="tab-content">

            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row card-group-row">
                    @include('components.cards.stats-card', ['title' => 'Total Participants', 'data'=>$totalParticipant, 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => 'Attended', 'data'=>$totalAttend, 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => "Didn't Attended", 'data'=>$totalDidntAttend, 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => "Illness", 'data'=>$totalIllness, 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => "Injured", 'data'=>$totalInjured, 'dataThisMonth'=>null])
                    @include('components.cards.stats-card', ['title' => "Others", 'data'=>$totalOther, 'dataThisMonth'=>null])
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
                    @if(isAllAdmin() || isCoach() and $schedule->status == 'Ongoing' || $schedule->status == 'Completed')
                        <x-buttons.basic-button icon="add" text="add new note" size="sm" margin="ml-auto"
                                                :id="$schedule->teamId" additionalClass="add-new-note-btn"/>
                    @endif
                </div>
                @if(count($schedule->notes)==0)
                    <x-warning-alert text="Training session note haven't created yet by coach"/>
                @endif
                @foreach($schedule->notes as $note)
                    <x-cards.training-note-card :note="$note" :training="$schedule"/>
                @endforeach
            </div>

            {{--    PLAYER SKILLS EVALUATION SECTION    --}}
            <div class="tab-pane fade" id="skills-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">player skills evaluation</div>
                </div>
                @if(isAllAdmin() || isCoach())
                    <x-tables.player-skill-event :route="route('training-schedules.player-skills', $schedule->hash)"/>
                @elseif(isPlayer())
                    <x-cards.player-skill-stats-card :allSkills="$allSkills"/>
                @endif
            </div>

            {{--    PLAYER PERFORMANCE REVIEW SECTION   --}}
            <div class="tab-pane fade" id="performance-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">player performance review</div>
                </div>
                @if(isAllAdmin() || isCoach())
                    <x-tables.player-performance-review-event :route="route('training-schedules.player-performance-review', $schedule->hash)"/>
                @elseif(isPlayer())
                    @if(!$playerPerformanceReview)
                        <x-warning-alert text="You haven't get any performance review from your coach for this training session"/>
                    @else
                        <x-player-event-performance-review :review="$playerPerformanceReview"/>
                    @endif
                @endif
            </div>
        </div>
    </div>

@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            processWithConfirmation(
                '.delete',
                "{{ route('training-schedules.destroy', ['training' => ':id']) }}",
                "{{ route('training-schedules.index') }}",
                'DELETE',
                "Are you sure to delete this training?",
                "Something went wrong when deleting this training!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.cancelBtn',
                "{{ route('training-schedules.cancel', $schedule->hash) }}",
                "{{ route('training-schedules.show', $schedule->hash) }}",
                'PATCH',
                "Are you sure to cancel this training?",
                "Something went wrong when cancelling this training!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.scheduled-btn',
                "{{ route('training-schedules.scheduled', $schedule->hash) }}",
                "{{ route('training-schedules.show', $schedule->hash) }}",
                'PATCH',
                "Are you sure to set this training to scheduled?",
                "Something went wrong when set this training to scheduled!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
