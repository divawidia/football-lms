@extends('layouts.master')
@section('title')
    Admins Management
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-change-password-modal :route="route('admin-managements.change-password', ['admin' => ':id'])"/>
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        @if(isSuperAdmin())
            <x-buttons.link-button color="primary" margin="mb-3" :href="route('admin-managements.create')" icon="add" text="Add New Admin"/>
        @endif
        <div class="card">
            <div class="card-body">
                <x-table :headers="['#','Name', 'Email', 'Phone Number', 'Age','Gender','Status', 'Action']"/>
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script type="module">
        import { processWithConfirmation } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;

        $(document).ready(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
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

            processWithConfirmation(
                '.setDeactivate',
                "{{ route('admin-managements.deactivate', ':id') }}",
                "{{ route('admin-managements.index') }}",
                'PATCH',
                "Are you sure to deactivate this admin account's status?",
                "Something went wrong when deactivating this admin account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.setActivate',
                "{{ route('admin-managements.activate', ':id') }}",
                "{{ route('admin-managements.index') }}",
                'PATCH',
                "Are you sure to activate this admin account's status?",
                "Something went wrong when activating this admin account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.deleteAdmin',
                "{{ route('admin-managements.destroy', ['admin' => ':id']) }}",
                "{{ route('admin-managements.index') }}",
                'DELETE',
                "Are you sure to delete this admin account?",
                "Something went wrong when deleting this admin account!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
