@extends('layouts.master')
@section('title')
    Teams Management
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container">
                <h2 class="mb-0 text-left">@yield('title')</h2>
                <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                    <li class="breadcrumb-item active">
                        @yield('title')
                    </li>
                </ol>
            </div>
        </div>

        <div class="container page-section">
            <div class="page-separator">
                <div class="page-separator__text">Our Teams</div>
                @if(isAllAdmin())
                    <a href="{{  route('team-managements.create') }}" class="btn btn-sm btn-primary ml-auto " id="add-new">
                        <span class="material-icons mr-2">
                            add
                        </span>
                        Add New Team
                    </a>
                @endif
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Players</th>
                                <th>Coaches/Staffs</th>
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

            @if(isAllAdmin())
                <div class="page-separator">
                    <div class="page-separator__text">Opponent Teams</div>
                    <a href="{{  route('opponentTeam-managements.create') }}" class="btn btn-sm btn-primary ml-auto " id="add-new">
                        <span class="material-icons mr-2">
                            add
                        </span>
                        Add New Team
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="opponentTable">
                                <thead>
                                <tr>
                                    <th>Name</th>
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
            @endif
        </div>
        @if(isAllAdmin())
            <x-process-data-confirmation btnClass=".setDeactivate"
                                         :processRoute="route('deactivate-team', ':id')"
                                         :routeAfterProcess="route('team-managements.index')"
                                         method="PATCH"
                                         confirmationText="Are you sure to deactivate this team status?"
                                         errorText="Something went wrong when deactivating this team!"/>

            <x-process-data-confirmation btnClass=".setActivate"
                                         :processRoute="route('activate-team', ':id')"
                                         :routeAfterProcess="route('team-managements.index')"
                                         method="PATCH"
                                         confirmationText="Are you sure to activate this team status?"
                                         errorText="Something went wrong when activating this team!"/>

            <x-process-data-confirmation btnClass=".delete-team"
                                        :processRoute="route('team-managements.destroy', ':id')"
                                        :routeAfterProcess="route('team-managements.index')"
                                         method="DELETE"
                                        confirmationText="Are you sure to delete this team?"
                                        errorText="Something went wrong when deleting team!"/>

            <x-process-data-confirmation btnClass=".delete-opponentTeam"
                                        :processRoute="route('opponentTeam-managements.destroy', ':id')"
                                        :routeAfterProcess="route('team-managements.index')"
                                         method="DELETE"
                                        confirmationText="Are you sure to delete this team?"
                                        errorText="Something went wrong when deleting team!"/>
        @endif
    @endsection
    @push('addon-script')
        <script>
            $(document).ready(function() {
                $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{{ $teamRoutes }}',
                    },
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'players', name: 'players' },
                        { data: 'coaches', name: 'coaches'},
                        { data: 'status', name: 'status' },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                @if(isAllAdmin())
                    $('#opponentTable').DataTable({
                        processing: true,
                        serverSide: true,
                        ordering: true,
                        ajax: {
                            url: '{!! route('opponentTeam-managements.index') !!}',
                        },
                        columns: [
                            { data: 'name', name: 'name' },
                            { data: 'status', name: 'status', width: '15%' },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            },
                        ]
                    });
                @endif
            });
        </script>
    @endpush
