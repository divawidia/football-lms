<canvas id="{{ $chartId }}"></canvas>

@push('addon-script')
    <script>
        $(document).ready(function () {
            const doughnutChart = document.getElementById('{{ $chartId }}');
            new Chart(doughnutChart, {
                type: 'doughnut',
                data: {
                    labels: @json($datas['label']),
                    datasets: [{
                        label: '# of Player',
                        data: @json($datas['data']),
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
