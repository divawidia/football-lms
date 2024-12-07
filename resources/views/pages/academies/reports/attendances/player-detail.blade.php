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
                        <a href="{{ route('attendance-report.index') }}" class="nav-link text-70"><i class="material-icons icon--left">keyboard_backspace</i> Back to Attendance Report</a>
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
                <h2 class="text-white mb-0">{{ $player->user->firstName  }} {{ $player->user->lastName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $player->position->name }}</p>
            </div>
{{--            <div class="dropdown">--}}
{{--                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                    Action--}}
{{--                            <span class="material-icons ml-3">--}}
{{--                                keyboard_arrow_down--}}
{{--                            </span>--}}
{{--                </button>--}}
{{--                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">--}}
{{--                    <a class="dropdown-item" href="{{ route('player-managements.edit', $user->id) }}"><span class="material-icons">edit</span> Edit Player</a>--}}
{{--                    @if($user->status == '1')--}}
{{--                        <form action="{{ route('deactivate-player', $user->id) }}" method="POST">--}}
{{--                            @method("PATCH")--}}
{{--                            @csrf--}}
{{--                            <button type="submit" class="dropdown-item">--}}
{{--                                <span class="material-icons">block</span> Deactivate Player--}}
{{--                            </button>--}}
{{--                        </form>--}}
{{--                    @else--}}
{{--                        <form action="{{ route('activate-player', $user->id) }}" method="POST">--}}
{{--                            @method("PATCH")--}}
{{--                            @csrf--}}
{{--                            <button type="submit" class="dropdown-item">--}}
{{--                                <span class="material-icons">check_circle</span> Activate Player--}}
{{--                            </button>--}}
{{--                        </form>--}}
{{--                    @endif--}}
{{--                    <a class="dropdown-item" href="{{ route('player-managements.change-password-page', $user->id) }}"><span class="material-icons">lock</span> Change Player Password</a>--}}
{{--                    <button type="button" class="dropdown-item delete-user" id="{{$user->id}}">--}}
{{--                        <span class="material-icons">delete</span> Delete Player--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>
        <div class="row card-group-row mb-4">
            @include('components.stats-card', ['title' => 'Total Attended','data' => $data['totalAttended'], 'dataThisMonth' => $data['thisMonthTotalAttended']])
            @include('components.stats-card', ['title' => 'Total Illness','data' => $data['totalIllness'], 'dataThisMonth' => $data['thisMonthTotalIllness']])
            @include('components.stats-card', ['title' => 'Total Injured','data' => $data['totalInjured'], 'dataThisMonth' => $data['thisMonthTotalInjured']])
            @include('components.stats-card', ['title' => 'Total Other','data' => $data['totalOther'], 'dataThisMonth' => $data['thisMonthTotalOther']])
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Training History</div>
        </div>
        <x-player-training-histories-table tableId="trainingHistoriesTable" :tableRoute="route('attendance-report.trainingTable', $player->id)"/>

        <div class="page-separator">
            <div class="page-separator__text">Match History</div>
        </div>
        <x-player-match-histories-table tableId="matchHistoriesTable" :tableRoute="route('attendance-report.matchDatatable', $player->id)"/>
    </div>
@endsection
