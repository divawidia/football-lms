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
                        <div class="h2 mb-0 mr-3" id="totalRevenue">{{ $requireActionInvoice['totalRequireActionInvoice'] }}</div>
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
                                <span id="totalPaidInvoices">{{ $requireActionInvoice['totalPastDueInvoices'] }} Invoices</span>
                            </span>
                            <span id="sumPaidInvoices">Rp. {{ $requireActionInvoice['sumPastDueInvoices'] }}</span>
                        </small>
                        <small class="d-flex align-items-start text-muted">
                            <span class="flex d-flex flex-column">
                                <span class="text-body"><strong>Uncollectible</strong></span>
                                <span id="totalPastDueInvoices">{{ $requireActionInvoice['totalUncollectInvoices'] }} Invoices</span>
                            </span>
                            <span id="sumPastDueInvoices">Rp. {{ $requireActionInvoice['sumUncollectInvoices'] }}</span>
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-flex">
                <div class="card">
                    <div class="card-body d-flex flex-row align-items-center">
                        <div class="h2 mb-0 mr-3" id="totalRevenue">Rp. {{ $recurringRevenue['mrr'] }}</div>
                        <div class="flex">
                            <div class="card-title h5">Monthly Est. Recuring Revenue</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <small class="d-flex align-items-start text-muted mb-2">
                            <span class="flex d-flex flex-column">
                                <span class="text-body"><strong>Quarterly</strong></span>
                            </span>
                            <span id="sumPaidInvoices">Rp. {{ $recurringRevenue['qrr'] }}</span>
                        </small>
                        <small class="d-flex align-items-start text-muted mb-2">
                            <span class="flex d-flex flex-column">
                                <span class="text-body"><strong>Yearly</strong></span>
                            </span>
                            <span id="sumPastDueInvoices">Rp. {{ $recurringRevenue['yrr'] }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex flex-row align-items-center flex-0">
                        <div class="h2 mb-0 mr-3" id="totalRevenue"></div>
                        <div class="flex">
                            <div class="card-title h5">Total Revenue</div>
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
                    <div class="card-body flex-0 row">
                        <div class="col-6">
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Paid</strong></span>
                                    <span id="totalPaidInvoices"></span>
                                </span>
                                <span class="mx-3" id="sumPaidInvoices"></span>
                            </small>
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Past Due</strong></span>
                                    <span id="totalPastDueInvoices"></span>
                                </span>
                                <span class="mx-3" id="sumPastDueInvoices"></span>
                            </small>
                        </div>
                        <div class="col-6">
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Open</strong></span>
                                    <span id="totalOpenInvoices"></span>
                                </span>
                                <span class="mx-3" id="sumOpenInvoices"></span>
                            </small>
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Uncollectible</strong></span>
                                    <span id="totalUncollectInvoices"></span>
                                </span>
                                <span class="mx-3" id="sumUncollectInvoices"></span>
                            </small>
                        </div>
                    </div>
                    <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex flex-row align-items-center flex-0">
                        <div class="card-title h5">INVOICE STATUS</div>
                    </div>
                    <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                        <canvas id="teamAgeChart"></canvas>
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
                        <canvas id="teamAgeChart"></canvas>
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

    </script>
@endpush
