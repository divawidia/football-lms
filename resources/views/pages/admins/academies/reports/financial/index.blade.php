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
            <div class="col-lg-7 d-flex">
                <div class="card card-group-row__card">
                    <div class="card-body d-flex flex-row align-items-center flex-0">
                        <div class="h2 mb-0 mr-3" id="totalSubscription"></div>
                        <div class="flex">
                            <div class="card-title h5">player's subscription</div>
                        </div>
                        <div class="ml-3 align-self-start">
                            <div class="dropdown mb-2">
                                <a href="" class="dropdown-toggle" data-toggle="dropdown" data-caret="false">Filter by : <span id="filter-type-subscription">All Time</span><i class="material-icons text-50 pb-1">expand_more</i></a>
                                <div id="filterSubscription" class="dropdown-menu dropdown-menu-right">
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
                        <div class="col-4">
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Scheduled</strong></span>
                                    <span id="totalScheduled"></span>
                                </span>
                            </small>
                        </div>
                        <div class="col-4">
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Unsubcribed</strong></span>
                                    <span id="totalUnsubscribed"></span>
                                </span>
                            </small>
                        </div>
                        <div class="col-4">
                            <small class="d-flex align-items-start text-muted mb-2">
                                <span class="flex d-flex flex-column">
                                    <span class="text-body"><strong>Pending Payment</strong></span>
                                    <span id="totalPending"></span>
                                </span>
                            </small>
                        </div>
                    </div>
                    <div class="card-body text-muted flex d-flex flex-column align-items-center justify-content-center">
                        <canvas id="subscriptionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Invoices</div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="invoicesTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Inovice Number</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Due Date</th>
                            <th>Created At</th>
                            <th>Last Updated</th>
                            <th>Subtotal</th>
                            <th>Total Tax</th>
                            <th>Total Amount</th>
                            <th>Status</th>
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
            const invoiceStatusChart = document.getElementById('invoiceStatusChart');
            const paymentTypeChart = document.getElementById('paymentTypeChart');
            const subscriptionChart = document.getElementById('subscriptionChart');
            let subscriptionLineChart;

            $('#invoicesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('invoices.index') !!}'
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'invoiceNumber', name: 'invoiceNumber'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'dueDate', name: 'dueDate'},
                    {data: 'createdAt', name: 'createdAt'},
                    {data: 'updatedAt', name: 'updatedAt'},
                    {data: 'subtotal', name: 'subtotal'},
                    {data: 'totalTax', name: 'totalTax'},
                    {data: 'ammount', name: 'ammount'},
                    {data: 'status', name: 'status'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            function doughnutChart(labels, data, datasetLabel, chartId){
                return new Chart(chartId, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: datasetLabel,
                            data: data,
                            backgroundColor: ['#20F4CB', '#E52534', '#F9B300', '#00122A']
                        }]
                    },
                    options: {
                        responsive: true,
                    },
                });
            }

            doughnutChart(@json($paymentType['label']), @json($paymentType['data']), '# of Invoices', paymentTypeChart)
            doughnutChart(@json($invoiceStatus['label']), @json($invoiceStatus['data']), '# of Invoices', invoiceStatusChart)


            function fetchSubscriptionChartData(filter, filterText) {
                $.ajax({
                    url: '{{ route('admin.financial-report.subscription-chart-data') }}',
                    type: 'GET',
                    data: {
                        filter: filter,
                    },
                    success: function (response) {
                        if (subscriptionLineChart) subscriptionLineChart.destroy(); // Destroy previous chart instance
                        subscriptionLineChart = new Chart(subscriptionChart, {
                            type: 'line',
                            data: response.data.chart,
                            options: {
                                responsive: true,
                            },
                        });
                        $('#filter-type-subscription').text(filterText)
                        $('#totalSubscription').text(response.data.totalSubsbcription)
                        $('#totalScheduled').text(response.data.totalScheduled + ' Player(s)')
                        $('#totalUnsubscribed').text(response.data.totalUnsubscribed + ' Player(s)')
                        $('#totalPending').text(response.data.totalPending + ' Player(s)')
                    },
                    error: function (err) {
                        console.error(err);
                        alert('Failed to fetch chart data.');
                    },
                });
            }

            $('#filterSubscription .dropdown-item').on('click', function () {
                const filter = $(this).attr('id');
                const filterText = $(this).text();
                fetchSubscriptionChartData(filter, filterText);
            });

            fetchSubscriptionChartData('allTime');
        });
    </script>
@endpush
