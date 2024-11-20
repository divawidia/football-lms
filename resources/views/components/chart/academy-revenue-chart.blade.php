<div class="card">
    <div class="card-body d-flex flex-row align-items-center flex-0">
        <div class="h2 mb-0 mr-3" id="totalRevenue"></div>
        <div class="flex">
            <div class="card-title h5">Total Revenue</div>
            <div class="card-subtitle text-50 d-flex align-items-center">
                {{ $revenueGrowth }}
                @if($revenueGrowth > 0)
                    <i class="material-icons text-success icon-16pt">keyboard_arrow_up</i>
                @elseif($revenueGrowth < 0)
                    <i class="material-icons text-danger icon-16pt">keyboard_arrow_up</i>
                @endif
                From Last Month
            </div>
        </div>
        <div class="ml-3 align-self-start">
            <div class="dropdown mb-2">
                <a href="" class="dropdown-toggle" data-toggle="dropdown" data-caret="false">Filter by : <span
                        id="filter-type">All Time</span><i class="material-icons text-50 pb-1">expand_more</i></a>
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

@push('addon-script')
    <script>
        $(document).ready(function () {
            const revenueChart = document.getElementById('revenueChart');
            let myChart;

            function fetchChartData(filter, filterText) {
                $.ajax({
                    url: '{{ route('admin.revenue-chart-data') }}',
                    type: 'GET',
                    data: {
                        filter: filter,
                    },
                    success: function (response) {
                        if (myChart) myChart.destroy(); // Destroy previous chart instance
                        myChart = new Chart(revenueChart, {
                            type: 'line',
                            data: response.data.chart,
                            options: {
                                responsive: true,
                            },
                        });
                        $('#filter-type').text(filterText)
                        $('#totalPaidInvoices').text(response.data.totalPaidInvoices + ' Invoices')
                        $('#totalPastDueInvoices').text(response.data.totalPastDueInvoices + ' Invoices')
                        $('#totalOpenInvoices').text(response.data.totalOpenInvoices + ' Invoices')
                        $('#totalUncollectInvoices').text(response.data.totalUncollectInvoices + ' Invoices')
                        $('#sumPaidInvoices').text('Rp. ' + response.data.sumPaidInvoices)
                        $('#sumPastDueInvoices').text('Rp. ' + response.data.sumPastDueInvoices)
                        $('#sumOpenInvoices').text('Rp. ' + response.data.sumOpenInvoices)
                        $('#sumUncollectInvoices').text('Rp. ' + response.data.sumUncollectInvoices)
                        $('#totalRevenue').text('Rp. ' + response.data.totalRevenue)
                    },
                    error: function (err) {
                        console.error(err);
                        alert('Failed to fetch chart data.');
                    },
                });
            }

            $('#filterRevenue .dropdown-item').on('click', function () {
                const filter = $(this).attr('id');
                const filterText = $(this).text();
                fetchChartData(filter, filterText);
            });

            fetchChartData('allTime');
        });
    </script>
@endpush
