@extends('layouts.master')
@section('title')
    Player Skill Assessments
@endsection

@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.players-coaches.add-performance-review/>
    <x-modal.players-coaches.skill-assessments-modal/>
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container">
                <h2 class="mb-0">@yield('title')</h2>
                <ol class="breadcrumb p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('coach.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="container page-section">
            <div class="card">
                <div class="card-body">
                    <x-table
                        :headers="['#', 'Name', 'Team', 'Strong Foot', 'Age', 'Gender', 'Last Updated', 'Action']"
                        tableId="table"
                    />
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
                        url: '{!! url()->current() !!}',
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'teams.name', name: 'teams.name' },
                        { data: 'strongFoot', name: 'strongFoot'},
                        { data: 'age', name: 'age' },
                        { data: 'user.gender', name: 'user.gender' },
                        { data: 'lastUpdated', name: 'lastUpdated' },
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ]
                });
            });
        </script>
    @endpush
