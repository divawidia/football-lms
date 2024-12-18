@extends('layouts.master')
@section('title')
    Archived Invoices
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container page__container d-flex flex-column">
            <h2 class="mb-0 text-left">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">@yield('title')</div>
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
                            <th>Deleted At</th>
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

    <x-process-data-confirmation btnClass=".restoreInvoice"
                                 :processRoute="route('invoices.restore', ['invoice' => ':id'])"
                                 :routeAfterProcess="route('invoices.index')"
                                 method="POST"
                                 confirmationText="Are you sure to restore this invoice?"
                                 errorText="Something went wrong when restoring the invoice!"/>

    <x-process-data-confirmation btnClass=".forceDeleteInvoice"
                                 :processRoute="route('invoices.permanent-delete', ['invoice' => ':id'])"
                                 :routeAfterProcess="route('invoices.index')"
                                 method="DELETE"
                                 confirmationText="Are you sure to permanently delete this invoice?"
                                 errorText="Something went wrong when deleting the invoice!"/>

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
                    {data: 'deletedAt', name: 'deletedAt'},
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


            // delete product data
            {{--body.on('click', '.restoreInvoice', function () {--}}
            {{--    const id = $(this).attr('id');--}}
            {{--    Swal.fire({--}}
            {{--        title: "Are you sure to restore this invoice?",--}}
            {{--        text: "You won't be able to revert this!",--}}
            {{--        icon: "warning",--}}
            {{--        showCancelButton: true,--}}
            {{--        confirmButtonColor: "#1ac2a1",--}}
            {{--        cancelButtonColor: "#E52534",--}}
            {{--        confirmButtonText: "Yes, restore it!"--}}
            {{--    }).then((result) => {--}}
            {{--        if (result.isConfirmed) {--}}
            {{--            $.ajax({--}}
            {{--                url: "{{ route('invoices.restore', ['invoice' => ':id']) }}".replace(':id', id),--}}
            {{--                type: 'POST',--}}
            {{--                data: {--}}
            {{--                    _token: "{{ csrf_token() }}"--}}
            {{--                },--}}
            {{--                success: function () {--}}
            {{--                    Swal.fire({--}}
            {{--                        title: 'Invoice successfully restored!',--}}
            {{--                        icon: 'success',--}}
            {{--                        showCancelButton: false,--}}
            {{--                        confirmButtonColor: "#1ac2a1",--}}
            {{--                        confirmButtonText:--}}
            {{--                            'Ok!'--}}
            {{--                    }).then((result) => {--}}
            {{--                        if (result.isConfirmed) {--}}
            {{--                            location.href = '{{ route('invoices.index') }}';--}}
            {{--                        }--}}
            {{--                    });--}}
            {{--                },--}}
            {{--                error: function (jqXHR, textStatus, errorThrown) {--}}
            {{--                    Swal.fire({--}}
            {{--                        icon: "error",--}}
            {{--                        title: "Something went wrong when archiving data!",--}}
            {{--                        text: errorThrown--}}
            {{--                    });--}}
            {{--                }--}}
            {{--            });--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}

            // force delete invoice
            {{--body.on('click', '.forceDeleteInvoice',function (){--}}
            {{--    const id = $(this).attr('id');--}}
            {{--    Swal.fire({--}}
            {{--        title: "Are you sure to permanently delete this invoice?",--}}
            {{--        text: "You won't be able to revert this!",--}}
            {{--        icon: "warning",--}}
            {{--        showCancelButton: true,--}}
            {{--        confirmButtonColor: "#1ac2a1",--}}
            {{--        cancelButtonColor: "#E52534",--}}
            {{--        confirmButtonText: "Yes, delete it!"--}}
            {{--    }).then((result) => {--}}
            {{--        if (result.isConfirmed) {--}}
            {{--            $.ajax({--}}
            {{--                url: "{{ route('invoices.permanent-delete', ['invoice' => ':id']) }}".replace(':id', id),--}}
            {{--                type: 'DELETE',--}}
            {{--                data: {--}}
            {{--                    _token: "{{ csrf_token() }}"--}}
            {{--                },--}}
            {{--                success: function () {--}}
            {{--                    Swal.fire({--}}
            {{--                        title: 'Invoice successfully permanently deleted!',--}}
            {{--                        icon: 'success',--}}
            {{--                        showCancelButton: false,--}}
            {{--                        confirmButtonColor: "#1ac2a1",--}}
            {{--                        confirmButtonText:--}}
            {{--                            'Ok!'--}}
            {{--                    }).then((result) => {--}}
            {{--                        if (result.isConfirmed) {--}}
            {{--                            location.href = '{{ route('invoices.index') }}';--}}
            {{--                        }--}}
            {{--                    });--}}
            {{--                },--}}
            {{--                error: function (jqXHR, textStatus, errorThrown) {--}}
            {{--                    Swal.fire({--}}
            {{--                        icon: "error",--}}
            {{--                        title: "Something went wrong when archiving data!",--}}
            {{--                        text: errorThrown--}}
            {{--                    });--}}
            {{--                }--}}
            {{--            });--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}
        });
    </script>
@endpush
