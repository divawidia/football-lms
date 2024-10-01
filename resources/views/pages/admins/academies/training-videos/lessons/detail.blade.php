@extends('layouts.master')
@section('title')
    {{ $data->lessonTitle }} Lesson
@endsection
@section('page-title')
    @yield('title')
@endsection


@section('modal')
    @include('pages.admins.academies.training-videos.lessons.form-modal.create')
    @include('pages.admins.academies.training-videos.lessons.form-modal.edit')
    @include('pages.admins.academies.training-videos.form-modal.edit')
@endsection

@section('content')
    <div class="bg-primary pb-lg-64pt py-32pt">
        <div class="container page__container">
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('training-videos.index') }}">Training Videos</a>
                <li class="breadcrumb-item"><a href="{{ route('training-videos.show', $data->trainingVideoId) }}">{{ $data->trainingVideo->trainingTitle }}</a>
                </li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>

            <div class="js-player bg-primary embed-responsive embed-responsive-16by9 my-4">
                <div class="player embed-responsive-item">
                    <div class="player__content">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $data->videoId }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-end mb-16pt">
                <h1 class="text-white flex m-0">{{ $data->lessonTitle }}</h1>
            </div>

            <p class="hero__lead measure-hero-lead text-white-50 mb-24pt">{!! $data->description !!}</p>

            <div class="btn-toolbar" role="toolbar">
                <a href="" id="editTrainingVideo" class="btn btn-sm btn-white">
                    <span class="material-icons mr-2">edit</span>
                    Edit Lesson
                </a>
                @if($data->status == "1")
                    <form action="{{ route('training-videos.lessons-unpublish', ['trainingVideo'=>$data->trainingVideoId, 'lesson'=>$data->id]) }}" method="POST">
                        @method("PATCH")
                        @csrf
                        <button type="submit" class="btn btn-sm btn-white mx-2">
                            <span class="material-icons mr-2">block</span>
                            Unpublish Lesson
                        </button>
                    </form>
                @else
                    <form action="{{ route('training-videos.lessons-publish', ['trainingVideo'=>$data->trainingVideoId, 'lesson'=>$data->id]) }}" method="POST">
                        @method("PATCH")
                        @csrf
                        <button type="submit" class="btn btn-sm btn-white mx-2">
                            <span class="material-icons mr-2">check_circle</span>
                            Publish Lesson
                        </button>
                    </form>
                @endif
                <button type="button" class="btn btn-sm btn-white delete-lesson" id="{{ $data->id }}">
                    <span class="material-icons mr-2">delete</span>
                    Delete Lesson
                </button>
            </div>
        </div>
    </div>
    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom-2 navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">visibility</i>
                    @if($data->status == '1')
                        Status : <span class="badge badge-pill badge-success ml-1">Publishes</span>
                    @else
                        Status : <span class="badge badge-pill badge-danger ml-1">Unpublished</span>
                    @endif
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

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
{{--            <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>--}}
        </div>

        <div class="row mb-3">
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data->players()->count() }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Total Assigned Players</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data->players()->where('completionStatus', '1')->count() }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Players Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data->players()->where('completionStatus', '0')->count() }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">Players on progress</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--    Assigned Player    --}}
        <div class="page-separator">
            <div class="page-separator__text">Assigned Player(s)</div>
{{--            <a href="{{ route('training-videos.assign-player', ['trainingVideo' => $data->id]) }}" class="btn btn-primary btn-sm ml-auto">--}}
{{--                <span class="material-icons mr-2">add</span>--}}
{{--                Add Player--}}
{{--            </a>--}}
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="playersTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Completion Status</th>
                            <th>Assigned At</th>
                            <th>Completed At</th>
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
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            const body = $('body');

            const playersTable = $('#playersTable').DataTable({
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
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            // This code loads the IFrame Player API code asynchronously.
            const tag = document.createElement('script');

            tag.src = "https://www.youtube.com/iframe_api";
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

            let player;

            // Load the YouTube Iframe API and create a player
            function onYouTubeIframeAPIReady() {
                player = new YT.Player('player', {
                    height: '250',
                    width: '100%',
                    videoId: '{{ $data->videoId }}',
                    // playerVars: {
                    //     'playsinline': 1
                    // },
                    events: {
                        'onReady': onPlayerReady,
                    }
                });
            }

            // When the player is ready, get the video duration and show video
            function onPlayerReady(event) {
                // const duration = player.getDuration(); // Get the duration in seconds
                event.target.playVideo();
                // $('.totalDuration').val(duration);
            }

{{--            onYouTubeIframeAPIReady('{{ $data->videoId }}', 'player');--}}

            // show edit form modal when edit lesson button clicked
            body.on('click', '.editLesson', function (e) {
                e.preventDefault();
                const id = $(this).attr('id');
                $.ajax({
                    url: "{{ route('training-videos.lessons-edit', ['trainingVideo'=>$data->id, 'lesson' => ':id']) }}".replace(':id', id),
                    type: 'get',
                    success: function (res) {
                        $('#editLessonModal').modal('show');

                        $('#formEditLessonModal #lessonFormTitle').text('Edit Lesson ' + res.data.lessonTitle);
                        $('#formEditLessonModal #lessonId').val(res.data.id);
                        $('#edit-lessonTitle').val(res.data.lessonTitle);
                        $('#edit-description').text(res.data.description);
                        $('#edit-lessonVideoURL').val(res.data.lessonVideoURL);
                        $('#formEditLessonModal .totalDuration').val(res.data.totalDuration);
                        $('#formEditLessonModal #videoId').val(res.data.videoId);
                        $('#edit-player').remove();
                        $('#formEditLessonModal #preview-container').html('<div id="edit-player"></div>')
                        onYouTubeIframeAPIReady(res.data.videoId, 'edit-player');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // showYoutubePreview('#edit-lessonVideoURL', '#formEditLessonModal', '#edit-player');

            // update lesson data when form submitted
            $('#formEditLessonModal').on('submit', function (e) {
                e.preventDefault();
                const id = $('#lessonId').val();

                $.ajax({
                    url: "{{ route('training-videos.lessons-update', ['trainingVideo'=>$data->id, 'lesson' => ':id']) }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#editLessonModal').modal('hide');
                        Swal.fire({
                            title: 'Training lesson successfully updated!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                lessonsTable.ajax.reload(null, false);
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        console.log(response)
                        $.each(response.errors, function (key, val) {
                            $('span.' + key).text(val[0]);
                            $("#edit-" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when updating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // delete lesson data
            body.on('click', '.deleteLesson', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure to delete this lesson?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('training-videos.lessons-destroy', ['trainingVideo'=>$data->id, 'lesson' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Training lesson successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('training-videos.show', $data->id) }}";
                                    }
                                });
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: errorThrown,
                                });
                            }
                        });
                    }
                });
            });

            // delete lesson data
            body.on('click', '.deletePlayer', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure to remove this player?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('training-videos.remove-player', ['trainingVideo'=>$data->id, 'player' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (res) {
                                Swal.fire({
                                    title: 'Player successfully removed from training!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        playersTable.ajax.reload(null, false);
                                    }
                                });
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when removing player!",
                                    text: errorThrown,
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
