@extends('layouts.master')
@section('title')
    Competitions
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container d-flex flex-column">
                <h2 class="mb-2">@yield('title')</h2>
                <ol class="breadcrumb p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">
                        @yield('title')
                    </li>
                </ol>
            </div>
        </div>

        <div class="container page-section">
            <a href="{{  route('competition-managements.create') }}" class="btn btn-primary mb-3" id="add-new">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
            <div class="card">
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

        <x-process-data-confirmation btnClass=".delete"
                                     :processRoute="route('competition-managements.destroy', ['competition' => ':id'])"
                                     :routeAfterProcess="route('competition-managements.index')"
                                     method="DELETE"
                                     confirmationText="Are you sure to delete this competition?"
                                     successText="Successfully deleted the competition!"
                                     errorText="Something went wrong when deleting the competition!"/>

        <x-process-data-confirmation btnClass=".cancelBtn"
                                     :processRoute="route('cancelled-competition', ['competition' => ':id'])"
                                     :routeAfterProcess="route('competition-managements.index')"
                                     method="PATCH"
                                     confirmationText="Are you sure to cancel this competition?"
                                     successText="Competition successfully mark as cancelled!"
                                     errorText="Something went wrong when marking the competition as cancelled!"/>

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
            });
        </script>
    @endpush
