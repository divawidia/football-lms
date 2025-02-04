@extends('layouts.master')
@section('title')
    {{ $player->user->firstName  }} {{ $player->user->lastName  }} Event Attendance
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    @if(isAllAdmin() || isCoach())
        <nav class="navbar navbar-light border-bottom border-top px-0">
            <div class="container page__container">
                <ul class="nav navbar-nav">
                    <li class="nav-item">
                        <a href="{{ route('attendance-report.admin-coach-index') }}" class="nav-link text-70"><i
                                    class="material-icons icon--left">keyboard_backspace</i> Back to Attendance
                            Report</a>
                    </li>
                </ul>
            </div>
        </nav>
    @endif

    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($player->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ getUserFullName($player->user->firstName) }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $player->position->name }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="nav-tabs-container">
            <ul class="nav nav-pills text-capitalize">
                <x-tabs.item title="Training Attendance Overview" link="training-attendance" :active="true"/>
                <x-tabs.item title="Match Attendance Overview" link="match-attendance"/>
            </ul>
        </div>
    </div>

    <div class="container page-section">
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="training-attendance-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Training Attendance Overview</div>
                </div>
                <div class="row card-group-row mb-4">
                    @include('components.cards.stats-card', ['title' => 'Total Attended','data' => $data['totalAttended'], 'dataThisMonth' => $data['thisMonthTotalAttended']])
                    @include('components.cards.stats-card', ['title' => 'Total Illness','data' => $data['totalIllness'], 'dataThisMonth' => $data['thisMonthTotalIllness']])
                    @include('components.cards.stats-card', ['title' => 'Total Injured','data' => $data['totalInjured'], 'dataThisMonth' => $data['thisMonthTotalInjured']])
                    @include('components.cards.stats-card', ['title' => 'Total Other','data' => $data['totalOther'], 'dataThisMonth' => $data['thisMonthTotalOther']])
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">Training History</div>
                </div>
                <x-player-training-histories-table tableId="trainingHistoriesTable"
                                                   :tableRoute="route('attendance-report.trainingTable', $player->id)"/>

                <div class="page-separator">
                    <div class="page-separator__text">Match History</div>
                </div>
                <x-player-match-histories-table tableId="matchHistoriesTable"
                                                :tableRoute="route('attendance-report.matchDatatable', $player->id)"/>
            </div>

            <div class="tab-pane fade show" id="match-attendance-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Training Attendance Overview</div>
                </div>
                <div class="row card-group-row mb-4">
                    @include('components.cards.stats-card', ['title' => 'Total Attended','data' => $data['totalAttended'], 'dataThisMonth' => $data['thisMonthTotalAttended']])
                    @include('components.cards.stats-card', ['title' => 'Total Illness','data' => $data['totalIllness'], 'dataThisMonth' => $data['thisMonthTotalIllness']])
                    @include('components.cards.stats-card', ['title' => 'Total Injured','data' => $data['totalInjured'], 'dataThisMonth' => $data['thisMonthTotalInjured']])
                    @include('components.cards.stats-card', ['title' => 'Total Other','data' => $data['totalOther'], 'dataThisMonth' => $data['thisMonthTotalOther']])
                </div>

                <div class="page-separator">
                    <div class="page-separator__text">Training History</div>
                </div>
                <x-player-training-histories-table tableId="trainingHistoriesTable"
                                                   :tableRoute="route('attendance-report.trainingTable', $player->id)"/>

                <div class="page-separator">
                    <div class="page-separator__text">Match History</div>
                </div>
                <x-player-match-histories-table tableId="matchHistoriesTable"
                                                :tableRoute="route('attendance-report.matchDatatable', $player->id)"/>
            </div>
        </div>
    </div>
@endsection
