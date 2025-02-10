<canvas id="{{ $chartId }}"></canvas>

@push('addon-script')
    <script>
        $(document).ready(function () {
            const lineChart = document.getElementById('{{ $chartId }}');
            new Chart(lineChart, {
                type: 'line',
                data: {
                    labels: @json($datas['labels']),
                    datasets: [{
                        label: 'Attended Player',
                        data: @json($datas['attended']),
                        borderColor: '#20F4CB',
                        tension: 0.4,
                    }, {
                        label: 'Didnt Attend Player',
                        data: @json($datas['didntAttend']),
                        borderColor: '#E52534',
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                },
            });
        });
    </script>
@endpush
