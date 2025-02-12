@extends('layouts.master')
@section('title')
    Invoices {{ $data->invoiceNumber }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('invoices.archived') }}" class="nav-link text-70"><i class="material-icons icon--left">keyboard_backspace</i> Back to Archived Invoices</a>
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
                            <h2 class="text-white">{{ $data->receiverUser->firstName }} {{ $data->receiverUser->lastName }}</h2>
                            <p class="text-white-50">
                                {{ $data->receiverUser->address }}
                                <br>{{ $data->receiverUser->city->name }}
                                <br>{{ $data->receiverUser->state->name }}
                                <br>{{ $data->receiverUser->country->name }}
                                <br>{{ $data->receiverUser->zipCode }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-white-70 mb-0"><strong>Prepared by</strong></p>
                            <h2 class="text-white">{{ $data->academy->academyName }}</h2>
                            <p class="text-white-50">
                                {{ $data->academy->address }}
                                <br>{{ $data->academy->city->name }}
                                <br>{{ $data->academy->state->name }}
                                <br>{{ $data->academy->country->name }}
                                <br>{{ $data->academy->zipCode }}
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 text-lg-right d-flex flex-lg-column mb-24pt mb-lg-0 border-bottom border-lg-0 pb-16pt pb-lg-0">
                    <div class="flex">
                        <p class="text-white-70 mb-8pt"><strong>Invoice {{ $data->invoiceNumber }}</strong></p>
                        <p class="text-white-50">{{ $data['createdDate'] }}</p>
                    </div>
                    <x-buttons.dropdown title="Action" icon="keyboard_arrow_down" btnColor="outline-white" iconMargin="ml-3">
                        <x-buttons.link-button :dropdownItem="true" href="javascript:window.print()" :id="$data->hash" icon="file_download" color="white" text="Download Invoice"/>
                        <x-buttons.basic-button icon="restore" iconColor="danger" color="white" text="Restore Invoice" additionalClass="restoreInvoice" :dropdownItem="true"/>
                        <x-buttons.basic-button icon="delete" iconColor="danger" color="white" text="Permanently Delete Invoice" additionalClass="forceDeleteInvoice" :dropdownItem="true"/>
                    </x-buttons.dropdown>
                </div>
            </div>
        </div>
    </div>

    <!-- // END BEFORE Page Content -->

    <!-- Page Content -->

    <div class="page-section container">
        <div class="page-separator">
            <div class="page-separator__text">Invoice Details</div>
        </div>

        <div class="card">
            <div class="card-body flex-column">
                <div class="d-flex align-items-center">
                    <div class="p-2"><p class="card-title mb-4pt">Invoice Status :</p></div>
                    @if ($data->status == 'Open')
                        <span class="ml-auto p-2 badge badge-pill badge-info">{{ $data->status }}</span>
                    @elseif($data->status == 'Paid')
                        <span class="ml-auto p-2 badge badge-pill badge-success">{{ $data->status }}</span>
                    @elseif ($data->status == 'Past Due')
                        <span class="ml-auto p-2 badge badge-pill badge-warning">{{ $data->status }}</span>
                    @elseif ($data->status == 'Uncollectible')
                        <span class="ml-auto p-2 badge badge-pill badge-danger">{{ $data->status }}</span>
                    @endif
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->created_at) }}</div>
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Due Date :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->dueDate) }}</div>
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Last updated at :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->updated_at) }}</div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="p-2"><p class="card-title mb-4pt">Created by :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ $data->creatorUser->firstName }} {{ $data->creatorUser->lastName }}</div>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Invoice Summary</div>
        </div>

        <div class="card">
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
                    @foreach($data->products as $product)
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
                        class="text-right"><strong>Rp. {{ number_format($data->subtotal) }}</strong></td>
                </tr>
                @if($data->tax)
                    <tr>
                        <td class="text-right text-70"><strong>Tax {{ $data->tax->taxName }} ~ {{ $data->tax->percentage }}% :</strong></td>
                        <td style="width: 130px;"
                            class="text-right"><strong>Rp. {{ number_format($data->totalTax) }}</strong></td>
                    </tr>
                @endif
                <tr>
                    <td class="text-right text-70"><strong>Total :</strong></td>
                    <td style="width: 130px;"
                        class="text-right"><strong>Rp. {{ number_format($data->ammountDue) }}</strong></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
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
