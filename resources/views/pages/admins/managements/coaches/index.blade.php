@extends('layouts.master')
@section('title')
    Coaches Management
@endsection

@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-change-password-modal :route="route('coach-managements.change-password', ['coach' => ':id'])"/>
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0 text-left">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item">
                    <a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        @if(isAllAdmin())
            <a href="{{  route('coach-managements.create') }}" class="btn btn-primary mb-3" id="add-new">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New Coach
            </a>
        @endif
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

    @if(isAllAdmin())
        <x-process-data-confirmation btnClass=".setDeactivate"
                                     :processRoute="route('deactivate-coach', ':id')"
                                     :routeAfterProcess="route('coach-managements.index')"
                                     method="PATCH"
                                     confirmationText="Are you sure to deactivate this coach account's status?"
                                     errorText="Something went wrong when deactivating this coach account!"/>

        <x-process-data-confirmation btnClass=".setActivate"
                                     :processRoute="route('activate-coach', ':id')"
                                     :routeAfterProcess="route('coach-managements.index')"
                                     method="PATCH"
                                     confirmationText="Are you sure to activate this coach account's status?"
                                     errorText="Something went wrong when activating this coach account!"/>

        <x-process-data-confirmation btnClass=".delete-user"
                                     :processRoute="route('coach-managements.destroy', ['coach' => ':id'])"
                                     :routeAfterProcess="route('coach-managements.index')"
                                     method="DELETE"
                                     confirmationText="Are you sure to delete this coach account?"
                                     errorText="Something went wrong when deleting this coach account!"/>
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
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
