@extends('layouts.master')
@section('title')
    {{ $data->trainingTitle }} Training Course
@endsection
@section('page-title')
    @yield('title')
@endsection


@section('modal')
    @if(isAllAdmin()||isCoach())
        <x-modal.training-courses.add-training-course-lesson-modal
            :routeStore="route('training-videos.lessons-store', $data->hash)"/>
        <x-modal.training-courses.edit-training-course-lesson-modal :trainingVideo="$data"/>
        <x-modal.training-courses.edit-training-course-modal :routeEdit="route('training-videos.edit', $data->id)"
                                                             :routeUpdate="route('training-videos.update', $data->hash)"/>
        <x-modal.teams.add-players-to-team
            :route="route('training-videos.update-player', ['trainingVideo' => $data->hash])"
            :players="$player"/>
    @endif

@endsection

@section('content')
    <div style="background: url({{ Storage::url($data->previewPhoto) }});
                    background-repeat: no-repeat;
                    background-size: cover;
                    overflow: hidden;
                    background-position: center center;">
        <div class="mdk-box__content">
            <nav class="navbar navbar-light border-bottom border-top px-0">
                <div class="container">
                    <ul class="nav navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('training-videos.index') }}" class="nav-link text-70">
                                <i class="material-icons icon--left">keyboard_backspace</i>
                                Back to Training Course Lists
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="hero py-64pt text-center text-sm-left" style="background-color: rgba(239, 37, 52, 0.8)">
                <div class="container">
                    <h1 class="text-white mt-3">{{ $data->trainingTitle }}</h1>
                    <div class="lead text-white-50 measure-hero-lead mb-24pt">
                        {!! $data->description !!}
                    </div>

                    {{-- progress bar for player page --}}
                    @if(isPlayer())
                        <div class="d-flex flex-row align-items-center">
                            <h5 class="text-white mb-0">Course Progress : </h5>
                            <div class="flex mx-4" style="max-width: 100%">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-accent-2" role="progressbar"
                                         style="width: {{ $playerCompletionProgress }}%;"></div>
                                </div>
                            </div>
                            <h5 class="text-white mb-0">{{ $playerCompletionProgress }}%</h5>
                        </div>
                        @if($playerCompletionProgress < 100)
                            <a href="{{ route('training-videos.show-player-lesson', ['trainingVideo' => $data->hash, 'lesson' => $uncompletePlayerTrainingLesson->hash]) }}"
                               id="resumeTraining" class="btn btn-sm btn-primary mt-4">
                                <span class="material-icons mr-2">play_arrow</span>
                                Resume Training Course
                            </a>
                        @endif
                    @endif
                    @if(isAllAdmin())
                        <div class="btn-toolbar" role="toolbar">
                            <x-buttons.basic-button icon="edit" text="Edit Training Course" id="editTrainingVideo"
                                                    color="white" iconColor=""/>
                            @if($data->status == "1")
                                <x-buttons.basic-button icon="block" text="Unpublish Training Course"
                                                        additionalClass="unpublishTraining" color="white"
                                                        iconColor="danger" margin="mx-3"/>
                            @else
                                <x-buttons.basic-button icon="check_circle" text="Publish Training Course"
                                                        additionalClass="publishTraining" color="white"
                                                        iconColor="success" margin="mx-3"/>
                            @endif
                            <x-buttons.basic-button icon="delete" text="Delete Training Course"
                                                    additionalClass="delete-training" :id="$data->id" color="white"
                                                    iconColor="danger"/>
                        </div>
                    @endif
                </div>
            </div>
            <div
                class="navbar navbar-expand-sm navbar-light bg-white border-bottom-2 navbar-list p-0 m-0 align-items-center">
                <div class="container">
                    <ul class="nav navbar-nav flex align-items-sm-center">
                        @if(isAllAdmin() || isCoach())
                            <li class="nav-item navbar-list__item">
                                <div class="media align-items-center">
                                <span class="media-left mr-16pt">
                                    <img src="{{ Storage::url($data->user->foto) }}"
                                         width="40"
                                         alt="avatar"
                                         class="rounded-circle">
                                </span>
                                    <div class="media-body">
                                        <a class="card-title m-0"
                                           href="{{ route('admin-managements.show', $data->user->id) }}">
                                            {{ $data->user->firstName }} {{ $data->user->lastName }}
                                        </a>
                                        <p class="text-50 lh-1 mb-0">{{ $data->user->roles[0]->name }}</p>
                                    </div>
                                </div>
                            </li>
                        @endif

                        <li class="nav-item navbar-list__item">
                            <i class="material-icons text-muted icon--left">schedule</i>
                            {{ $totalDuration }}
                        </li>
                        <li class="nav-item navbar-list__item">
                            <span class="material-icons text-muted icon--left">play_circle_outline</span>
                            {{ $data->lessons()->count() }} Lesson
                        </li>
                        <li class="nav-item navbar-list__item">
                            <i class="material-icons text-muted icon--left">assessment</i>
                            {{ $data->level }}
                        </li>
                        @if(isAllAdmin() || isCoach())
                            <li class="nav-item navbar-list__item">
                                <i class="material-icons text-muted icon--left">visibility</i>
                                @if($data->status == '1')
                                    Status : <span class="badge badge-pill badge-success ml-1">Published</span>
                                @else
                                    Status : <span class="badge badge-pill badge-danger ml-1">Unpublished</span>
                                @endif
                            </li>
                        @endif
                        <li class="nav-item navbar-list__item">
                            <i class="material-icons text-muted icon--left">date_range</i>
                            Last updated : {{ date('M d, Y ~ H:i', strtotime($data->updated_at)) }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="container page__container page-section">
        @if(isAllAdmin() || isCoach())
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>

            <div class="row mb-3">
                @include('components.cards.stats-card', ['title' => 'Total Assigned Players','data' => $totalPLayers, 'dataThisMonth' => null])
                @include('components.cards.stats-card', ['title' => 'Players Completed','data' => $totalCompleted, 'dataThisMonth' => null])
                @include('components.cards.stats-card', ['title' => 'Players on progress','data' => $totalOnProgress, 'dataThisMonth' => null])
            </div>

            {{--    Lessons    --}}
            <div class="page-separator">
                <div class="page-separator__text">Lesson(s)</div>
                @if(isAllAdmin())
                    <x-buttons.basic-button icon="add" text="Add Training Video lesson" id="addLesson" color="primary"
                                            iconColor="" margin="ml-auto"/>
                @endif
            </div>
            <div class="card">
                <div class="card-body">
                    <x-table :headers="['#', 'Title', 'Lesson Length', 'Description', 'Publish Status', 'Created At', 'Last Updated', 'Action']" tableId="lessonsTable"/>
                </div>
            </div>

            {{--    Assigned Player    --}}
            <div class="page-separator">
                <div class="page-separator__text">Assigned Player(s)</div>
                @if(isAllAdmin())
                    <x-buttons.basic-button icon="add" text="Assign Player into training course" id="add-players"
                                            color="primary" iconColor="" margin="ml-auto"/>
                @endif
            </div>
            <div class="card">
                <div class="card-body">
                    <x-table
                        :headers="['#', 'Name', 'Progress', 'Completion Status', 'Assigned At', 'Completed At', 'Action']"
                        tableId="playersTable"/>
                </div>
            </div>

        @elseif(isPlayer())
            <div class="border-left page-section pl-32pt">
                @php($i = 1)
                @foreach($data->lessons()->where('training_video_lessons.status', '1')->get() as $lesson)
                    <div class="d-flex align-items-center page-num-container">
                        <div class="page-num">{{ $i }}</div>
                        <h4>{{ $lesson->lessonTitle }}</h4>
                    </div>

                    <p class="text-70 mb-24pt">{{ $lesson->description }}</p>

                    <div class="card mb-32pt mb-lg-64pt">
                        <a class="accordion__toggle"
                           href="{{ route('training-videos.show-player-lesson', ['trainingVideo' => $data->hash, 'lesson' => $lesson->hash]) }}">
                            @if($lesson->players()->where('playerId', $player->id)->first(['player_lesson.completionStatus'])->completionStatus == '1')
                                <span class="accordion__toggle-icon material-icons text-success">check_circle</span>
                                <strong class="card-title mx-4">Completed</strong>
                            @elseif($lesson->players()->where('playerId', $player->id)->first(['player_lesson.completionStatus'])->completionStatus == '0')
                                <span class="accordion__toggle-icon material-icons">play_circle_outline</span>
                                <strong class="card-title mx-4">Take Lesson</strong>
                            @endif
                            <span class="text-muted ml-auto">{{ secondToMinute($lesson->totalDuration) }}</span>
                        </a>
                    </div>
                    @php($i++)
                @endforeach
            </div>

        @endif
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#lessonsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('training-videos.lessons-index', $data->id) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'title', name: 'title'},
                    {data: 'totalDuration', name: 'totalDuration'},
                    {data: 'description', name: 'description'},
                    {data: 'status', name: 'status'},
                    {data: 'created_date', name: 'created_date'},
                    {data: 'last_updated', name: 'last_updated'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $('#playersTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('training-videos.players', $data->id) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'progress', name: 'progress'},
                    {data: 'status', name: 'status'},
                    {data: 'assignedAt', name: 'assignedAt'},
                    {data: 'completedAt', name: 'completedAt'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            @if(isAllAdmin())
                // unpublish training course
                processWithConfirmation(
                    '.unpublishTraining',
                    "{{ route('training-videos.unpublish', $data->hash) }}",
                    "{{ route('training-videos.show', $data->hash) }}",
                    'PATCH',
                    "Are you sure to unpublish this training course?",
                    "Something went wrong when unpublishing training course!",
                    "{{ csrf_token() }}"
                );

                // delete training course
                processWithConfirmation(
                    '.delete-training',
                    "{{ route('training-videos.destroy', $data->hash) }}",
                    "{{ route('training-videos.index') }}",
                    'DELETE',
                    "Are you sure to delete this training course?",
                    "Something went wrong when deleting training course!",
                    "{{ csrf_token() }}"
                );

                // publish training course
                processWithConfirmation(
                    '.publishTraining',
                    "{{ route('training-videos.publish', $data->hash) }}",
                    "{{ route('training-videos.show', $data->hash) }}",
                    'PATCH',
                    "Are you sure to publish this training course?",
                    "Something went wrong when publishing training course!",
                    "{{ csrf_token() }}"
                );

                // delete lesson
                processWithConfirmation(
                    '.deleteLesson',
                    "{{ route('training-videos.lessons-destroy', ['trainingVideo'=>$data->hash, 'lesson' => ':id']) }}",
                    "{{ route('training-videos.show', $data->hash) }}",
                    'DELETE',
                    "Are you sure to delete this lesson?",
                    "Something went wrong when deleting lesson!",
                    "{{ csrf_token() }}"
                );

                // remove player
                processWithConfirmation(
                    '.deletePlayer',
                    "{{ route('training-videos.remove-player', ['trainingVideo'=>$data->hash, 'player' => ':id']) }}",
                    "{{ route('training-videos.show', $data->hash) }}",
                    'DELETE',
                    "Are you sure to remove this player from training course?",
                    "Something went wrong when removing the player from training course!",
                    "{{ csrf_token() }}"
                );

                // unpublish lesson
                processWithConfirmation(
                    '.unpublish-lesson',
                    "{{ route('training-videos.lessons-unpublish', ['trainingVideo'=>$data->hash, 'lesson'=>':id']) }}",
                    null,
                    'PATCH',
                    "Are you sure to unpublish this lesson?",
                    "Something went wrong when unpublishing lesson!",
                    "{{ csrf_token() }}"
                );

                // publish lesson
                processWithConfirmation(
                    '.publish-lesson',
                    "{{ route('training-videos.lessons-publish', ['trainingVideo'=>$data->hash, 'lesson'=>':id']) }}",
                    null,
                    'PATCH',
                    "Are you sure to publish this lesson?",
                    "Something went wrong when publishing lesson!",
                    "{{ csrf_token() }}"
                );
            @endif
        });
    </script>
@endpush
