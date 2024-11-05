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
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            const body = $('body');

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

            function updateInvoiceStatus(status, route){
                $.ajax({
                    url: route,
                    method: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        Swal.fire({
                            title: 'Invoice successfully mark as '+status+'!',
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when processing invoice status!",
                            text: errorThrown,
                        });
                    }
                });
            }

            body.on('click', '.setStatus', function (e){
                e.preventDefault();
                const invoiceId = $(this).attr('id');
                const status = $(this).attr('data-status');

                if (status === 'paid'){
                    updateInvoiceStatus(status, '{{ route('invoices.set-paid', ':id') }}'.replace(':id', invoiceId))
                } else if(status === 'uncollectible'){
                    updateInvoiceStatus(status, '{{ route('invoices.set-uncollectible', ':id') }}'.replace(':id', invoiceId))
                } else if(status === 'past-due'){
                    updateInvoiceStatus(status, '{{ route('invoices.set-past-due', ':id') }}'.replace(':id', invoiceId))
                } else if(status === 'open'){
                    updateInvoiceStatus(status, '{{ route('invoices.set-open', ':id') }}'.replace(':id', invoiceId))
                }
            });

            // archive invoice
            body.on('click', '.deleteInvoice', function () {
                const id = $(this).attr('id');
                Swal.fire({
                    title: "Are you sure to archive this invoice?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, archive it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('invoices.destroy', ['invoice' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire({
                                    title: 'Invoice successfully archived!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when archiving data!",
                                    text: errorThrown
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
