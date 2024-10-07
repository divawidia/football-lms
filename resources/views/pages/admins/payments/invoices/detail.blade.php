@extends('layouts.master')
@section('title')
    Invoices
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    @include('pages.admins.payments.invoices.form-modal.create')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="billing-history.html"
                       class="nav-link text-70"><i class="material-icons icon--left">keyboard_backspace</i> Back to Payment History</a>
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
                            <h2 class="text-white">Alexander Watson</h2>
                            <p class="text-white-50">640 Joy Bypass Suite 448<br>Germany</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-white-70 mb-0"><strong>Prepared by</strong></p>
                            <h2 class="text-white">Luma Inc.</h2>
                            <p class="text-white-50">32 Noah Cliffs Suite 626, Romania<br>Tax ID RO18880609</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 text-lg-right d-flex flex-lg-column mb-24pt mb-lg-0 border-bottom border-lg-0 pb-16pt pb-lg-0">
                    <div class="flex">
                        <p class="text-white-70 mb-8pt"><strong>Invoice</strong></p>
                        <p class="text-white-50">
                            15 Mar 2018<br>
                            10003578
                        </p>
                    </div>
                    <div><button class="btn btn-outline-white">Download <i class="material-icons icon--right">file_download</i></button></div>
                </div>
            </div>
        </div>
    </div>

    <!-- // END BEFORE Page Content -->

    <!-- Page Content -->

    <div class="page-section container page__container">
        <div class="page-separator">
            <div class="page-separator__text">Invoice Summary</div>
        </div>

        <div class="card table-responsive mb-24pt">
            <table class="table table-flush table--elevated">
                <thead>
                <tr>
                    <th>Description</th>
                    <th style="width: 60px;"
                        class="text-right">Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <p class="mb-0"><strong>Basic Plan - Monthly Subscription</strong></p>
                        <p class="text-50">For the period of June 20, 2018 to July 20, 2018</p>
                    </td>
                    <td class="text-right"><strong>&dollar;9</strong></td>
                </tr>
                <tr>
                    <td><strong>Credit discount</strong></td>
                    <td class="text-right"><strong>-&dollar;5</strong></td>
                </tr>
                </tbody>
            </table>

            <table class="table table-flush">
                <tfoot>
                <tr>
                    <td class="text-right text-70"><strong>Subtotal</strong></td>
                    <td style="width: 60px;"
                        class="text-right"><strong>&dollar;4</strong></td>
                </tr>
                <tr>
                    <td class="text-right text-70"><strong>Total</strong></td>
                    <td style="width: 60px;"
                        class="text-right"><strong>&dollar;4</strong></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="px-16pt">
            <p class="text-70 mb-8pt"><strong>Invoice paid</strong></p>
            <div class="media">
                <div class="media-left mr-16pt">
                    <img src="../../public/images/visa.svg"
                         alt="visa"
                         width="38" />
                </div>
                <div class="media-body text-50">
                    You don’t need to take further action. Your credit card Visa ending in 2819 has been charged on June 20, 2018.
                </div>
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {

        });
    </script>
@endpush
