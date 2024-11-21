@extends('layouts.master')
@section('title')
    Admins Management
@endsection
@section('page-title')
    Admins Management
@endsection

@section('modal')
    <x-change-password-modal :route="route('admin-managements.change-password', ['admin' => ':id'])"/>
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

    @if(isSuperAdmin())
        <x-process-data-confirmation btnClass=".setDeactivate"
                                     :processRoute="route('deactivate-admin', ':id')"
                                     :routeAfterProcess="route('admin-managements.index')"
                                     method="PATCH"
                                     confirmationText="Are you sure to deactivate this admin account's status?"
                                     errorText="Something went wrong when deactivating this admin account!"/>

        <x-process-data-confirmation btnClass=".setActivate"
                                     :processRoute="route('activate-admin', ':id')"
                                     :routeAfterProcess="route('admin-managements.index')"
                                     method="PATCH"
                                     confirmationText="Are you sure to activate this admin account's status?"
                                     errorText="Something went wrong when activating this admin account!"/>

        <x-process-data-confirmation btnClass=".deleteAdmin"
                                     :processRoute="route('admin-managements.destroy', ['admin' => ':id'])"
                                     :routeAfterProcess="route('admin-managements.index')"
                                     method="DELETE"
                                     confirmationText="Are you sure to delete this admin account?"
                                     errorText="Something went wrong when deleting this admin account!"/>
    @endif
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
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
        });
    </script>
@endpush
