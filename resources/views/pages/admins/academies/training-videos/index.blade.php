@extends('layouts.master')
@section('title')
    Training Videos
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    @include('pages.admins.academies.training-videos.form-modal.create')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container page__container d-flex flex-column">
            <h2 class="mb-2">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page__container page-section">
        <a href="" class="btn btn-primary mb-3" id="addTrainingVideo">
                <span class="material-icons mr-2">
                    add
                </span>
            Add New
        </a>

        @if(count($data)==0)
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <strong class="text-black-100">You haven't created any training videos yet</strong>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($data as $training)
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-sm card--elevated p-relative o-hidden">
                            <a href="{{ route('training-videos.show', $training->id) }}">
                                <img class="img-index-page" src="{{ Storage::url($training->previewPhoto) }}"
                                     alt="training-preview">
                            </a>
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex">
                                        <a class="card-title mb-4pt"
                                           href="{{ route('training-videos.show', $training->id) }}">{{$training->trainingTitle}}</a>
                                    </div>
                                    <a href="{{ route('training-videos.show', $training->id) }}"
                                       class="ml-4pt material-icons text-20 card-course__icon-favorite">edit</a>
                                </div>
                                <div class="d-flex">
                                    <div class="d-flex align-items-center mr-2">
                                        <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                        <p class="flex text-50 lh-1 mb-0"><small>{{ $training->totalMinute }}
                                                hours</small></p>
                                    </div>
                                    <div class="d-flex align-items-center mr-2">
                                        <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                        <p class="flex text-50 lh-1 mb-0"><small>{{ $training->totalLesson }}
                                                lessons</small></p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                        <p class="flex text-50 lh-1 mb-0"><small>{{ $training->level }}</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {

            $('#addTrainingVideo').on('click', function (e) {
                e.preventDefault();
                $('#addTrainingVideoModal').modal('show');
            });

            $('#formAddTrainingVideoModal').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('training-videos.store') }}",
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#editTrainingVideoModal').modal('hide');
                        Swal.fire({
                            title: 'Training video successfully created!',
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
                        $.each(response.errors, function (key, val) {
                            $('span.' + key).text(val[0]);
                            $("#" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when creating data!",
                            text: errorThrown,
                        });
                    }
                });
            });
        });
    </script>
@endpush
