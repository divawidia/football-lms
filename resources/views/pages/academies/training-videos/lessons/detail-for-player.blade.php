@extends('layouts.master')
@section('title')
    {{ $data->lessonTitle }} Lesson
@endsection
@section('page-title')
    @yield('title')
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
            <nav class="course-nav">
                @php($i = 1)
                @foreach($trainingVideo->lessons as $lesson)
                    <a class="@if($data->id == $lesson->id) bg-accent-2 @endif" data-toggle="tooltip"
                       data-placement="bottom"
                       title="{{ $lesson->lessonTitle }}"
                       href="{{ route('training-videos.show-player-lesson', ['trainingVideo' => $trainingVideo->id, 'lesson' => $lesson->id]) }}">
                        @if($lesson->players()->where('playerId', $loggedPlayerUser->id)->first(['player_lesson.completionStatus'])->completionStatus)
                            <span class="material-icons text-success">check_circle</span>
                        @else
                            {{ $i }}
                        @endif
                        @php($i++)
                    </a>
                @endforeach
            </nav>
            <div class="d-flex flex-row align-items-center">
                <h5 class="text-white mb-0">Course Progress : </h5>
                <div class="flex mx-4" style="max-width: 100%">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-accent-2" role="progressbar" style="width: {{ $playerCompletionProgress }}%;"></div>
                    </div>
                </div>
                <h5 class="text-white mb-0">{{ $playerCompletionProgress }}%</h5>
            </div>
            <div class="js-player bg-primary embed-responsive embed-responsive-16by9 my-4">
                <div class="player embed-responsive-item">
                    <div id="video-player" class="player__content">
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-end mb-16pt">
                <h1 class="text-white flex m-0">{{ $data->lessonTitle }}</h1>
            </div>

            <p class="hero__lead measure-hero-lead text-white-50 mb-24pt">{!! $data->description !!}</p>
                <div class="btn-toolbar" role="toolbar">
                    @if($trainingVideo->lessons()->first()->id != $data->id)
                        <a class="btn btn-sm btn-white" href="{{ route('training-videos.show-player-lesson', ['trainingVideo' => $trainingVideo->id, 'lesson' => $previousId]) }}" id="previousBtn">
                            <span class="material-icons icon-24pt mr-2">chevron_left</span>
                            Previous Lesson
                        </a>
                    @endif
                    @if($trainingVideo->lessons()->latest()->first()->id != $data->id)
                        <a class="btn btn-sm btn-white ml-auto" href="" id="nextBtn">
                            Next Lesson
                            <span class="material-icons icon-24pt ml-2">chevron_right</span>
                        </a>
                    @endif
                </div>
        </div>
    </div>
    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom-2 navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">

                        <i class="material-icons text-muted icon--left">visibility</i>
                        @if($lessonCompletionStatus == '1')
                            Status : <span class="badge badge-pill badge-success ml-1">Completed</span>
                        @else
                            Status : <span class="badge badge-pill badge-danger ml-1">Not Completed</span>
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
        <div class="border-left page-section pl-32pt">
            @php($i = 1)
            @foreach($trainingVideo->lessons as $lesson)
                <div class="d-flex align-items-center page-num-container">
                    <div class="page-num">{{ $i }}</div>
                    <h4>{{ $lesson->lessonTitle }}</h4>
                </div>

                <p class="text-70 mb-24pt">{{ $lesson->description }}</p>

                <div class="card mb-32pt mb-lg-64pt @if($data->id == $lesson->id) bg-accent-2 text-white @endif">
                    <a class="accordion__toggle" href="{{ route('training-videos.show-player-lesson', ['trainingVideo' => $trainingVideo->id, 'lesson' => $lesson->id]) }}">
                        @if($lesson->players()->where('playerId', $loggedPlayerUser->id)->first(['player_lesson.completionStatus'])->completionStatus == '1')
                            <span class="accordion__toggle-icon material-icons text-success">check_circle</span>
                            <strong class="card-title mx-4">Completed</strong>
                        @else
                            <span class="accordion__toggle-icon material-icons">play_circle_outline</span>
                            <strong class="card-title mx-4">Take Lesson</strong>
                        @endif
                        <span class="text-muted ml-auto">{{ secondToMinute($lesson->totalDuration) }}</span>
                    </a>
                </div>
                @php($i++)
            @endforeach
        </div>
    </div>

@endsection

@push('addon-script')
    <script>
        // This code loads the IFrame Player API code asynchronously.
        const tag = document.createElement('script');

        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        function markVideoAsComplete() {
            $.ajax({
                url: '{{ url()->route('training-videos.mark-as-complete', ['trainingVideo' => $trainingVideo->id, 'lesson' => $data->id]) }}', // Route to handle video completion
                method: 'POST',
                data: {
                    playerId: '{{ $loggedPlayerUser->id }}', // Pass the video ID to backend
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response.message);
                    @if($nextId != null)
                        window.location.href = '{{ route('training-videos.show-player-lesson', ['trainingVideo' => $trainingVideo->id, 'lesson' => $nextId]) }}'
                    @else
                        window.location.href = '{{ route('training-videos.completed', ['trainingVideo' => $trainingVideo->id]) }}'
                    @endif
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        $('#nextBtn').on('click', function (e){
            e.preventDefault();
            markVideoAsComplete();
        });


        //    This function creates an <iframe> (and YouTube player)
        //    after the API code downloads.
        let player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('video-player', {
                height: '390',
                width: '100%',
                videoId: '{{ $data->videoId }}',
                playerVars: {
                    'playsinline': 1
                },
                events: {
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerStateChange(event) {
            if (event.data === YT.PlayerState.ENDED) {
                markVideoAsComplete();
            }
        }
    </script>
@endpush
