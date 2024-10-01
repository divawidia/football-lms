@extends('layouts.master')
@section('title')
    {{ $data->trainingTitle }} Training Videos
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
    <div style="background: url({{ Storage::url($data->previewPhoto) }});
                    background-repeat: no-repeat;
                    background-size: cover;
                    overflow: hidden;
                    background-position: center center;">
        <div class="mdk-box__content">
            <div class="hero py-64pt text-center text-sm-left" style="background-color: rgba(239, 37, 52, 0.75)">
                <div class="container page__container">
                    <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('training-videos.index') }}">Training Videos</a>
                        </li>
                        <li class="breadcrumb-item active">
                            @yield('title')
                        </li>
                    </ol>
                    <h1 class="text-white mt-3">{{ $data->trainingTitle }}</h1>
                    <div class="lead text-white-50 measure-hero-lead mb-24pt">
                        {!! $data->description !!}
                    </div>
                    <div class="btn-toolbar" role="toolbar">
                        <a href="" id="editTrainingVideo" class="btn btn-sm btn-white">
                            <span class="material-icons mr-2">edit</span>
                            Edit Training
                        </a>
                        @if($data->status == "1")
                            <form action="{{ route('training-videos.unpublish', $data->id) }}" method="POST">
                                @method("PATCH")
                                @csrf
                                <button type="submit" class="btn btn-sm btn-white mx-2">
                                    <span class="material-icons mr-2">block</span>
                                    Unpublish Training
                                </button>
                            </form>
                        @else
                            <form action="{{ route('training-videos.publish', $data->id) }}" method="POST">
                                @method("PATCH")
                                @csrf
                                <button type="submit" class="btn btn-sm btn-white mx-2">
                                    <span class="material-icons mr-2">check_circle</span>
                                    Publish Training
                                </button>
                            </form>
                        @endif
                        <button type="button" class="btn btn-sm btn-white delete-training" id="{{ $data->id }}">
                            <span class="material-icons mr-2">delete</span>
                            Delete Training
                        </button>
                    </div>
                </div>
            </div>
            <div
                class="navbar navbar-expand-sm navbar-light bg-white border-bottom-2 navbar-list p-0 m-0 align-items-center">
                <div class="container page__container">
                    <ul class="nav navbar-nav flex align-items-sm-center">
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
                        <li class="nav-item navbar-list__item">
                            <i class="material-icons text-muted icon--left">schedule</i>
                            {{ $data->totalMinute }} m
                        </li>
                        <li class="nav-item navbar-list__item">
                            <span class="material-icons text-muted icon--left">play_circle_outline</span>
                            {{ $data->totalLesson }} Lesson
                        </li>
                        <li class="nav-item navbar-list__item">
                            <i class="material-icons text-muted icon--left">assessment</i>
                            {{ $data->level }}
                        </li>
                        <li class="nav-item navbar-list__item">
                            <i class="material-icons text-muted icon--left">visibility</i>
                            @if($data->status == '1')
                                Status : <span class="badge badge-pill badge-success ml-1">Publishes</span>
                            @else
                                Status : <span class="badge badge-pill badge-danger ml-1">Unpublished</span>
                            @endif
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
        </div>
    </div>
    <div class="container page__container page-section">
        {{--    Lessons    --}}
        <div class="page-separator">
            <div class="page-separator__text">Lesson(s)</div>
            <a href="" id="addLesson" class="btn btn-primary btn-sm ml-auto"><span
                    class="material-icons mr-2">add</span> Add lesson</a>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="lessonsTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Lesson Length</th>
                            <th>Description</th>
                            <th>Publish Status</th>
                            <th>Created At</th>
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

        {{--    Assigned Player    --}}
        <div class="page-separator">
            <div class="page-separator__text">Assigned Player(s)</div>
            <a href="{{ route('training-videos.assign-player', ['trainingVideo' => $data->id]) }}" class="btn btn-primary btn-sm ml-auto">
                <span class="material-icons mr-2">add</span>
                Add Player
            </a>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="playersTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Progress</th>
                            <th>Completion Status</th>
                            <th>Assigned At</th>
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

            const lessonsTable = $('#lessonsTable').DataTable({
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
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '20%'
                    },
                ]
            });

            const playersTable = $('#playersTable').DataTable({
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
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            $('#addLesson').on('click', function (e) {
                e.preventDefault();
                $('#addLessonModal').modal('show');
            });

            // show edit form modal when edit training button clicked
            $('#editTrainingVideo').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('training-videos.edit', $data->id) }}",
                    type: 'get',
                    success: function (res) {
                        $('#editTrainingVideoModal').modal('show');

                        document.getElementById('training-title').textContent = 'Edit Training ' + res.data.trainingTitle;
                        $('#trainingId').val(res.data.id);
                        $('#trainingTitle').val(res.data.trainingTitle);
                        $('#level').val(res.data.level);
                        $('#formEditTrainingVideoModal #description').text(res.data.description);
                        $('#preview').attr('src', "/storage/" + res.data.previewPhoto).addClass('d-block');
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

            // update training video data when form submitted
            $('#formEditTrainingVideoModal').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('training-videos.update', $data->id) }}",
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#editTrainingVideoModal').modal('hide');
                        Swal.fire({
                            title: 'Training video successfully updated!',
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
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        console.log(response)
                        $.each(response.errors, function (key, val) {
                            $('span.' + key).text(val[0]);
                            $("#" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when updating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // This code loads the IFrame Player API code asynchronously.
            const tag = document.createElement('script');

            tag.src = "https://www.youtube.com/iframe_api";
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

            let player;

            // Load the YouTube Iframe API and create a player
            function onYouTubeIframeAPIReady(videoId, playerId) {
                player = new YT.Player(playerId, {
                    height: '250',
                    width: '100%',
                    videoId: videoId,
                    // playerVars: {
                    //     'playsinline': 1
                    // },
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                    }
                });
            }

            // When the player is ready, get the video duration and show video
            function onPlayerReady(event) {
                const duration = player.getDuration(); // Get the duration in seconds
                // event.target.playVideo();
                $('.totalDuration').val(duration);
            }

            let done = false;

            function onPlayerStateChange(event) {
                if (event.data == YT.PlayerState.PLAYING && !done) {
                    setTimeout(stopVideo, 6000);
                    done = true;
                }
            }

            function stopVideo() {
                player.stopVideo();
            }

            // Extract video ID from the URL
            function extractVideoID(url) {
                const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
                const match = url.match(regex);
                return (match && match[1]) ? match[1] : null;
            }

            // Handle form submission
            function showYoutubePreview(inputId, formId, playerId) {
                $(inputId).on('change', function (e) {
                    e.preventDefault(); // Prevent form submission

                    let preview = $(formId + ' #preview-container');
                    let player = $(playerId);
                    let errorSpan = $(formId + ' span.lessonVideoURL');
                    let inputUrl = $(inputId);

                    errorSpan.text('');
                    inputUrl.removeClass('is-invalid');

                    if (player.attr('src') != undefined) {
                        player.remove();
                        preview.html('<div id="' + playerId.replace(/^#/, '') + '"></div>')
                    }

                    // Get the YouTube URL from the input
                    const url = inputUrl.val();

                    // Extract the video ID
                    const videoID = extractVideoID(url);
                    $(formId + ' #videoId').val(videoID);

                    if (videoID) {
                        onYouTubeIframeAPIReady(videoID, playerId.replace(/^#/, ''));
                    } else {
                        errorSpan.text('Invalid youtube URL');
                        inputUrl.addClass('is-invalid');
                    }
                });
            }

            showYoutubePreview('#lessonVideoURL', '#formAddLessonModal', '#create-player');

            // create schedule note data
            $('#formAddLessonModal').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('training-videos.lessons-store', $data->id) }}",
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#addLessonModal').modal('hide');
                        Swal.fire({
                            title: 'Training lesson successfully created!',
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
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        console.log(response);
                        $.each(response.errors, function (key, val) {
                            $('#formAddLessonModal span.' + key).text(val[0]);
                            $("#formAddLessonModal #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when updating data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            body.on('click', '.delete-training', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('training-videos.destroy', ['trainingVideo' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (res) {
                                Swal.fire({
                                    title: 'Training video successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('training-videos.index') }}";
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

            showYoutubePreview('#edit-lessonVideoURL', '#formEditLessonModal', '#edit-player');

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
                            success: function (res) {
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
        });
    </script>
@endpush
