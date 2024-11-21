@extends('layouts.master')
@section('title')
    Financial Report
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container d-flex flex-column">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page__container page-section">
        {{--    Overview    --}}
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body d-flex flex-row align-items-center">
                        <div class="h2 mb-0 mr-3">{{ $requireActionInvoice['totalRequireActionInvoice'] }}</div>
                        <div class="flex">
                            <div class="card-title h5">Requires Action Invoices</div>
                            <div class="card-subtitle text-50 d-flex align-items-center">
                                Rp. {{ $requireActionInvoice['sumRequireActionInvoice'] }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <small class="d-flex align-items-start text-muted mb-2">
                            <span class="flex d-flex flex-column">
                                <span class="text-body"><strong>Past Due</strong></span>
                                <span>{{ $requireActionInvoice['totalPastDueInvoices'] }} Invoices</span>
                            </span>
                            <span>Rp. {{ $requireActionInvoice['sumPastDueInvoices'] }}</span>
                        </small>
                        <small class="d-flex align-items-start text-muted">
                            <span class="flex d-flex flex-column">
                                <span class="text-body"><strong>Uncollectible</strong></span>
                                <span>{{ $requireActionInvoice['totalUncollectInvoices'] }} Invoices</span>
                            </span>
                            <span>Rp. {{ $requireActionInvoice['sumUncollectInvoices'] }}</span>
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-flex">
                <div class="card">
                    <div class="card-body d-flex flex-row align-items-center">
                        <div class="h2 mb-0 mr-3">Rp. {{ $recurringRevenue['mrr'] }}</div>
                        <div class="flex">
                            <div class="card-title h5">Monthly Estimated Recuring Revenue</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <small class="d-flex align-items-start text-muted mb-2">
                            <span class="flex d-flex flex-column">
                                <span class="text-body"><strong>Quarterly</strong></span>
                            </span>
                            <span>Rp. {{ $recurringRevenue['qrr'] }}</span>
                        </small>
                        <small class="d-flex align-items-start text-muted mb-2">
                            <span class="flex d-flex flex-column">
                                <span class="text-body"><strong>Yearly</strong></span>
                            </span>
                            <span>Rp. {{ $recurringRevenue['yrr'] }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <x-academy-revenue-chart :revenueGrowth="$revenueGrowth"/>
            </div>
            <div class="col-lg-5 d-flex">
                <div class="card">
                    <div class="card-body d-flex flex-row align-items-center flex-0">
                        <div class="card-title h5">INVOICE STATUS</div>
                    </div>
                    <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                        <canvas id="invoiceStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex flex-row align-items-center flex-0">
                        <div class="card-title h5">payments type</div>
                    </div>
                    <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                        <canvas id="paymentTypeChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex flex-row align-items-center flex-0">
                        <div class="h2 mb-0 mr-3" id="totalRevenue"></div>
                        <div class="flex">
                            <div class="card-title h5">new player's subscription</div>
                            <div class="card-subtitle text-50 d-flex align-items-center">
{{--                                {{ $dataOverview['revenueGrowth'] }}--}}
{{--                                @if($dataOverview['revenueGrowth'] > 0)--}}
{{--                                    <i class="material-icons text-success icon-16pt">keyboard_arrow_up</i>--}}
{{--                                @elseif($dataOverview['revenueGrowth'] < 0)--}}
{{--                                    <i class="material-icons text-danger icon-16pt">keyboard_arrow_up</i>--}}
{{--                                @endif--}}
                                From Last Month
                            </div>
                        </div>
                        <div class="ml-3 align-self-start">
                            <div class="dropdown mb-2">
                                <a href="" class="dropdown-toggle" data-toggle="dropdown" data-caret="false">Filter by : <span id="filter-type">All Time</span><i class="material-icons text-50 pb-1">expand_more</i></a>
                                <div id="filterRevenue" class="dropdown-menu dropdown-menu-right">
                                    <button type="button" class="dropdown-item" id="today">Today</button>
                                    <button type="button" class="dropdown-item" id="weekly">Weekly</button>
                                    <button type="button" class="dropdown-item" id="monthly">Monthly</button>
                                    <button type="button" class="dropdown-item" id="yearly">Yearly</button>
                                    <button type="button" class="dropdown-item" id="allTime">All Time</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            const invoiceStatusChart = document.getElementById('invoiceStatusChart');
            const paymentTypeChart = document.getElementById('paymentTypeChart');
            new Chart(invoiceStatusChart, {
                type: 'doughnut',
                data: {
                    labels: @json($invoiceStatus['label']),
                    datasets: [{
                        label: '# of Invoices',
                        data: @json($invoiceStatus['data']),
                        backgroundColor: ['#20F4CB', '#E52534', '#F9B300', '#00122A']
                    }]
                },
                options: {
                    responsive: true,
                },
            });
            new Chart(paymentTypeChart, {
                type: 'doughnut',
                data: {
                    labels: @json($paymentType['label']),
                    datasets: [{
                        label: '# of Invoices',
                        data: @json($paymentType['data']),
                        backgroundColor: ['#20F4CB', '#E52534', '#F9B300', '#00122A']
                    }]
                },
                options: {
                    responsive: true,
                },
            });
        });
    </script>
@endpush
