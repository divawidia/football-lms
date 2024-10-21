@extends('layouts.master')
@section('title')
    Coaches Management
@endsection

@section('page-title')
    @yield('title')
@endsection

@section('modal')
    @include('pages.managements.form-modal.change-password')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0 text-left">@yield('title')</h2>

            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="@if(Auth::user()->hasRole('admin|Super-Admin'))
                        {{ route('admin.dashboard') }}
                        @elseif(Auth::user()->hasRole('coach'))
                        {{ route('coach.dashboard') }}
                        @endif">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <a href="{{  route('coach-managements.create') }}" class="btn btn-primary mb-3" id="add-new">
                <span class="material-icons mr-2">
                    add
                </span>
            Add New Coach
        </a>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Team</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Age</th>
                            <th>Gender</th>
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
        $(document).ready(function () {
            const body = $('body');
            const datatable = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'teams', name: 'teams'},
                    {data: 'user.email', name: 'user.email'},
                    {data: 'user.phoneNumber', name: 'user.phoneNumber'},
                    {data: 'age', name: 'age'},
                    {data: 'user.gender', name: 'user.gender'},
                    {data: 'status', name: 'status'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            body.on('click', '.changePassword', function (e) {
                const id = $(this).attr('id');
                e.preventDefault();
                $('#changePasswordModal').modal('show');
                $('#userId').val(id);
            })
            // update admin password
            $('#formChangePasswordModal').on('submit', function (e) {
                e.preventDefault();
                const id = $('#userId').val();
                $.ajax({
                    url: "{{ route('player-managements.change-password', ['player' => ":id"]) }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#changePasswordModal').modal('hide');
                        Swal.fire({
                            title: 'Accounts password successfully updated!',
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
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

            body.on('click', '.delete-user', function () {
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
                            url: "{{ route('coach-managements.destroy', ['coach' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Coach's account successfully deleted!",
                                });
                                datatable.ajax.reload();
                            },
                            error: function (error) {
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
