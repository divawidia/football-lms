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
            <x-buttons.link-button margin="mb-3" :href="route('competition-managements.create')" icon="add" text="Add New"/>

            <div class="card">
                <div class="card-body">
                    <x-table :headers="['#','Name', 'Internal/External', 'Competition Date', 'Location', 'Status', 'Action']" tableId="table"/>
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
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        { data: 'name', name: 'name' },
                        { data: 'isInternal', name: 'isInternal' },
                        { data: 'date', name: 'date'},
                        { data: 'location', name: 'location'},
                        { data: 'status', name: 'status' },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });

                processWithConfirmation(
                    '.delete',
                    "{{ route('competition-managements.destroy', ['competition' => ':id']) }}",
                    "{{ route('competition-managements.index') }}",
                    'DELETE',
                    "Are you sure to delete this competition?",
                    "Something went wrong when deleting the competition!",
                    "{{ csrf_token() }}",
                );

                processWithConfirmation(
                    '.cancelBtn',
                    "{{ route('cancelled-competition', ['competition' => ':id']) }}",
                    "{{ route('competition-managements.index') }}",
                    'PATCH',
                    "Are you sure to cancel this competition?",
                    "Something went wrong when cancelling the competition!",
                    "{{ csrf_token() }}",
                );
            });
        </script>
    @endpush
