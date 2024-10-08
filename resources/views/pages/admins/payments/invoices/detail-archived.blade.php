@extends('layouts.master')
@section('title')
    Invoices {{ $data['invoice']->invoiceNumber }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('invoices.index') }}" class="nav-link text-70"><i class="material-icons icon--left">keyboard_backspace</i> Back to Invoices</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- BEFORE Page Content -->

    <div class="page-section bg-primary border-bottom-2">
        <div class="container page__container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-md-6 mb-24pt mb-lg-0">
                            <p class="text-white-70 mb-0"><strong>Prepared for</strong></p>
                            <h2 class="text-white">{{ $data['invoice']->receiverUser->firstName }} {{ $data['invoice']->receiverUser->lastName }}</h2>
                            <p class="text-white-50">
                                {{ $data['invoice']->receiverUser->address }}
                                <br>{{ $data['invoice']->receiverUser->city->name }}
                                <br>{{ $data['invoice']->receiverUser->state->name }}
                                <br>{{ $data['invoice']->receiverUser->country->name }}
                                <br>{{ $data['invoice']->receiverUser->zipCode }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-white-70 mb-0"><strong>Prepared by</strong></p>
                            <h2 class="text-white">{{ $data['invoice']->academy->academyName }}</h2>
                            <p class="text-white-50">
                                {{ $data['invoice']->academy->address }}
                                <br>{{ $data['invoice']->academy->city->name }}
                                <br>{{ $data['invoice']->academy->state->name }}
                                <br>{{ $data['invoice']->academy->country->name }}
                                <br>{{ $data['invoice']->academy->zipCode }}
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 text-lg-right d-flex flex-lg-column mb-24pt mb-lg-0 border-bottom border-lg-0 pb-16pt pb-lg-0">
                    <div class="flex">
                        <p class="text-white-70 mb-8pt"><strong>Invoice {{ $data['invoice']->invoiceNumber }}</strong></p>
                        <p class="text-white-50">
                            {{ $data['createdDate'] }}
                        </p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                            <span class="material-icons ml-3">
                                keyboard_arrow_down
                            </span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a href="javascript:window.print()" class="dropdown-item" id="{{$data['invoice']->id}}">
                                <span class="material-icons">file_download</span>
                                Download Invoice
                            </a>
                            <form action="{{ route('invoices.restore', ['invoice' => $data['invoice']->id]) }}" method="POST">
                                @method('PATCH')
                                @csrf
                                <button type="button" class="dropdown-item restoreInvoice">
                                    <span class="material-icons text-success">restore</span>
                                    Restore Invoice
                                </button>
                            </form>
                            <form action="{{ route('invoices.permanent-delete', ['invoice' => $data['invoice']->id]) }}" method="POST">
                                @method('PATCH')
                                @csrf
                                <button type="button" class="dropdown-item forceDeleteInvoice">
                                    <span class="material-icons text-danger">delete</span>
                                    Permanently Delete Invoice
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- // END BEFORE Page Content -->

    <!-- Page Content -->

    <div class="page-section container page__container">
        <div class="page-separator">
            <div class="page-separator__text">Invoice Details</div>
        </div>

        <div class="card card-sm card-group-row__card">
            <div class="card-body flex-column">
                <div class="d-flex align-items-center">
                    <div class="p-2"><p class="card-title mb-4pt">Invoice Status :</p></div>
                    @if ($data['invoice']->status == 'Open')
                        <span class="ml-auto p-2 badge badge-pill badge-info">{{ $data['invoice']->status }}</span>
                    @elseif($data['invoice']->status == 'Paid')
                        <span class="ml-auto p-2 badge badge-pill badge-success">{{ $data['invoice']->status }}</span>
                    @elseif ($data['invoice']->status == 'Past Due')
                        <span class="ml-auto p-2 badge badge-pill badge-warning">{{ $data['invoice']->status }}</span>
                    @elseif ($data['invoice']->status == 'Uncollectible')
                        <span class="ml-auto p-2 badge badge-pill badge-danger">{{ $data['invoice']->status }}</span>
                    @endif
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ $data['createdAt'] }}</div>
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Due Date :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ $data['dueDate'] }}</div>
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Last updated at :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ $data['updatedAt'] }}</div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="p-2"><p class="card-title mb-4pt">Created by :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ $data['invoice']->creatorUser->firstName }} {{ $data['invoice']->creatorUser->lastName }}</div>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Invoice Summary</div>
        </div>

        <div class="card table-responsive mb-24pt">
            <table class="table table-flush table--elevated">
                <thead>
                    <tr>
                        <th>Products</th>
                        <th class="text-center">Qty</th>
                        <th style="width: 130px;"
                            class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['invoice']->products as $product)
                        <tr>
                            <td>
                                <p class="mb-0"><strong>{{ $product->productName }}</strong></p>
                            </td>
                            <td class="text-center"><strong>x {{ $product->pivot->qty }}</strong></td>
                            <td class="text-right"><strong>Rp. {{ number_format($product->pivot->ammount) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="table table-flush">
                <tfoot>
                <tr>
                    <td class="text-right text-70"><strong>Subtotal :</strong></td>
                    <td style="width: 130px;"
                        class="text-right"><strong>Rp. {{ number_format($data['invoice']->subtotal) }}</strong></td>
                </tr>
                @if($data['invoice']->tax)
                    <tr>
                        <td class="text-right text-70"><strong>Tax {{ $data['invoice']->tax->taxName }} ~ {{ $data['invoice']->tax->percentage }}% :</strong></td>
                        <td style="width: 130px;"
                            class="text-right"><strong>Rp. {{ number_format($data['invoice']->totalTax) }}</strong></td>
                    </tr>
                @endif
                <tr>
                    <td class="text-right text-70"><strong>Total :</strong></td>
                    <td style="width: 130px;"
                        class="text-right"><strong>Rp. {{ number_format($data['invoice']->ammountDue) }}</strong></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            // restore invoice
            $('.restoreInvoice').on('click', function () {
                Swal.fire({
                    title: "Are you sure to restore this invoice?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, restore it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(this).attr('action'),
                            type: $(this).attr('method'),
                            success: function () {
                                Swal.fire({
                                    title: 'Invoice successfully restored!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.href = '{{ route('invoices.index') }}';
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

            // force delete invoice
            $('.forceDeleteInvoice').on('click', function () {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure to permanently delete this invoice?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(this).attr('action'),
                            type: $(this).attr('method'),
                            success: function () {
                                Swal.fire({
                                    title: 'Invoice successfully permanently deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.href = '{{ route('invoices.index') }}';
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
