@extends('layouts.master')
@section('title')
    {{ $data->user->firstName  }} {{ $data->user->lastName  }} Skill Stats
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('coach.player-managements.show', $data->id) }}" class="nav-link text-70">
                        <i class="material-icons icon--left">keyboard_backspace</i>
                        Back to Player Profile
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="player-photo">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $data->user->firstName  }} {{ $data->user->lastName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player - {{ $data->position->name }}</p>
            </div>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="row card-group-row">
            <div class="col-sm-4 card-group-row__col flex-column">
                {{--Skill Stats Radar Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <canvas id="skillStatsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-sm-8 card-group-row__col flex-column">
                {{--Skill Stats History Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats History</div>
                </div>
                <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                    <div class="card-body flex-column">
                        <canvas id="skillStatsHistoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
            const skillStatsChart = document.getElementById('skillStatsChart');
            const skillStatsHistoryChart = document.getElementById('skillStatsHistoryChart');

            new Chart(skillStatsChart, {
                type: 'radar',
                data: {
                    labels: @json($skillStats['label']),
                    datasets: [{
                        label: 'Skill Stats',
                        data: @json($skillStats['data']),
                        borderColor: '#E52534',
                        backgroundColor: 'rgba(229, 37, 52, 0.5)',
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

            new Chart(skillStatsHistoryChart, {
                type: 'line',
                data: {
                    labels: @json($skillStatsHistory['label']),
                    datasets: [
{{--                        @dd($skillStatsHistory['data'])--}}
                        @foreach($skillStatsHistory['data']['label'] as $data)
                            {
                                label: '{{ $data }}',
                                data: @json($skillStatsHistory['data'][$data]),
                                // borderColor: '#20F4CB',
                                tension: 0.4,
                            },
                        @endforeach
                    ]
                },
                options: {
                    responsive: true,
                },
            });
        });
    </script>
@endpush
