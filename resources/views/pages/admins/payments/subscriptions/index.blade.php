@extends('layouts.master')
@section('title')
    Subscriptions
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-edit-subscription-tax-modal/>
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0 text-left">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <a href="{{ route('subscriptions.create') }}" class="btn btn-primary my-3 ">
            <span class="material-icons mr-2">
                add
            </span>
            Add New
        </a>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="subscriptionsTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Product</th>
                            <th>Cycle</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>Next Due Date</th>
                            <th>Amount Due</th>
                            <th>Created At</th>
                            <th>Last Updated</th>
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

    <x-delete-data-confirmation deleteBtnClass=".deleteSubscription" :destroyRoute="route('subscriptions.destroy', ':id')" :routeAfterDelete="route('subscriptions.index')"/>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            const body = $('body');

            $('#subscriptionsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'product', name: 'product'},
                    {data: 'cycle', name: 'cycle'},
                    {data: 'status', name: 'status'},
                    {data: 'startDate', name: 'startDate'},
                    {data: 'nextDueDate', name: 'nextDueDate'},
                    {data: 'amountDue', name: 'amountDue'},
                    {data: 'createdAt', name: 'createdAt'},
                    {data: 'updatedAt', name: 'updatedAt'},
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
