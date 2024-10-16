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
        <div class="page-separator">
            <div class="page-separator__text">Skill Stats</div>
        </div>
        <div class="card align-items-center">
            <div class="card-body">
                <canvas id="skillStatsChart" height="400"></canvas>
            </div>
        </div>

        {{--Skill Stats History Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Skill Stats History</div>
{{--            <div class="ml-auto mr-2 form-group">--}}
{{--                <label class="form-label mb-0" for="skill">Filter by skill</label>--}}
{{--                <select class="form-control form-select" id="skill" name="skill">--}}
{{--                    <option disabled selected>Select skill stats</option>--}}
{{--                    @foreach($skillLabels as $label => $value)--}}
{{--                        <option value="{{ $value }}">--}}
{{--                            {{ $label }}--}}
{{--                        </option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}
{{--            <div class="form-group mr-1">--}}
{{--                <label class="form-label mb-0" for="startDateFilter">Filter by date range</label>--}}
{{--                <input id="startDateFilter"--}}
{{--                    type="text"--}}
{{--                    class="form-control"--}}
{{--                    placeholder="Start Date"--}}
{{--                   onfocus="(this.type='date')"--}}
{{--                   onblur="(this.type='text')"/>--}}
{{--            </div>--}}
{{--            <div class="form-group">--}}
{{--                <label class="form-label mb-0" for="endDateFilter"></label>--}}
{{--                <input id="endDateFilter"--}}
{{--                       type="text"--}}
{{--                       class="form-control"--}}
{{--                       placeholder="End Date"--}}
{{--                       onfocus="(this.type='date')"--}}
{{--                       onblur="(this.type='text')"/>--}}
{{--            </div>--}}
        </div>
        <div class="card">
            <div class="card-body">
                <canvas id="skillStatsHistoryChart"></canvas>
            </div>
        </div>
{{--        <div class="row">--}}
{{--            <div class="col-sm-5">--}}
{{--                --}}{{--Skill Stats Radar Section--}}
{{--                <div class="page-separator">--}}
{{--                    <div class="page-separator__text">Skill Stats</div>--}}
{{--                </div>--}}
{{--                <div class="card">--}}
{{--                    <div class="card-body">--}}
{{--                        <canvas id="skillStatsChart"></canvas>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-sm-7">--}}
{{--                --}}{{--Skill Stats History Section--}}
{{--                <div class="page-separator">--}}
{{--                    <div class="page-separator__text">Skill Stats History</div>--}}
{{--                    <select class="ml-auto mr-2 form-control form-select" id="skill" name="skill">--}}
{{--                        <option disabled selected>Filter by skill</option>--}}
{{--                        @foreach($skillLabels as $label => $value)--}}
{{--                            <option value="{{ $value }}">--}}
{{--                                {{ $label }}--}}
{{--                            </option>--}}
{{--                        @endforeach--}}
{{--                    </select>--}}
{{--                    <input--}}
{{--                        id="startDateFilter"--}}
{{--                        type="date"--}}
{{--                        class="form-control"--}}
{{--                        placeholder="Input start date"--}}
{{--                    />--}}
{{--                </div>--}}
{{--                <div class="card">--}}
{{--                    <div class="card-body">--}}
{{--                        <canvas id="skillStatsHistoryChart"></canvas>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        {{--All Skill Stats Section--}}
        @if($allSkills == null)
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">This player has not added any skill stats yet</small>
                    </div>
                </div>
            </div>
        @else
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
        @endif
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
            const skillStatsChart = document.getElementById('skillStatsChart');
            const skillStatsHistoryChart = document.getElementById('skillStatsHistoryChart');

            // alert($('#rangeDateFilter').val())

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
                        @foreach($skillStatsHistory['data'] as $key => $value)
                            {
                                label: '{{ $key }}',
                                data: @json($value),
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
