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

            <div class="card card-form d-flex flex-column flex-sm-row">
                <div class="card-form__body card-body-form-group flex">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group mb-0 mb-lg-3">
                                <label class="form-label mb-0" for="certification">Filter by certification</label>
                                <select class="form-control form-select" id="certification" data-toggle="select">
                                    <option selected disabled>Select certification</option>
                                    @foreach($certifications as $certification)
                                        <option value="{{ $certification->id }}">{{ $certification->name }}</option>
                                    @endforeach
                                    <option value="{{ null }}">All certification</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label mb-0" for="specializations">Filter by specializations</label>
                                <select class="form-control form-select" id="specializations" data-toggle="select">
                                    <option selected disabled>Select specializations</option>
                                    @foreach($specializations as $specialization)
                                        <option value="{{ $specialization->id }}">{{ $specialization->name }}</option>
                                    @endforeach
                                    <option value="{{ null }}">All specializations</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
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
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label class="form-label mb-0" for="status">Filter by status</label>
                                <select class="form-control form-select" id="status" data-toggle="select">
                                    <option selected disabled>Select status</option>
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

            function coachsTable(certification = null, specializations = null, team = null, status = null) {
                $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! url()->current() !!}',
                        type: "GET",
                        data: {
                            certification: certification,
                            specializations: specializations,
                            team: team,
                            status: status
                        }
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
                    ],
                    bDestroy: true
                });
            }

            $('#filterBtn').on('click', function () {
                const certification = $('#certification').val();
                const specializations = $('#specializations').val();
                const team = $('#team').val();
                const status = $('#status').val();
                coachsTable(certification, specializations, team, status);
            });

            coachsTable();

            processWithConfirmation(
                '.setDeactivate',
                "{{ route('deactivate-coach', ':id') }}",
                "{{ route('coach-managements.index') }}",
                'PATCH',
                "Are you sure to deactivate this coach account's status?",
                "Something went wrong when deactivating this coach account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.setActivate',
                "{{ route('deactivate-coach', ':id') }}",
                "{{ route('coach-managements.index') }}",
                'PATCH',
                "Are you sure to setActivate this coach account's status?",
                "Something went wrong when setActivating this coach account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.delete-user',
                "{{ route('coach-managements.destroy', ['coach' => ':id']) }}",
                "{{ route('coach-managements.index') }}",
                'PATCH',
                "Are you sure to delete this coach account's status?",
                "Something went wrong when deleting this coach account!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
