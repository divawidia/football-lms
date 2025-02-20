@extends('layouts.master')
@section('title')
    Archived Invoices
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0 text-left">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="page-separator">
            <div class="page-separator__text">@yield('title')</div>
        </div>
        <div class="card">
            <div class="card-body">
                <x-table tableId="invoicesTable" :headers="['#', 'Invoice Number', 'Name', 'Email', 'Amount Due', 'Due Date', 'Status', 'Deleted At', 'Last Updated', 'Action']" />
            </div>
        </div>
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#invoicesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'invoiceNumber', name: 'invoiceNumber'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'ammount', name: 'ammount'},
                    {data: 'dueDate', name: 'dueDate'},
                    {data: 'status', name: 'status'},
                    {data: 'deletedAt', name: 'deletedAt'},
                    {data: 'updatedAt', name: 'updatedAt'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            processWithConfirmation(
                ".restoreInvoice",
                "{{ route('invoices.restore', ['invoice' => ':id']) }}",
                "{{ route('invoices.index') }}",
                "POST",
                "Are you sure to restore this invoice?",
                "Something went wrong when restoring the invoice!",
                "{{ csrf_token() }}"
            );
            processWithConfirmation(
                ".forceDeleteInvoice",
                "{{ route('invoices.permanent-delete', ['invoice' => ':id']) }}",
                "{{ route('invoices.index') }}",
                "DELETE",
                "Are you sure to permanently delete this invoice?",
                "Something went wrong when deleting the invoice!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
