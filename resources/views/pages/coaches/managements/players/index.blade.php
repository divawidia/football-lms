@extends('layouts.master')
@section('title')
    Players Management
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left">
                <div class="flex d-flex flex-column flex-sm-row align-items-center">
                    <div class="mb-24pt mb-sm-0 w-100">
                        <div class="d-flex flex-row">
                            <h2 class="mb-0">@yield('title')</h2>
                        </div>
                        <ol class="breadcrumb p-0 m-0">
                            <li class="breadcrumb-item"><a href="{{ route('coach.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">
                                @yield('title')
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container page__container page-section">
            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
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
                        { data: 'teams.name', name: 'teams.name' },
                        { data: 'user.email', name: 'user.email'},
                        { data: 'user.phoneNumber', name: 'user.phoneNumber' },
                        { data: 'age', name: 'age' },
                        { data: 'user.gender', name: 'user.gender' },
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
