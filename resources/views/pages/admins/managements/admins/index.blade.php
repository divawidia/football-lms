@extends('layouts.master')
@section('title')
    Admins Management
@endsection
@section('page-title')
    Admins Management
@endsection

@section('modal')
    @include('pages.managements.form-modal.change-password')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">Admins Management</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">
                    Admins Management
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        @if(isSuperAdmin())
            <a href="{{ route('admin-managements.create') }}" class="btn btn-primary mb-3">
            <span class="material-icons mr-2">
                add
            </span>
                Add New Admin
            </a>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="table">
                        <thead>
                        <tr>
                            <th>Name</th>
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
            // AJAX DataTable
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    {data: 'name', name: 'name'},
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

            @if(isSuperAdmin())
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
                    url: "{{ route('admin-managements.change-password', ['admin' => ":id"]) }}".replace(':id', id),
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

            // delete data alert
            body.on('click', '.deleteAdmin', function () {
                const id = $(this).attr('id');
                Swal.fire({
                    title: "Are you sure to delete this admin account?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin-managements.destroy', ['admin' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Admin successfully archived!',
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
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: errorThrown
                                });
                            }
                        });
                    }
                });
            });
            @endif
        });
    </script>
@endpush
