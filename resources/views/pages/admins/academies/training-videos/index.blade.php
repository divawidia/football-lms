@extends('layouts.master')
@section('title')
    Training Videos
@endsection
@section('page-title')
    @yield('title')
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
            <a href="{{  route('training-videos.create') }}" class="btn btn-primary mb-3" id="add-new">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>

            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm card--elevated p-relative o-hidden">
                        <a href="">
                            <img class="img-fluid" src="{{ Storage::url('images/image 1.jpg') }}" alt="training-preview">
                        </a>
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex">
                                    <a class="card-title mb-4pt" href="">Learn Angular fundamentals</a>
                                </div>
                                <a href="" class="ml-4pt material-icons text-20 card-course__icon-favorite">edit</a>
                            </div>
                            <div class="d-flex">
                                <div class="d-flex align-items-center mr-2">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>6 hours</small></p>
                                </div>
                                <div class="d-flex align-items-center mr-2">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>12 lessons</small></p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>Beginner</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm card--elevated p-relative o-hidden">
                        <a href="">
                            <img class="img-fluid" src="{{ Storage::url('images/image 1.jpg') }}" alt="training-preview">
                        </a>
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex">
                                    <a class="card-title mb-4pt" href="">Learn Angular fundamentals</a>
                                </div>
                                <a href="" class="ml-4pt material-icons text-20 card-course__icon-favorite">edit</a>
                            </div>
                            <div class="d-flex">
                                <div class="d-flex align-items-center mr-2">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>6 hours</small></p>
                                </div>
                                <div class="d-flex align-items-center mr-2">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>12 lessons</small></p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>Beginner</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm card--elevated p-relative o-hidden">
                        <a href="">
                            <img class="img-fluid" src="{{ Storage::url('images/image 1.jpg') }}" alt="training-preview">
                        </a>
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex">
                                    <a class="card-title mb-4pt" href="">Learn Angular fundamentals</a>
                                </div>
                                <a href="" class="ml-4pt material-icons text-20 card-course__icon-favorite">edit</a>
                            </div>
                            <div class="d-flex">
                                <div class="d-flex align-items-center mr-2">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>6 hours</small></p>
                                </div>
                                <div class="d-flex align-items-center mr-2">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>12 lessons</small></p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>Beginner</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm card--elevated p-relative o-hidden">
                        <a href="">
                            <img class="img-fluid" src="{{ Storage::url('images/image 1.jpg') }}" alt="training-preview">
                        </a>
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex">
                                    <a class="card-title mb-4pt" href="">Learn Angular fundamentals</a>
                                </div>
                                <a href="" class="ml-4pt material-icons text-20 card-course__icon-favorite">edit</a>
                            </div>
                            <div class="d-flex">
                                <div class="d-flex align-items-center mr-2">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>6 hours</small></p>
                                </div>
                                <div class="d-flex align-items-center mr-2">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>12 lessons</small></p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>Beginner</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endsection
    @push('addon-script')
        <script>
            $(document).ready(function() {
                const datatable = $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! url()->current() !!}',
                    },
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'divisions', name: 'divisions' },
                        { data: 'teams', name: 'teams' },
                        { data: 'date', name: 'date'},
                        { data: 'location', name: 'location'},
                        { data: 'contact', name: 'contact' },
                        { data: 'status', name: 'status' },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            width: '15%'
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
