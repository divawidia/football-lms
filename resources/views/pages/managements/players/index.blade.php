@extends('layouts.master')
@section('title')
    Players Management
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.change-password-modal :route="route('player-managements.change-password', ['player' => ':id'])"/>
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        @if(isAllAdmin())
            <x-buttons.link-button color="primary" margin="mb-3" :href="route('player-managements.create')" icon="add" text="Add New Player"/>
        @endif

        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-body-form-group flex">
                <div class="row">
                    <div class="col-lg-3">
                        <x-forms.select name="position" label="Filter by Position">
                            <option disabled selected>Select player's position</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                            <option value="{{ null }}">All position</option>
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3">
                        <x-forms.select name="skill" label="Filter by skill">
                            <option selected disabled>Select player's skill level</option>
                            @foreach(['Beginner', 'Intermediate', 'Advance'] as $skills)
                                <option value="{{ $skills }}">{{ $skills }}</option>
                            @endforeach
                            <option value="{{ null }}">All skill</option>
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3">
                        <x-forms.select name="team" label="Filter by team">
                            <option selected disabled>Select player's team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" data-avatar-src="{{ Storage::url($team->logo) }}">{{ $team->teamName }}</option>
                            @endforeach
                            <option value="{{ null }}">All teams</option>
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3">
                        <x-forms.select name="status" label="Filter by status">
                            <option selected disabled>Select player's status</option>
                            @foreach(['Active' => '1', 'Non-active' => '0', 'All Status' => null] as $statusLabel => $statusVal)
                                <option value="{{ $statusVal }}">{{ $statusLabel }}</option>
                            @endforeach
                            <option value="{{ null }}">All status</option>
                        </x-forms.select>
                    </div>
                </div>
            </div>
            <button class="btn bg-alt border-left border-top border-top-sm-0 rounded-0" type="button" id="filterBtn"><i class="material-icons text-primary icon-20pt">refresh</i></button>
        </div>

        <div class="card">
            <div class="card-body">
                <x-table :headers="['#','Name', 'Team', 'Email', 'Phone Number', 'Age','Gender','Status', 'Action']"/>
            </div>
        </div>
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            const playerIndexUrl = @if(isAllAdmin()) '{!! url()->route('player-managements.admin-index') !!}' @else '{!! url()->route('player-managements.coach-index') !!}' @endif

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
                    ],
                    bDestroy: true
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
                "{{ route('player-managements.deactivate', ':id') }}",
                "{{ route('player-managements.index') }}",
                'PATCH',
                "Are you sure to deactivate this player account's status?",
                "Something went wrong when deactivating this player account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.setActivate',
                "{{ route('player-managements.activate', ':id') }}",
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
