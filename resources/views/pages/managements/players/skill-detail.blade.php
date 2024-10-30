@extends('layouts.master')
@section('title')
    {{ $data->user->firstName  }} {{ $data->user->lastName  }} Skill Stats
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-skill-assessments-modal :route="route('skill-assessments.store', $data->id)"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    @if(isAllAdmin() || isCoach())
                        <a href="{{ route('player-managements.show', $data->id) }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Player Profile
                        </a>
                    @elseif(isPlayer())
                        <a href="{{ route('player.dashboard') }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Dashboard
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div
                class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="player-photo">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $data->user->firstName  }} {{ $data->user->lastName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player - {{ $data->position->name }}</p>
            </div>
            @if(isCoach())
                <a class="btn btn-outline-white addSkills" id="{{ $data->id }}" href="">
                    <span class="material-icons mr-2">edit</span>
                    Update Skills
                </a>
            @endif
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Skill Stats</div>
        </div>
        <div class="card align-items-center">
            <div class="card-body">
                <x-player-skill-stats-radar-chart :labels="$skillStats['label']" :datas="$skillStats['data']"
                                                  chartId="skillStatsChart"/>
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

        {{--All Skill Stats Section--}}
        <x-player-skill-stats-card :allSkills="$allSkills"/>
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            const skillStatsHistoryChart = $('#skillStatsHistoryChart');
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
