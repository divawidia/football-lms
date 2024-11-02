@extends('layouts.master')
@section('title')
    {{ $data->lessonTitle }} Lesson
@endsection
@section('page-title')
    @yield('title')
@endsection


@section('modal')
    @include('pages.academies.training-videos.lessons.form-modal.edit')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('training-videos.show', $data->trainingVideoId) }}" class="nav-link text-70"><i
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

            @if(isAllAdmin() || isCoach())
                <div class="btn-toolbar" role="toolbar">
                    <button type="button" class="btn btn-sm btn-white editLesson" id="{{ $data->id }}">
                        <span class="material-icons mr-2">edit</span>
                        Edit Lesson
                    </button>
                    @if($data->status == "1")
                        <form action="{{ route('training-videos.lessons-unpublish', ['trainingVideo'=>$data->trainingVideoId, 'lesson'=>$data->id]) }}"
                              method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="btn btn-sm btn-white mx-2">
                                <span class="material-icons mr-2">block</span>
                                Unpublish Lesson
                            </button>
                        </form>
                    @else
                        <form action="{{ route('training-videos.lessons-publish', ['trainingVideo'=>$data->trainingVideoId, 'lesson'=>$data->id]) }}"
                              method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="btn btn-sm btn-white mx-2">
                                <span class="material-icons mr-2">check_circle</span>
                                Publish Lesson
                            </button>
                        </form>
                    @endif
                    <button type="button" class="btn btn-sm btn-white deleteLesson" id="{{ $data->id }}">
                        <span class="material-icons mr-2">delete</span>
                        Delete Lesson
                    </button>
                </div>
            @elseif(isPlayer())
                <div class="btn-toolbar" role="toolbar">
                    @if($trainingVideo->lessons()->first()->id != $data->id)
                        <a class="btn btn-sm btn-white" href="{{ route('training-videos.lessons-show', ['trainingVideo' => $trainingVideo->id, 'lesson' => $previousId]) }}" id="{{ $data->id }}">
                            <span class="material-icons icon-24pt mr-2">chevron_left</span>
                            Previous Lesson
                        </a>
                    @endif
                    @if($trainingVideo->lessons()->latest()->first()->id != $data->id)
                        <a class="btn btn-sm btn-white ml-auto" href="{{ route('training-videos.lessons-show', ['trainingVideo' => $trainingVideo->id, 'lesson' => $nextId]) }}" id="{{ $data->id }}">
                            Next Lesson
                            <span class="material-icons icon-24pt ml-2">chevron_right</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom-2 navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">
                    @if(isAllAdmin() || isCoach())
                        <i class="material-icons text-muted icon--left">visibility</i>
                        @if($data->status == '1')
                            Status : <span class="badge badge-pill badge-success ml-1">Published</span>
                        @else
                            Status : <span class="badge badge-pill badge-danger ml-1">Unpublished</span>
                        @endif
                    @elseif(isPlayer())
                        <i class="material-icons text-muted icon--left">visibility</i>
                        @dd($data->players()->where('playerId', $loggedPlayerUser->id)->first()->pivot->completionStatus == '0')
                        @if($data->status == '1')
                            Status : <span class="badge badge-pill badge-success ml-1">Completed</span>
                        @else
                            Status : <span class="badge badge-pill badge-danger ml-1">Not Completed</span>
                        @endif
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

    @if(isAllAdmin() || isCoach())
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
    @endif

@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            @if(isPlayer())

                function onYouTubeIframeAPIReady() {
                    new YT.Player('video-player', {
                        videoId: '{{ $data->videoId }}', // Replace with your YouTube video ID
                        events: {
                            'onStateChange': onPlayerStateChange
                        }
                    });
                }

                function onPlayerStateChange(event) {
                    // Video has ended
                    if (event.data === YT.PlayerState.ENDED) {
                        markVideoAsComplete();
                    }
                }

                function markVideoAsComplete() {
                    $.ajax({
                        url: '{{ url()->route('training-videos.mark-as-complete', ['trainingVideo' => $trainingVideo->id, 'lesson' => $data->id]) }}', // Route to handle video completion
                        method: 'POST',
                        data: {
                            userId: 'YOUR_VIDEO_ID', // Pass the video ID to backend
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log(response.message);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            @endif


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

            // show edit form modal when edit lesson button clicked
            body.on('click', '.editLesson', function (e) {
                e.preventDefault();
                const id = $(this).attr('id');
                $.ajax({
                    url: "{{ route('training-videos.lessons-edit', ['trainingVideo'=>$data->trainingVideoId, 'lesson' => ':id']) }}".replace(':id', id),
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
                            title: "Something went wrong when showing form!",
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
                    url: "{{ route('training-videos.lessons-update', ['trainingVideo'=>$data->trainingVideoId, 'lesson' => ':id']) }}".replace(':id', id),
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
                                window.location.reload();
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
        });
    </script>
@endpush
