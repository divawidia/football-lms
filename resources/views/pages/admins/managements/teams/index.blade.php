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
    @endsection
    @push('addon-script')
        <script>
            $(document).ready(function() {
                const datatable = $('#table').DataTable({
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
                            searchable: false,
                            width: '15%'
                        },
                    ]
                });
                @if(Auth::user()->hasRole('admin'))
                    const opponentTable = $('#opponentTable').DataTable({
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
                                searchable: false,
                                width: '15%'
                            },
                        ]
                    });

                    $('body').on('click', '.delete-team', function() {
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
                                    url: "{{ route('team-managements.destroy', ['team' => ':id']) }}".replace(':id', id),
                                    type: 'DELETE',
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(response) {
                                        Swal.fire({
                                            icon: "success",
                                            title: "Team successfully deleted!",
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

                    $('body').on('click', '.delete-opponentTeam', function() {
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
                                    url: "{{ route('opponentTeam-managements.destroy', ['team' => ':id']) }}".replace(':id', id),
                                    type: 'DELETE',
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(response) {
                                        Swal.fire({
                                            icon: "success",
                                            title: "Team successfully deleted!",
                                        });
                                        opponentTable.ajax.reload();
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
                @endif
            });
        </script>
    @endpush
