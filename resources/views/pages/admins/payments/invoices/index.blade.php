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
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Invoices</div>
            <a href="{{ route('invoices.archived') }}" class="btn btn-sm btn-white border-danger ml-auto ">
                <span class="material-icons mr-2 text-danger">
                    delete
                </span>
                Archived Invoice
            </a>
            <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-primary ml-3 ">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="invoicesTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Inovice Number</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Amount Due</th>
                            <th>Due Date</th>
                            <th>Status</th>
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

    <x-process-data-confirmation btnClass=".setUcollectibleStatus"
                                 :processRoute="route('invoices.set-uncollectible', ':id')"
                                 :routeAfterProcess="route('invoices.index')"
                                 method="PATCH"
                                 confirmationText="Are you sure to mark this invoice to uncollectible?"
                                 successText="Successfully mark the invoice to uncollectible!"
                                 errorText="Something went wrong when marking the invoice to uncollectible!"/>

    <x-process-data-confirmation btnClass=".deleteInvoice"
                                 :processRoute="route('invoices.destroy', ['invoice' => ':id'])"
                                 :routeAfterProcess="route('invoices.index')"
                                 method="DELETE"
                                 confirmationText="Are you sure to archive this invoice?"
                                 successText="Invoice successfully archived!"
                                 errorText="Something went wrong when archiving data!"/>
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
