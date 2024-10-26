<canvas id="{{ $chartId }}"></canvas>

@push('addon-script')
    <script>
        $(document).ready(function () {
            const skillStatsChart = document.getElementById('{{ $chartId }}');
            new Chart(skillStatsChart, {
                type: 'radar',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Skill Stats',
                        data: @json($datas),
                        borderColor: '{{ $borderColor ?? '#E52534' }}',
                        backgroundColor: '{{ $backgroundColor ?? 'rgba(229, 37, 52, 0.5)' }}',
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        r: {
                            angleLines: {
                                display: false
                            },
                            suggestedMin: 0,
                            suggestedMax: 100
                        }
                    }
                },
            });
        });
    </script>
@endpush
