@extends('layouts.master')
@section('title')
    {{ $data->user->firstName  }} {{ $data->user->lastName  }} Skill Stats
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-skill-assessments-modal/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    @if(isAllAdmin())
                        <a href="{{ route('player-managements.show', $data->hash) }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Player Profile
                        </a>
                    @elseif(isCoach())
                        <a href="{{ route('skill-assessments.index') }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Player Skill Assessments
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
                <x-charts.player-skill-stats-radar-chart :labels="$skillStats['label']" :datas="$skillStats['data']"
                                                  chartId="skillStatsChart"/>
            </div>
        </div>

        {{--All Skill Stats Section--}}
        <x-cards.player-skill-stats-card :allSkills="$allSkills"/>

        {{--Skill Stats History Section--}}
        <x-charts.player-skill-history-chart :player="$data"/>

    </div>
@endsection
