@extends('layouts.master')
@section('title')
    Invoices
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
                <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Invoices</div>
            <x-buttons.link-button color="white border-danger" margin="ml-auto" :href="route('invoices.archived')" icon="delete" text="Archived Invoice"/>
            <x-buttons.link-button color="primary" margin="ml-3" :href="route('invoices.create')" icon="add" text="Add New Invoice"/>
        </div>
        <div class="card">
            <div class="card-body">
                <x-table tableId="invoicesTable" :headers="['#', 'Invoice Number', 'Contact', 'Email', 'Due Date', 'Created At', 'Last Updated', 'Subtotal', 'Total Tax', 'Total Amount', 'Status', 'Payment Method', 'Action']" />
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
                    url: '{!! url()->current() !!}'
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'invoiceNumber', name: 'invoiceNumber'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'dueDate', name: 'dueDate'},
                    {data: 'createdAt', name: 'createdAt'},
                    {data: 'updatedAt', name: 'updatedAt'},
                    {data: 'subtotal', name: 'subtotal'},
                    {data: 'totalTax', name: 'totalTax'},
                    {data: 'ammount', name: 'ammount'},
                    {data: 'status', name: 'status'},
                    {data: 'paymentMethod', name: 'paymentMethod'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            processWithConfirmation(
                ".setUcollectibleStatus",
                "{{ route('invoices.set-uncollectible', ':id') }}",
                null,
                "PATCH",
                "Are you sure to mark this invoice as uncollectible?",
                "Something went wrong when marking the invoice as uncollectible!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".deleteInvoice",
                "{{ route('invoices.destroy', ['invoice' => ':id']) }}",
                null,
                "DELETE",
                "Are you sure to archive this invoice?",
                "Something went wrong when archiving data!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
