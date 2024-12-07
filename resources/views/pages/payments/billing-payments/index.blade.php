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
                <li class="breadcrumb-item"><a href="{{ route('player.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="page-separator">
            <div class="page-separator__text">outstanding payments</div>
        </div>
        @if(count($openInvoices) > 0)
            @foreach($openInvoices as $invoice)
                <div class="alert alert-soft-warning mb-lg-32pt border-danger">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="mr-8pt">
                            <i class="material-icons">access_time</i>
                        </div>
                        <div class="flex" style="min-width: 180px">
                            <small class="text-100">
                                Please pay your amount due of <strong>{{ priceFormat($invoice->ammountDue) }}</strong> for invoice
                                <a href="{{ route('billing-and-payments.show', $invoice->id) }}" class="text-underline">{{ $invoice->invoiceNumber }}</a>
                            </small>
                        </div>
                        <x-pay-invoice-button btnClass="btn btn-sm btn-primary" btnText="Pay Now!" :invoiceId="$invoice->id" :snapToken="$invoice->snapToken"/>
                    </div>
                </div>
            @endforeach
        @else
            <x-warning-alert text="There are currently no payments due"/>
        @endif


        <div class="page-separator">
            <div class="page-separator__text">Invoice Histories</div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="invoicesTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Inovice Number</th>
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

        <div class="page-separator">
            <div class="page-separator__text">Subscriptions</div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="subscriptionsTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Cycle</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>Next Due Date</th>
                            <th>Amount Due</th>
                            <th>Created At</th>
                            <th>Last Updated</th>
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
                        width: '20%'
                    },
                ]
            });

            $('#subscriptionsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('billing-and-payments.subscriptions') !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'product', name: 'product'},
                    {data: 'cycle', name: 'cycle'},
                    {data: 'status', name: 'status'},
                    {data: 'startDate', name: 'startDate'},
                    {data: 'nextDueDate', name: 'nextDueDate'},
                    {data: 'amountDue', name: 'amountDue'},
                    {data: 'createdAt', name: 'createdAt'},
                    {data: 'updatedAt', name: 'updatedAt'},
                ]
            });
        });
    </script>
@endpush
