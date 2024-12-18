@extends('layouts.master')
@section('title')
    Players Management
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-change-password-modal :route="route('player-managements.change-password', ['player' => ':id'])"/>
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item">
                    <a href="{{ checkRoleDashboardRoute() }}">
                        Home
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        @if(isAllAdmin())
            <a href="{{  route('player-managements.create') }}" class="btn btn-primary mb-3" id="add-new">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New Player
            </a>
        @endif

            <div class="card card-form d-flex flex-column flex-sm-row">
                <div class="card-form__body card-body-form-group flex">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group mb-0 mb-lg-3">
                                <label class="form-label mb-0" for="position">Filter by Position</label>
                                <select class="form-control form-select" id="position" data-toggle="select">
                                    <option selected disabled>Select player's position</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                    <option value="{{ null }}">All teams</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label mb-0" for="skill">Filter by Skills</label>
                                <select class="form-control form-select" id="skill" data-toggle="select">
                                    <option selected disabled>Select player's skill level</option>
                                    @foreach(['Beginner', 'Intermediate', 'Advance'] as $skills)
                                        <option value="{{ $skills }}">{{ $skills }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label mb-0" for="team">Filter by team</label>
                                <select class="form-control form-select" id="team" data-toggle="select">
                                    <option selected disabled>Select player's team</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" data-avatar-src="{{ Storage::url($team->logo) }}">{{ $team->teamName }}</option>
                                    @endforeach
                                    <option value="{{ null }}">All teams</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label mb-0" for="status">Filter by status</label>
                                <select class="form-control form-select" id="status" data-toggle="select">
                                    <option selected disabled>Select player's status</option>
                                    @foreach(['Active' => '1', 'Non-active' => '0', 'All Status' => null] as $statusLabel => $statusVal)
                                        <option value="{{ $statusVal }}">{{ $statusLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn bg-alt border-left border-top border-top-sm-0 rounded-0" type="button" id="filterBtn"><i class="material-icons text-primary icon-20pt">refresh</i></button>
            </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="table">
                        <thead>
                        <tr>
                             <th>#</th>
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
    <script type="module">
        import { processWithConfirmation } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;
        $(document).ready(function () {
            const playerIndexUrl = @if(isAllAdmin()) '{!! url()->route('admin.player-managements.index') !!}' @elseif(isCoach()) '{!! url()->route('coach.player-managements.index') !!}' @endif

            function playersTable(position = null, skill = null, team = null, status = null) {
                $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: playerIndexUrl,
                        type: "GET",
                        data: {
                            position: position,
                            skill: skill,
                            team: team,
                            status: status
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'teams.name', name: 'teams.name'},
                        {data: 'user.email', name: 'user.email'},
                        {data: 'user.phoneNumber', name: 'user.phoneNumber'},
                        {data: 'age', name: 'age'},
                        {data: 'user.gender', name: 'user.gender'},
                        {data: 'status', name: 'status'},
                        {
                            data: 'action', name: 'action', orderable: false, searchable: false, width: '15%'
                        },
                    ]
                });
            }

            $('#filterBtn').on('click', function () {
                const position = $('#position').val();
                const skill = $('#skill').val();
                const team = $('#team').val();
                const status = $('#status').val();
                playersTable(position, skill, team, status);
            });

            playersTable();

            @if(isAllAdmin())
            processWithConfirmation(
                '.setDeactivate',
                "{{ route('deactivate-player', ':id') }}",
                "{{ route('player-managements.index') }}",
                'PATCH',
                "Are you sure to deactivate this player account's status?",
                "Something went wrong when deactivating this player account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.setActivate',
                "{{ route('activate-player', ':id') }}",
                "{{ route('player-managements.index') }}",
                'PATCH',
                "Are you sure to activate this player account's status?",
                "Something went wrong when activating this player account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.delete-user',
                "{{ route('player-managements.destroy', ['player' => ':id']) }}",
                "{{ route('player-managements.index') }}",
                'DELETE',
                "Are you sure to delete this player account?",
                "Something went wrong when deleting this player account!",
                "{{ csrf_token() }}"
            );
            @endif
        });
    </script>
@endpush
