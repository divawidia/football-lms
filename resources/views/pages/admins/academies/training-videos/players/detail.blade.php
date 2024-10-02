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
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-32pt mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-32pt mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $data->user->firstName }} {{ $data->user->lastName }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player - {{ $data->position->name }}</p>
            </div>
            <button class="btn btn-outline-white deletePlayer" type="button" id="{{ $data->id }}">
                <span class="material-icons ml-3">
                    cancel
                </span>
                Remove Player
            </button>
        </div>
    </div>

    <div class="navbar navbar-expand-sm navbar-light bg-white border-bottom-2 navbar-list p-0 m-0 align-items-center">
        <div class="container page__container">
            <ul class="nav navbar-nav flex align-items-sm-center">
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">visibility</i>
                    @if($item->pivot->status == '1')
                        Completion Status : <span class="badge badge-pill badge-success ml-1">Completed</span>
                    @else
                        Completion Status : <span class="badge badge-pill badge-danger ml-1">On Progress</span>
                    @endif
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">schedule</i>
                    Progress : {{ $item->pivot->progress }} %
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">date_range</i>
                    Assigned at : {{ date('M d, Y ~ H:i', strtotime($data->created_at)) }}
                </li>
                <li class="nav-item navbar-list__item">
                    <i class="material-icons text-muted icon--left">date_range</i>
                    Completed At : {{ date('M d, Y ~ H:i', strtotime($data->updated_at)) }}
                </li>
            </ul>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>

        <div class="row mb-3">
            <div class="col-6 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data->lessons()->where('completionStatus', '1')->count() }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Total Completed Lesson</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $data->lessons()->where('completionStatus', '0')->count() }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Total Lesson Remaining</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--    Assigned Player    --}}
        <div class="page-separator">
            <div class="page-separator__text">Lesson(s)</div>
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
                            <th>Completion Status</th>
                            <th>Completed At</th>
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

            const playersTable = $('#playersTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('training-videos.lessons-players', ['trainingVideo'=>$data->trainingVideoId, 'lesson'=>$data->id]) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'lessonTitle', name: 'lessonTitle'},
                    {data: 'totalDuration', name: 'totalDuration'},
                    {data: 'status', name: 'status'},
                    {data: 'completedAt', name: 'completedAt'},
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
                                        window.location.href = "{{ route('training-videos.show', $data->trainingVideos->id) }}";
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
