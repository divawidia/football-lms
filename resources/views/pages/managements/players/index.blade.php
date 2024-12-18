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
                            <div class="form-group mb-0 mb-lg-4">
                                <label class="form-label mb-0" for="startDateFilter">Filter by Position</label>
                                <input id="startDateFilter"
                                       type="text"
                                       class="form-control"
                                       placeholder="Start Date"
                                       onfocus="(this.type='date')"
                                       onblur="(this.type='text')"/>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label mb-0" for="endDateFilter">Filter by Skills</label>
                                <input id="endDateFilter"
                                       type="text"
                                       class="form-control"
                                       placeholder="End Date"
                                       onfocus="(this.type='date')"
                                       onblur="(this.type='text')"/>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label mb-0" for="team">Filter by team</label>
                                <select class="form-control form-select" id="team" data-toggle="select">
                                    <option selected disabled>Select team</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" data-avatar-src="{{ Storage::url($team->logo) }}">{{ $team->teamName }}</option>
                                    @endforeach
                                    <option value="{{ null }}">All teams</option>
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

    @if(isAllAdmin())
        <x-process-data-confirmation btnClass=".setDeactivate"
                                     :processRoute="route('deactivate-player', ':id')"
                                     :routeAfterProcess="route('player-managements.index')"
                                     method="PATCH"
                                     confirmationText="Are you sure to deactivate this player account's status?"
                                     errorText="Something went wrong when deactivating this player account!"/>

        <x-process-data-confirmation btnClass=".setActivate"
                                     :processRoute="route('activate-player', ':id')"
                                     :routeAfterProcess="route('player-managements.index')"
                                     method="PATCH"
                                     confirmationText="Are you sure to activate this player account's status?"
                                     errorText="Something went wrong when activating this player account!"/>

        <x-process-data-confirmation btnClass=".delete-user"
                                     :processRoute="route('player-managements.destroy', ['player' => ':id'])"
                                     :routeAfterProcess="route('player-managements.index')"
                                     method="DELETE"
                                     confirmationText="Are you sure to delete this player account?"
                                     errorText="Something went wrong when deleting this player account!"/>
    @endif
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            const body = $('body');
            const playerIndexUrl = @if(isAllAdmin()) '{!! url()->route('admin.player-managements.index') !!}' @elseif(isCoach()) '{!! url()->route('coach.player-managements.index') !!}' @endif

            const datatable = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: playerIndexUrl
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
        });
    </script>
@endpush
