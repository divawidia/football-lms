@extends('layouts.master')
@section('title')
    Competitions
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left">
                <div class="flex d-flex flex-column flex-sm-row align-items-center">
                    <div class="mb-24pt mb-sm-0 mr-sm-24pt text-sm-left">
                        <h2 class="mb-0">@yield('title')</h2>
                        <ol class="breadcrumb p-0 m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">
                                @yield('title')
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container page__container page-section">
            <a href="{{  route('competition-managements.create') }}" class="btn btn-primary mb-3" id="add-new">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Group Division</th>
                                <th>Joined Teams</th>
                                <th>Competition Date</th>
                                <th>Location</th>
                                <th>Contact</th>
                                <th>Status</th>
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
                $('body').on('click', '.delete-competition', function() {
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
