@extends('layouts.master')
@section('title')
    Dashboard
@endsection
@section('page-title')
    Dashboard
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('player.dashboard') }}">Home</a></li>
            </ol>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
            {{--            <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>--}}
        </div>

        <div class="row card-group-row mb-4">
            @include('components.cards.stats-card', ['title' => 'Match Played','data' => $overview['matchPlayed'], 'dataThisMonth' => $overview['thisMonthMatchPlayed']])
            @include('components.cards.stats-card', ['title' => 'Minutes Played','data' => $overview['statsData']['minutesPlayed'], 'dataThisMonth' => $overview['statsData']['minutesPlayedThisMonth']])
            @include('components.cards.stats-card', ['title' => 'Fouls','data' => $overview['statsData']['fouls'], 'dataThisMonth' => $overview['statsData']['foulsThisMonth']])
            @if($data->position == 'Goalkeeper (GK)')
                @include('components.cards.stats-card', ['title' => 'Saves','data' => $overview['statsData']['saves'], 'dataThisMonth' => $overview['statsData']['savesThisMonth']])
            @else
                @include('components.cards.stats-card', ['title' => 'Goals','data' => $overview['statsData']['goals'], 'dataThisMonth' => $overview['statsData']['goalsThisMonth']])
            @endif
            @include('components.cards.stats-card', ['title' => 'Assists','data' => $overview['statsData']['assists'], 'dataThisMonth' => $overview['statsData']['assistsThisMonth']])
            @include('components.cards.stats-card', ['title' => 'Own Goals','data' => $overview['statsData']['ownGoal'], 'dataThisMonth' => $overview['statsData']['ownGoalThisMonth']])
            @include('components.cards.stats-card', ['title' => 'Wins','data' => $overview['statsData']['Win'], 'dataThisMonth' => $overview['statsData']['WinThisMonth']])
            @include('components.cards.stats-card', ['title' => 'Losses','data' => $overview['statsData']['Lose'], 'dataThisMonth' => $overview['statsData']['LoseThisMonth']])
            @include('components.cards.stats-card', ['title' => 'Draws','data' => $overview['statsData']['Draw'], 'dataThisMonth' => $overview['statsData']['DrawThisMonth']])
        </div>

        <div class="row">
            <div class="col-sm-6 flex-column">
                {{--Skill stats Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats</div>
                    <a href="{{ route('player.skill-stats') }}"
                       class="btn btn-white border btn-sm ml-auto">
                        View More
                        <span class="material-icons ml-2 icon-16pt">chevron_right</span>
                    </a>
                </div>
                <div class="card">
                    <x-player-skill-stats-radar-chart :labels="$playerSkillStats['label']"
                                                      :datas="$playerSkillStats['data']" chartId="uniqueChartId"/>
                </div>
            </div>

            <div class="col-sm-6 flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Joined Teams</div>
                </div>

                @foreach($teams as $team)
                    <a class="card" href="{{ route('team-managements.show', ['team'=>$team->hash]) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 d-flex flex-column flex-md-row align-items-center">
                                    <img src="{{ Storage::url($team->logo) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="team-logo">
                                    <div class="ml-md-3 text-center text-md-left">
                                        <h5 class="mb-0">{{$team->teamName}}</h5>
                                        <p class="text-50 lh-1 mb-0">{{$team->ageGroup}}</p>
                                    </div>
                                </div>
                                <div class="col-6 d-flex flex-column justify-content-center align-items-end">
                                    <div>
                                        <i class='fa fa-users icon-16pt text-danger mr-2'></i>
                                        {{ $team->players()->count() }} Players
                                    </div>
                                    <div>
                                        <i class="fa fa-user-tie icon-16pt text-danger mr-2"></i>
                                        {{ $team->coaches()->count() }} Coaches
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{--Latest Training Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Latest Trainings</div>
        </div>
        @if(count($latestTrainings) == 0)
            <x-warning-alert text="There are no latest trainings at this moment"/>
        @endif
        <div class="row">
            @foreach($latestTrainings as $training)
                <div class="col-md-6">
                    <a class="card" href="{{ route('training-schedules.show', $training->hash) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 d-flex flex-column flex-md-row align-items-center">
                                    <img src="{{ Storage::url($training->teams[0]->logo) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="team-logo">
                                    <div class="ml-md-3 text-center text-md-left">
                                        <h5 class="mb-0">{{$training->teams[0]->teamName}}</h5>
                                        <p class="text-50 lh-1 mb-0">{{$training->teams[0]->ageGroup}}</p>
                                    </div>
                                </div>
                                <div class="col-6 d-flex flex-column">
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">event</i>
                                        {{ date('D, M d Y', strtotime($training->date)) }}
                                    </div>
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                                        {{ date('h:i A', strtotime($training->startTime)) }}
                                        - {{ date('h:i A', strtotime($training->endTime)) }}
                                    </div>
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                        {{ $training->place }}
                                    </div>
                                    <div>
                                        @if($training->pivot->attendanceStatus == 'Attended')
                                            <i class="material-icons text-success icon--left icon-16pt">check_circle</i>
                                        @else
                                            <i class="material-icons text-danger icon--left icon-16pt">cancel</i>
                                        @endif
                                        {{ $training->pivot->attendanceStatus }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>


        <div class="page-separator">
            <div class="page-separator__text">Latest Match</div>
        </div>
        @if(count($latestMatches) == 0)
            <x-warning-alert text="There are no latest matches at this moment"/>
        @endif
        @foreach($latestMatches as $match)
            @php
                $homeTeam = $match->teams()->where('teamId', $match->homeTeamId)->first();
                if ($match->matchType == 'Internal Match') {
                    $awayTeam = $match->teams()->where('teamId', $match->awayTeamId)->first();
                } else {
                    $awayTeam = $match->externalTeam;
                }
            @endphp
            <a class="card" href="{{ route('match-schedules.show', $match->hash) }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                            <img src="{{ Storage::url($match->homeTeam->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="team-logo">
                            <div class="ml-md-3 text-center text-md-left">
                                <h6 class="mb-0">{{ $match->homeTeam->teamName }}</h6>
                                <p class="text-50 lh-1 mb-0">{{ $match->homeTeam->ageGroup }}</p>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h2 class="mb-0">
                                {{ $homeTeam->pivot->teamScore }}
                                -
                                @if($match->matchType == 'Internal Match')
                                    {{ $awayTeam->pivot->teamScore }}
                                @else
                                    {{ $awayTeam->teamScore }}
                                @endif
                            </h2>
                        </div>
                        <div class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                            @if($match->matchType == 'Internal Match')
                                <div class="mr-md-3 text-center text-md-right">
                                    <h5 class="mb-0">{{ $match->awayTeam->teamName }}</h5>
                                    <p class="text-50 lh-1 mb-0">{{$match->awayTeam->ageGroup}}</p>
                                </div>
                                <img src="{{ Storage::url($match->awayTeam->logo) }}"
                                     width="50"
                                     height="50"
                                     class="rounded-circle img-object-fit-cover"
                                     alt="team-logo">
                            @else
                                <div class="mr-md-3 text-center text-md-right">
                                    <h5 class="mb-0">{{ $match->externalTeam->teamName }}</h5>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row justify-content-center mt-3">
                        <div class="mr-2">
                            <i class="material-icons text-danger icon--left icon-16pt">event</i>
                            {{ date('D, M d Y', strtotime($match->date)) }}
                        </div>
                        <div class="mr-2">
                            <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                            {{ date('h:i A', strtotime($match->startTime)) }}
                            - {{ date('h:i A', strtotime($match->endTime)) }}
                        </div>
                        <div class="mr-2">
                            <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                            {{ $match->place }}
                        </div>
                        <div>
                            @if($match->pivot->attendanceStatus == 'Attended')
                                <i class="material-icons text-success icon--left icon-16pt">check_circle</i>
                            @else
                                <i class="material-icons text-danger icon--left icon-16pt">cancel</i>
                            @endif
                            {{ $match->pivot->attendanceStatus }}
                        </div>
                    </div>
                </div>
            </a>
        @endforeach

        {{--performance review Section--}}
        <div class="page-separator">
            <div class="page-separator__text">player performance review</div>
        </div>
        <x-player-performance-review-table
                :route="route('player-managements.performance-reviews', ['player' => $data->id])"
                tableId="performanceReviewTable"/>

        {{--Parents/Guardians Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Parents/Guardians</div>
        </div>
        <x-player-parents-tables :player="$data->id"/>

        <div class="page-separator">
            <div class="page-separator__text">Upcoming Matches</div>
            <a href="{{ route('match-schedules.index') }}" class="btn border btn-white btn-sm ml-auto">
                View More
                <span class="material-icons ml-2 icon-16pt">chevron_right</span>
            </a>
        </div>
        @if(count($overview['upcomingMatches']) == 0)
            <x-warning-alert text="There are no matches scheduled at this moment"/>
        @endif
        @foreach($overview['upcomingMatches'] as $match)
            <a class="card" href="{{ route('match-schedules.show', $match->hash) }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                            <img src="{{ Storage::url($match->homeTeam->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="team-logo">
                            <div class="ml-md-3 text-center text-md-left">
                                <h5 class="mb-0">{{$match->homeTeam->teamName}}</h5>
                                <p class="text-50 lh-1 mb-0">{{$match->homeTeam->ageGroup}}</p>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h2 class="mb-0">Vs.</h2>
                        </div>
                        <div
                                class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                            <div class="mr-md-3 text-center text-md-right">
                                <h5 class="mb-0">{{ $match->awayTeam->teamName }}</h5>
                                <p class="text-50 lh-1 mb-0">{{$match->awayTeam->ageGroup}}</p>
                            </div>
                            <img src="{{ Storage::url($match->awayTeam->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="team-logo">
                        </div>
                    </div>

                    <div class="row justify-content-center mt-3">
                        <div class="mr-2">
                            <i class="material-icons text-danger icon--left icon-16pt">event</i>
                            {{ date('D, M d Y', strtotime($match->date)) }}
                        </div>
                        <div class="mr-2">
                            <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                            {{ date('h:i A', strtotime($match->startTime)) }}
                            - {{ date('h:i A', strtotime($match->endTime)) }}
                        </div>
                        <div>
                            <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                            {{ $match->place }}
                        </div>
                    </div>
                </div>
            </a>
        @endforeach

        <div class="page-separator">
            <div class="page-separator__text">Upcoming Training</div>
            <a href="{{ route('training-schedules.index') }}" class="btn border btn-white btn-sm ml-auto">
                View More
                <span class="material-icons ml-2 icon-16pt">chevron_right</span>
            </a>
        </div>
        @if(count($overview['upcomingTrainings']) == 0)
            <x-warning-alert text="There are no trainings scheduled at this moment"/>
        @endif
        <div class="row">
            @foreach($overview['upcomingTrainings'] as $training)
                <div class="col-lg-6">
                    <a class="card" href="{{ route('training-schedules.show', $training->hash) }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 d-flex flex-column flex-md-row align-items-center">
                                    <img src="{{ Storage::url($training->teams[0]->logo) }}"
                                         width="50"
                                         height="50"
                                         class="rounded-circle img-object-fit-cover"
                                         alt="team-logo">
                                    <div class="ml-md-3 text-center text-md-left">
                                        <h5 class="mb-0">{{$training->teams[0]->teamName}}</h5>
                                        <p class="text-50 lh-1 mb-0">{{$training->teams[0]->ageGroup}}</p>
                                    </div>
                                </div>
                                <div class="col-6 d-flex flex-column">
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">event</i>
                                        {{ date('D, M d Y', strtotime($training->date)) }}
                                    </div>
                                    <div class="mr-2">
                                        <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                                        {{ date('h:i A', strtotime($training->startTime)) }}
                                        - {{ date('h:i A', strtotime($training->endTime)) }}
                                    </div>
                                    <div>
                                        <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                        {{ $training->place }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
