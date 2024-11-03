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
                        <div class="flex"
                             style="min-width: 180px">
                            <small class="text-100">
                                Please pay your amount due of <strong>{{ priceFormat($invoice->ammountDue) }}</strong> for invoice
                                <a href="{{ route('billing-and-payments.show', $invoice->id) }}" class="text-underline">{{ $invoice->invoiceNumber }}</a>
                            </small>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary payInvoice" id="{{ $invoice->id }}" data-snaptoken="{{ $invoice->snapToken }}">
                            <span class="material-icons mx-2">payment</span>
                            Pay Now!
                        </button>
                    </div>
                </div>
            @endforeach
        @endif


        <div class="page-separator">
            <div class="page-separator__text">Invoice Histories</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
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
    </div>
@endsection
@push('addon-script')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
    <script>
        $(document).ready(function () {
            const body = $('body');

            body.on('click', '.payInvoice', function (e){
                e.preventDefault();
                const snapToken = $(this).attr('data-snapToken');
                const invoiceId = $(this).attr('id');

                snap.pay(snapToken, {
                    // Optional
                    onSuccess: function(result){
                        /* You may add your own js here, this is just example */
                        console.log(result);
                        Swal.fire({
                            title: 'Invoice successfully paid!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });

                    },
                    // Optional
                    onPending: function(result){
                        /* You may add your own js here, this is just example */
                        Swal.fire({
                            title: 'Invoice payment still pending!',
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    // Optional
                    onError: function(result){
                        /* You may add your own js here, this is just example */
                        Swal.fire({
                            title: 'Something wrong when processing Invoice payment!',
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.href = '{{ route('invoices.set-uncollectible', ':id') }}'.replace(':id', invoiceId)
                            }
                        });
                    }
                });
            });

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
                        width: '15%'
                    },
                ]
            });
        });
    </script>
@endpush
