@extends('layouts.master')
@section('title')
    {{ $data->lessonTitle }} Lesson
@endsection
@section('page-title')
    @yield('title')
@endsection


@section('modal')
    <x-modal.training-courses.edit-training-course-lesson-modal :trainingVideo="$data->trainingVideo"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('training-videos.show', $data->trainingVideo->hash) }}"
                       class="nav-link text-70"><i
                                class="material-icons icon--left">keyboard_backspace</i> Back
                        to {{ $data->trainingVideo->trainingTitle }}</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="bg-primary pb-lg-64pt py-32pt">
        <div class="container page__container">
            <div class="js-player bg-primary embed-responsive embed-responsive-16by9 my-4">
                <div class="player embed-responsive-item">
                    <div id="video-player" class="player__content">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $data->videoId }}"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-end mb-16pt">
                <h1 class="text-white flex m-0">{{ $data->lessonTitle }}</h1>
            </div>

            <p class="hero__lead measure-hero-lead text-white-50 mb-24pt">{!! $data->description !!}</p>

            <div class="btn-toolbar" role="toolbar">
                @if(isAllAdmin())
                    <x-buttons.basic-button icon="edit" text="Edit Training Video Lesson" additionalClass="editLesson" :id="$data->id"
                                            color="white" iconColor=""/>
                    @if($data->status == "1")
                        <x-buttons.basic-button icon="block" text="Unpublish Training video lesson"
                                                additionalClass="unpublish-lesson" color="white"
                                                iconColor="danger" margin="mx-3"/>
                    @else
                        <x-buttons.basic-button icon="check_circle" text="publish Training video lesson"
                                                additionalClass="publish-lesson" color="white"
                                                iconColor="success" margin="mx-3"/>
                    @endif
                    <x-buttons.basic-button icon="delete" text="Delete Training video lesson"
                                            additionalClass="deleteLesson" :id="$data->id" color="white"
                                            iconColor="danger"/>
                @endif
            </div>
        </div>
    </div>
    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom-2 navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">visibility</i>
                    Status : <span class="badge badge-pill @if($data->status == '1') badge-success @else badge-danger @endif ml-1 text-capitalize">@if($data->status == '1') published @else unpublished @endif</span>
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">schedule</i>
                    {{ $totalDuration }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">date_range</i>
                    Created at : {{ date('M d, Y ~ H:i', strtotime($data->created_at)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">date_range</i>
                    Last updated : {{ date('M d, Y ~ H:i', strtotime($data->updated_at)) }}
                </li>
            </ul>
        </div>
    </div>

    @if(isAllAdmin() || isCoach())
        <div class="container">
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
            </div>

            <div class="row mb-3">
                @include('components.cards.stats-card', ['title' => 'Total Assigned Players','data' => $data->players()->count(), 'dataThisMonth' => null])
                @include('components.cards.stats-card', ['title' => 'Players Completed','data' => $data->players()->where('completionStatus', '1')->count(), 'dataThisMonth' => null])
                @include('components.cards.stats-card', ['title' => 'Players on progress','data' => $data->players()->where('completionStatus', '0')->count(), 'dataThisMonth' => null])
            </div>

            {{--    Assigned Player    --}}
            <div class="page-separator">
                <div class="page-separator__text">Assigned Player(s)</div>
            </div>
            <div class="card">
                <div class="card-body">
                    <x-table :headers="['#', 'Name', 'Completion Status', 'Assigned At', 'Completed At', 'Action']" tableId="playersTable"/>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#playersTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('training-videos.lessons-players', ['trainingVideo'=>$data->trainingVideoId, 'lesson'=>$data->id]) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'status', name: 'status'},
                    {data: 'assignedAt', name: 'assignedAt'},
                    {data: 'completedAt', name: 'completedAt'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            @if(isAllAdmin())
                // delete training course
                processWithConfirmation(
                    '.deleteLesson',
                    "{{ route('training-videos.lessons-destroy', ['trainingVideo'=>$data->trainingVideo->hash, 'lesson' => ':id']) }}",
                    "{{ route('training-videos.show', $data->trainingVideo->hash) }}",
                    'DELETE',
                    "Are you sure to delete this lesson?",
                    "Something went wrong when deleting lesson!",
                    "{{ csrf_token() }}"
                );

                // unpublish lesson
                processWithConfirmation(
                    '.unpublish-lesson',
                    "{{ route('training-videos.lessons-unpublish', ['trainingVideo'=>$data->trainingVideo->hash, 'lesson'=>$data->hash]) }}",
                    null,
                    'PATCH',
                    "Are you sure to unpublish this lesson?",
                    "Something went wrong when unpublishing lesson!",
                    "{{ csrf_token() }}"
                );

                // publish lesson
                processWithConfirmation(
                    '.publish-lesson',
                    "{{ route('training-videos.lessons-publish', ['trainingVideo'=>$data->trainingVideo->hash, 'lesson'=>$data->hash]) }}",
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
