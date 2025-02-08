@extends('layouts.master')
@section('title')
    Training Course
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.training-courses.create-training-course-modal/>
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container d-flex flex-column">
            <h2 class="mb-2">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        @if(isAllAdmin())
            <x-buttons.basic-button icon="add" text="Add New Training Course" id="addTrainingVideo" color="primary" iconColor="" margin="mb-3"/>
        @elseif(isPlayer())
            <div class="page-separator">
                <div class="page-separator__text">Assigned Training Videos</div>
            </div>
        @endif

        @if(count($data)==0)
            @if(isAllAdmin() || isCoach())
                <x-warning-alert text="You haven't created any training course yet"/>
            @elseif(isPlayer())
                <x-warning-alert text="You haven't assigned to any training course yet"/>
            @endif
        @else
            <div class="row">
                @foreach($data as $training)
                    <div class="col-sm-6">
                        <div class="card card-sm card--elevated p-relative o-hidden">
                            <a href="{{ route('training-videos.show', $training->hash) }}">
                                <img class="img-index-page" src="{{ Storage::url($training->previewPhoto) }}" alt="training-preview">
                            </a>
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex">
                                        <a class="card-title mb-4pt"
                                           href="{{ route('training-videos.show', $training->hash) }}">{{$training->trainingTitle}}</a>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="d-flex align-items-center mr-2">
                                        <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                        <p class="flex text-50 lh-1 mb-0"><small>{{ secondToMinute($training->lessons()->sum('totalDuration')) }}</small></p>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                        <p class="flex text-50 lh-1 mb-0"><small>{{ $training->lessons()->count() }}
                                                lessons</small></p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                        <p class="flex text-50 lh-1 mb-0"><small>{{ $training->level }}</small></p>
                                    </div>
                                    @if(isAllAdmin() || isCoach())
                                        @if($training->status == '1')
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-pill badge-success ml-1">Published</span>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-pill badge-danger ml-1">Unpublished</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
