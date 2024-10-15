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
        <div class="row">
            <div class="col-sm-5">
                {{--Skill Stats Radar Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="skillStatsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                {{--Skill Stats History Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats History</div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <canvas id="skillStatsHistoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-Header d-flex align-items-center p-3">
                <h4 class="card-title">SKILLS</h4>
                <div class="card-subtitle text-50 ml-auto">Last updated at {{ date('D, M d Y h:i A', strtotime($allSkills->updated_at)) }}</div>
            </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Controlling</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->controlling }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Receiving</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->recieving }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Dribbling</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->dribbling }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Passing</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->passing }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Shooting</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->shooting }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Crossing</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->crossing }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Turning</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->turning }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Ball Handling</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->ballHandling }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Power Kicking</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->powerKicking }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Goal Keeping</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->goalKeeping }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Offensive Play</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->offensivePlay }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <strong class="flex">Defensive Play</strong>
                            </div>
                            <div class="col-9">
                                <div class="flex" style="max-width: 100%">
                                    <div class="progress"
                                         style="height: 8px;">
                                        <div class="progress-bar bg-primary"
                                             role="progressbar"
                                             style="width: {{ $allSkills->defensivePlay }}%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
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
                        @foreach($skillStatsHistory['data']['label'] as $data)
                            {
                                label: '{{ $data }}',
                                data: @json($skillStatsHistory['data'][$data]),
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
