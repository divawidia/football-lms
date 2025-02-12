@extends('layouts.master')
@section('title')
    Subscriptions
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.subscriptions.edit-subscription-tax-modal :taxes="$taxes"/>
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0 text-left">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="card">
            <div class="card-body">
                <x-table :headers="['#', 'Name', 'Email', 'Product', 'Cycle', 'Status', 'Start Date', 'Next Due Date', 'Amount Due', 'Created At', 'Last Updated', 'Action']" tableId="subscriptionsTable"/>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
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
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            processWithConfirmation(
                ".deleteSubscription",
                "{{ route('subscriptions.destroy', ':id') }}",
                "{{ route('subscriptions.index') }}",
                "DELETE",
                "Are you sure to delete this player's subscription?",
                "Something went wrong when deleting player's subscription!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
