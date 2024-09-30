@extends('layouts.master')
@section('title')
    {{ $data->trainingTitle }} Training Videos
@endsection
@section('page-title')
    @yield('title')
@endsection


@section('modal')
    <!-- Modal add lesson -->
    <div class="modal fade" id="addLessonModal" tabindex="-1" aria-labelledby="addLessonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formAddLessonModal">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add lesson to {{ $data->trainingTitle }} Training</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="add_lessonTitle">Lesson Title</label>
                            <small class="text-danger">*</small>
                            <input type="text"
                                   id="add_lessonTitle"
                                   name="lessonTitle"
                                   class="form-control"
                                   placeholder="Input lesson's title ..."
                                    required>
                            <span class="invalid-feedback lessonTitle_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editor">Description</label>
                            <small class="text-sm">(Optional)</small>
                            <div class="editor-container editor-container_classic-editor editor-container_include-style" id="editor-container">
                                <div class="editor-container__editor">
                                <textarea class="form-control"
                                          id="editor"
                                          name="description">
                                </textarea>
                                </div>
                            </div>
                            <span class="invalid-feedback description_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="add_lessonVideoURL">Lesson Video URL</label>
                            <small class="text-danger">*</small>
                            <input type="text"
                                   id="add_lessonVideoURL"
                                   name="lessonVideoURL"
                                   class="form-control"
                                   placeholder="Input youtube video url (only from youtube!) ..."
                                   required>
                            <span class="invalid-feedback lessonVideoURL_error" role="alert">
                                <strong></strong>
                            </span>
                            <div id="preview-container">
                                <div id="player"></div>
                            </div>
                        </div>
                        <input type="hidden" id="totalDuration" name="totalDuration">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                                <li class="breadcrumb-item"><a href="{{ route('training-videos.index') }}">Training Videos</a></li>
                                <li class="breadcrumb-item active">
                                    @yield('title')
                                </li>
                            </ol>
                            <h1 class="text-white mt-3">{{ $data->trainingTitle }}</h1>
                            <div class="lead text-white-50 measure-hero-lead mb-24pt">
                                {!! $data->description !!}
                            </div>
                            <a href="" class="btn btn-sm btn-white">
                                <span class="material-icons mr-2">edit</span>
                                Edit Training
                            </a>
                            <a href="" class="btn btn-sm btn-white my-2 mx-2">
                                <span class="material-icons mr-2">block</span>
                                Unpublished Training
                            </a>
                            <a href="" class="btn btn-sm btn-white">
                                <span class="material-icons mr-2">delete</span>
                                Delete Training
                            </a>
                        </div>
                    </div>
                    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom-2 navbar-list p-0 m-0 align-items-center">
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
                                            <a class="card-title m-0" href="{{ route('admin-managements.show', $data->user->id) }}">
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
                <a href="" id="addLesson" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add lesson</a>
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
                <a href="" id="addPlayer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add Player</a>
            </div>
            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="lessonsTable">
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
            $(document).ready(function() {
                $('#lessonsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! route('training-videos.lessons-index', $data->id) !!}',
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
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

                $('#addLesson').on('click', function (e) {
                    e.preventDefault();
                    $('#addLessonModal').modal('show');
                });

                // This code loads the IFrame Player API code asynchronously.
                var tag = document.createElement('script');

                tag.src = "https://www.youtube.com/iframe_api";
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                let player;

                // Load the YouTube Iframe API and create a player
                function onYouTubeIframeAPIReady(videoId) {
                    player = new YT.Player('player', {
                        height: '250',
                        width: '100%',
                        videoId: videoId,
                        playerVars: {
                            'playsinline': 1
                        },
                        events: {
                            'onReady': onPlayerReady
                        }
                    });
                }

                // When the player is ready, get the video duration and show video
                function onPlayerReady(event) {
                    const duration = player.getDuration(); // Get the duration in seconds
                    // event.target.playVideo();
                    $('#totalDuration').val(duration);
                }

                // Extract video ID from the URL
                function extractVideoID(url) {
                    const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
                    const match = url.match(regex);
                    return (match && match[1]) ? match[1] : null;
                }

                // Handle form submission
                $('#add_lessonVideoURL').on('change', function (e) {
                    e.preventDefault(); // Prevent form submission

                    // Get the YouTube URL from the input
                    const url = document.getElementById('add_lessonVideoURL').value;

                    // Extract the video ID
                    const videoID = extractVideoID(url);

                    if (videoID) {
                        onYouTubeIframeAPIReady(videoID);
                    } else {
                        alert('Invalid YouTube URL');
                    }
                });

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
                        error: function (xhr) {
                            const response = JSON.parse(xhr.responseText);
                            console.log(response);
                            $.each(response.errors, function (key, val) {
                                $('span.' + key + '_error').text(val[0]);
                                $("#add_" + key).addClass('is-invalid');
                            });
                        }
                    });
                });

                $('body').on('click', '.delete', function() {
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
                                url: "{{ route('competition-managements.destroy', ['competition' => ':id']) }}".replace(':id', id),
                                type: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function() {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Competition successfully deleted!",
                                    });
                                    datatable.ajax.reload();
                                },
                                error: function(error) {
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
            });
        </script>
    @endpush
