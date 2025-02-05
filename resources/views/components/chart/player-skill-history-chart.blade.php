<div class="page-separator">
    <div class="page-separator__text">Skill Stats History</div>
    <div class="form-group ml-auto">
        <label class="form-label mb-0" for="startDateFilter">Filter by date range</label>
        <input id="startDateFilter"
               type="text"
               class="form-control"
               placeholder="Start Date"
               onfocus="(this.type='date')"
               onblur="(this.type='text')"/>
    </div>
    <div class="form-group ml-2">
        <label class="form-label mb-0" for="endDateFilter"></label>
        <input id="endDateFilter"
               type="text"
               class="form-control"
               placeholder="End Date"
               onfocus="(this.type='date')"
               onblur="(this.type='text')"/>
    </div>
</div>
<div class="card">
    <div class="card-body" id="skillStatsHistoryCard">
        <canvas id="skillStatsHistoryChart"></canvas>
    </div>
</div>

@push('addon-script')
    <script>
        $(document).ready(function () {
            const skillStatsHistoryChart = $('#skillStatsHistoryChart');
            let myChart;

            function fetchChartData(startDate, endDate) {
                $.ajax({
                    url: '{{ route('player-managements.skill-stats-history', $player->hash) }}',
                    type: 'GET',
                    data: {
                        startDate: startDate,
                        endDate: endDate
                    },
                    success: function (response) {
                        if (myChart) myChart.destroy(); // Destroy previous chart instance
                        myChart = new Chart(skillStatsHistoryChart, {
                            type: 'line',
                            data: response.data,
                            options: {
                                responsive: true,
                            },
                        })
                    },
                    error: function (err) {
                        console.error(err);
                        alert('Failed to fetch chart data.');
                    },
                });
            }

            $('#startDateFilter').on('change', function () {
                const startDate = $(this).val();
                const endDate = $('#endDateFilter').val();
                fetchChartData(startDate, endDate);
            });
            $('#endDateFilter').on('change', function () {
                const startDate = $('#startDateFilter').val();
                const endDate = $(this).val();
                fetchChartData(startDate, endDate);
            });

            fetchChartData(null, null);
        });
    </script>
@endpush
