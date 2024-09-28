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

                <div class="col-sm-6 col-md-4 col-xl-3">

                    <div class="card card-sm card--elevated p-relative o-hidden overlay overlay--primary js-overlay mdk-reveal js-mdk-reveal "
                         data-overlay-onload-show
                         data-popover-onload-show
                         data-force-reveal
                         data-partial-height="44"
                         data-toggle="popover"
                         data-trigger="click">
                        <a href="instructor-edit-course.html"
                           class="js-image"
                           data-position="">
                            <img src="../../public/images/paths/angular_430x168.png"
                                 alt="course">
                            <span class="overlay__content align-items-start justify-content-start">
                                        <span class="overlay__action card-body d-flex align-items-center">
                                            <i class="material-icons mr-4pt">edit</i>
                                            <span class="card-title text-white">Edit</span>
                                        </span>
                                    </span>
                        </a>
                        <div class="mdk-reveal__content">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex">
                                        <a class="card-title mb-4pt"
                                           href="instructor-edit-course.html">Learn Angular fundamentals</a>
                                    </div>
                                    <a href="instructor-edit-course.html"
                                       class="ml-4pt material-icons text-20 card-course__icon-favorite">edit</a>
                                </div>
                                <div class="d-flex">
                                    <div class="rating flex">
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star_border</span></span>
                                    </div>
                                    <small class="text-50">6 hours</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="popoverContainer d-none">
                        <div class="media">
                            <div class="media-left mr-12pt">
                                <img src="../../public/images/paths/angular_40x40@2x.png"
                                     width="40"
                                     height="40"
                                     alt="Angular"
                                     class="rounded">
                            </div>
                            <div class="media-body">
                                <div class="card-title mb-0">Learn Angular fundamentals</div>
                                <p class="lh-1">
                                    <span class="text-50 small">with</span>
                                    <span class="text-50 small font-weight-bold">Elijah Murray</span>
                                </p>
                            </div>
                        </div>

                        <p class="my-16pt text-70">Learn the fundamentals of working with Angular and how to create basic applications.</p>

                        <div class="mb-16pt">
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Fundamentals of working with Angular</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Create complete Angular applications</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Working with the Angular CLI</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Understanding Dependency Injection</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Testing with Angular</small></p>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="d-flex align-items-center mb-4pt">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>6 hours</small></p>
                                </div>
                                <div class="d-flex align-items-center mb-4pt">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>12 lessons</small></p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>Beginner</small></p>
                                </div>
                            </div>
                            <div class="col text-right">
                                <a href="instructor-edit-course.html"
                                   class="btn btn-primary">Edit course</a>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-sm-6 col-md-4 col-xl-3">

                    <div class="card card-sm card--elevated p-relative o-hidden overlay overlay--primary js-overlay mdk-reveal js-mdk-reveal "
                         data-partial-height="44"
                         data-toggle="popover"
                         data-trigger="click">
                        <a href="instructor-edit-course.html"
                           class="js-image"
                           data-position="">
                            <img src="../../public/images/paths/swift_430x168.png"
                                 alt="course">
                            <span class="overlay__content align-items-start justify-content-start">
                                        <span class="overlay__action card-body d-flex align-items-center">
                                            <i class="material-icons mr-4pt">edit</i>
                                            <span class="card-title text-white">Edit</span>
                                        </span>
                                    </span>
                        </a>
                        <div class="mdk-reveal__content">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex">
                                        <a class="card-title mb-4pt"
                                           href="instructor-edit-course.html">Build an iOS Application in Swift</a>
                                    </div>
                                    <a href="instructor-edit-course.html"
                                       class="ml-4pt material-icons text-20 card-course__icon-favorite">edit</a>
                                </div>
                                <div class="d-flex">
                                    <div class="rating flex">
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star_border</span></span>
                                    </div>
                                    <small class="text-50">6 hours</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="popoverContainer d-none">
                        <div class="media">
                            <div class="media-left mr-12pt">
                                <img src="../../public/images/paths/swift_40x40@2x.png"
                                     width="40"
                                     height="40"
                                     alt="Angular"
                                     class="rounded">
                            </div>
                            <div class="media-body">
                                <div class="card-title mb-0">Build an iOS Application in Swift</div>
                                <p class="lh-1">
                                    <span class="text-50 small">with</span>
                                    <span class="text-50 small font-weight-bold">Elijah Murray</span>
                                </p>
                            </div>
                        </div>

                        <p class="my-16pt text-70">Learn the fundamentals of working with Angular and how to create basic applications.</p>

                        <div class="mb-16pt">
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Fundamentals of working with Angular</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Create complete Angular applications</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Working with the Angular CLI</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Understanding Dependency Injection</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Testing with Angular</small></p>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="d-flex align-items-center mb-4pt">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>6 hours</small></p>
                                </div>
                                <div class="d-flex align-items-center mb-4pt">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>12 lessons</small></p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>Beginner</small></p>
                                </div>
                            </div>
                            <div class="col text-right">
                                <a href="instructor-edit-course.html"
                                   class="btn btn-primary">Edit course</a>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-sm-6 col-md-4 col-xl-3">

                    <div class="card card-sm card--elevated p-relative o-hidden overlay overlay--primary js-overlay mdk-reveal js-mdk-reveal "
                         data-partial-height="44"
                         data-toggle="popover"
                         data-trigger="click">
                        <a href="instructor-edit-course.html"
                           class="js-image"
                           data-position="">
                            <img src="../../public/images/paths/wordpress_430x168.png"
                                 alt="course">
                            <span class="overlay__content align-items-start justify-content-start">
                                        <span class="overlay__action card-body d-flex align-items-center">
                                            <i class="material-icons mr-4pt">edit</i>
                                            <span class="card-title text-white">Edit</span>
                                        </span>
                                    </span>
                        </a>
                        <div class="mdk-reveal__content">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex">
                                        <a class="card-title mb-4pt"
                                           href="instructor-edit-course.html">Build a WordPress Website</a>
                                    </div>
                                    <a href="instructor-edit-course.html"
                                       class="ml-4pt material-icons text-20 card-course__icon-favorite">edit</a>
                                </div>
                                <div class="d-flex">
                                    <div class="rating flex">
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star_border</span></span>
                                    </div>
                                    <small class="text-50">6 hours</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="popoverContainer d-none">
                        <div class="media">
                            <div class="media-left mr-12pt">
                                <img src="../../public/images/paths/wordpress_40x40@2x.png"
                                     width="40"
                                     height="40"
                                     alt="Angular"
                                     class="rounded">
                            </div>
                            <div class="media-body">
                                <div class="card-title mb-0">Build a WordPress Website</div>
                                <p class="lh-1">
                                    <span class="text-50 small">with</span>
                                    <span class="text-50 small font-weight-bold">Elijah Murray</span>
                                </p>
                            </div>
                        </div>

                        <p class="my-16pt text-70">Learn the fundamentals of working with Angular and how to create basic applications.</p>

                        <div class="mb-16pt">
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Fundamentals of working with Angular</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Create complete Angular applications</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Working with the Angular CLI</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Understanding Dependency Injection</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Testing with Angular</small></p>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="d-flex align-items-center mb-4pt">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>6 hours</small></p>
                                </div>
                                <div class="d-flex align-items-center mb-4pt">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>12 lessons</small></p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>Beginner</small></p>
                                </div>
                            </div>
                            <div class="col text-right">
                                <a href="instructor-edit-course.html"
                                   class="btn btn-primary">Edit course</a>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-sm-6 col-md-4 col-xl-3">

                    <div class="card card-sm card--elevated p-relative o-hidden overlay overlay--primary js-overlay mdk-reveal js-mdk-reveal "
                         data-partial-height="44"
                         data-toggle="popover"
                         data-trigger="click">
                        <a href="instructor-edit-course.html"
                           class="js-image"
                           data-position="left">
                            <img src="../../public/images/paths/react_430x168.png"
                                 alt="course">
                            <span class="overlay__content align-items-start justify-content-start">
                                        <span class="overlay__action card-body d-flex align-items-center">
                                            <i class="material-icons mr-4pt">edit</i>
                                            <span class="card-title text-white">Edit</span>
                                        </span>
                                    </span>
                        </a>
                        <div class="mdk-reveal__content">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex">
                                        <a class="card-title mb-4pt"
                                           href="instructor-edit-course.html">Become a React Native Developer</a>
                                    </div>
                                    <a href="instructor-edit-course.html"
                                       class="ml-4pt material-icons text-20 card-course__icon-favorite">edit</a>
                                </div>
                                <div class="d-flex">
                                    <div class="rating flex">
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star</span></span>
                                        <span class="rating__item"><span class="material-icons">star_border</span></span>
                                    </div>
                                    <small class="text-50">6 hours</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="popoverContainer d-none">
                        <div class="media">
                            <div class="media-left mr-12pt">
                                <img src="../../public/images/paths/react_40x40@2x.png"
                                     width="40"
                                     height="40"
                                     alt="Angular"
                                     class="rounded">
                            </div>
                            <div class="media-body">
                                <div class="card-title mb-0">Become a React Native Developer</div>
                                <p class="lh-1">
                                    <span class="text-50 small">with</span>
                                    <span class="text-50 small font-weight-bold">Elijah Murray</span>
                                </p>
                            </div>
                        </div>

                        <p class="my-16pt text-70">Learn the fundamentals of working with Angular and how to create basic applications.</p>

                        <div class="mb-16pt">
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Fundamentals of working with Angular</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Create complete Angular applications</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Working with the Angular CLI</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Understanding Dependency Injection</small></p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="material-icons icon-16pt text-50 mr-8pt">check</span>
                                <p class="flex text-50 lh-1 mb-0"><small>Testing with Angular</small></p>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="d-flex align-items-center mb-4pt">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">access_time</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>6 hours</small></p>
                                </div>
                                <div class="d-flex align-items-center mb-4pt">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">play_circle_outline</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>12 lessons</small></p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="material-icons icon-16pt text-50 mr-4pt">assessment</span>
                                    <p class="flex text-50 lh-1 mb-0"><small>Beginner</small></p>
                                </div>
                            </div>
                            <div class="col text-right">
                                <a href="instructor-edit-course.html"
                                   class="btn btn-primary">Edit course</a>
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
