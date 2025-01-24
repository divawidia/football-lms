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
                    <li class="breadcrumb-item active">@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="container page-section">
            @if(isAllAdmin())
                <x-buttons.link-button color="primary" margin="mb-3" :href="route('team-managements.create')" icon="add" text="Add New team"/>
            @endif
            <div class="card">
                <div class="card-body">
                    <x-table :headers="['#','Name', 'Players', 'Coaches/Staffs', 'Status', 'Action']"/>
                </div>
            </div>
        </div>
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
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        { data: 'name', name: 'name' },
                        { data: 'players', name: 'players' },
                        { data: 'coaches', name: 'coaches'},
                        { data: 'status', name: 'status' },
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ]
                });

                processWithConfirmation(
                    ".setDeactivate",
                    "{{ route('team-managements.deactivate', ':id') }}",
                    "{{ route('team-managements.index') }}",
                    "PATCH",
                    "Are you sure to deactivate this team status?",
                    "Something went wrong when deactivating this team!",
                    "{{ csrf_token() }}"
                );

                processWithConfirmation(
                    ".setActivate",
                    "{{ route('team-managements.activate', ':id') }}",
                    "{{ route('team-managements.index') }}",
                    "PATCH",
                    "Are you sure to activate this team status?",
                    "Something went wrong when activating this team!",
                    "{{ csrf_token() }}"
                );

                processWithConfirmation(
                    ".delete-team",
                    "{{ route('team-managements.destroy', ':id') }}",
                    "{{ route('team-managements.index') }}",
                    "DELETE",
                    "Are you sure to delete this team?",
                    "Something went wrong when deleting team!",
                    "{{ csrf_token() }}"
                );
            });
        </script>
    @endpush
