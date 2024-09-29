@extends('layouts.master')
@section('title')
    {{ $data->trainingTitle }} Training Videos
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div style="background: url({{ Storage::url($data->previewPhoto) }});
                    background-repeat: no-repeat;
                    background-size: cover;
                    overflow: hidden;
                    background-position: center center;">
                <div class="mdk-box__content">
                    <div class="hero py-64pt text-center text-sm-left" style="background-color: rgba(239, 37, 52, 0.8)">
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
                <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add lesson</a>
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
        </div>
    @endsection
    @push('addon-script')
        <script>
            $(document).ready(function() {
                const lessonsTable = $('#lessonsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! route('training-videos.lessons-index', $data->id) !!}',
                    },
                    columns: [
                        { data: 'DT_Rowindex', name: 'DT_Rowindex', orderable: false, searchable: false},
                        {data: 'title', name: 'title'},
                        {data: 'totalMinutes', name: 'totalMinutes'},
                        {data: 'description', name: 'description'},
                        {data: 'status', name: 'status'},
                        {data: 'created_date', name: 'created_date'},
                        {data: 'last_updated', name: 'last_updated'},
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
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
                                success: function(response) {
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
