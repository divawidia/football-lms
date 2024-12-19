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
            <div class="card card-form d-flex flex-column flex-sm-row">
                <div class="card-form__body card-body-form-group flex">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group mb-0 mb-lg-3">
                                <label class="form-label mb-0" for="position">Filter by Specialization</label>
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
                                <label class="form-label mb-0" for="skill">Filter by Certification</label>
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
