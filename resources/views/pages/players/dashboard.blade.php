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
            <div class="page-separator__text">Joined Teams</div>
        </div>

        <div class="row">
            @foreach($teams as $team)
                <div class="col-lg-6">
                    <a class="card" href="">
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
                </div>
            @endforeach
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
            {{--            <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>--}}
        </div>

        <div class="row card-group-row mb-4">
            @include('components.stats-card', ['title' => 'Match Played','data' => $overview['matchPlayed'], 'dataThisMonth' => $overview['thisMonthMatchPlayed']])
            @include('components.stats-card', ['title' => 'Minutes Played','data' => $overview['minutesPlayed'], 'dataThisMonth' => $overview['thisMonthMinutesPlayed']])
            @include('components.stats-card', ['title' => 'Fouls','data' => $overview['fouls'], 'dataThisMonth' => $overview['thisMonthFouls']])
            @if($data->position == 'Goalkeeper (GK)')
                @include('components.stats-card', ['title' => 'Saves','data' => $overview['saves'], 'dataThisMonth' => $overview['thisMonthSaves']])
            @else
                @include('components.stats-card', ['title' => 'Goals','data' => $overview['goals'], 'dataThisMonth' => $overview['thisMonthGoals']])
            @endif
            @include('components.stats-card', ['title' => 'Assists','data' => $overview['assists'], 'dataThisMonth' => $overview['thisMonthAssists']])
            @include('components.stats-card', ['title' => 'Own Goals','data' => $overview['ownGoals'], 'dataThisMonth' => $overview['thisMonthOwnGoals']])
            @include('components.stats-card', ['title' => 'Wins','data' => $overview['wins'], 'dataThisMonth' => $overview['thisMonthWins']])
            @include('components.stats-card', ['title' => 'Losses','data' => $overview['losses'], 'dataThisMonth' => $overview['thisMonthLosses']])
            @include('components.stats-card', ['title' => 'Draws','data' => $overview['draws'], 'dataThisMonth' => $overview['thisMonthDraws']])
        </div>

        <div class="row">
            <div class="col-sm-6 flex-column">
                {{--Teams Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">Skill Stats</div>
                    <a href="{{ route('player-managements.skill-stats', $data->id) }}"
                       class="btn btn-white border btn-sm ml-auto">
                        View More
                        <span class="material-icons ml-2 icon-16pt">chevron_right</span>
                    </a>
                </div>
                <div class="card">
                    <x-player-skill-stats-radar-chart :labels="$playerSkillStats['label']" :datas="$playerSkillStats['data']" chartId="uniqueChartId"/>
                </div>
            </div>
            <div class="col-sm-6 flex-column">
                {{--performance review Section--}}
                <div class="page-separator">
                    <div class="page-separator__text">performance review</div>
                </div>
                @if(count($performanceReviews)==0)
                    <div class="alert alert-light border-left-accent" role="alert">
                        <div class="d-flex flex-wrap align-items-center">
                            <i class="material-icons mr-8pt">error_outline</i>
                            <div class="media-body"
                                 style="min-width: 180px">
                                <small class="text-black-100">You haven't added any note performance review to this
                                    player
                                    yet</small>
                            </div>
                        </div>
                    </div>
                @endif
                @foreach($performanceReviews as $review)
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="flex">
                                <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($review->created_at)) }}</h4>
                                <div class="card-subtitle text-50">
                                    Last updated at {{ date('D, M d Y h:i A', strtotime($review->updated_at)) }}
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">
                                    more_vert
                                </span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item edit-note" id="{{ $review->id }}" href="">
                                        <span class="material-icons">edit</span>
                                        Edit Note
                                    </a>
                                    <button type="button" class="dropdown-item delete-note" id="{{ $review->id }}">
                                        <span class="material-icons">delete</span>
                                        Delete Note
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                echo $review->performanceReview
                            @endphp
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{--Latest Training Section--}}
        <div class="page-separator">
            <div class="page-separator__text">Latest Trainings</div>
        </div>
        @if(count($latestTrainings) == 0)
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">There are no latest matches at this momment</small>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            @foreach($latestTrainings as $training)
                <div class="col-md-6">
                    <a class="card" href="{{ route('training-schedules.show', $training->id) }}">
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
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">There are no latest matches at this momment</small>
                    </div>
                </div>
            </div>
        @endif
        @foreach($latestMatches as $match)
            <a class="card" href="{{ route('match-schedules.show', $match->id) }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                            <img src="{{ Storage::url($match->teams[0]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="team-logo">
                            <div class="ml-md-3 text-center text-md-left">
                                <h6 class="mb-0">{{ $match->teams[0]->teamName }}</h6>
                                <p class="text-50 lh-1 mb-0">{{ $match->teams[0]->ageGroup }}</p>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h2 class="mb-0">{{ $match->teams[0]->pivot->teamScore }}
                                - {{ $match->teams[1]->pivot->teamScore }}</h2>
                        </div>
                        <div
                            class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                            <div class="mr-md-3 text-center text-md-right">
                                <h6 class="mb-0">{{ $match->teams[1]->teamName }}</h6>
                                <p class="text-50 lh-1 mb-0">{{ $match->teams[1]->ageGroup }}</p>
                            </div>
                            <img src="{{ Storage::url($match->teams[1]->logo) }}"
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
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">There are no matches scheduled at this time</small>
                    </div>
                </div>
            </div>
        @endif
        @foreach($overview['upcomingMatches'] as $match)
            <a class="card" href="{{ route('match-schedules.show', $match->id) }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                            <img src="{{ Storage::url($match->teams[0]->logo) }}"
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="team-logo">
                            <div class="ml-md-3 text-center text-md-left">
                                <h5 class="mb-0">{{$match->teams[0]->teamName}}</h5>
                                <p class="text-50 lh-1 mb-0">{{$match->teams[0]->ageGroup}}</p>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h2 class="mb-0">Vs.</h2>
                        </div>
                        <div
                            class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                            <div class="mr-md-3 text-center text-md-right">
                                <h5 class="mb-0">{{ $match->teams[1]->teamName }}</h5>
                                <p class="text-50 lh-1 mb-0">{{$match->teams[1]->ageGroup}}</p>
                            </div>
                            <img src="{{ Storage::url($match->teams[1]->logo) }}"
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
            <div class="alert alert-light border-left-accent" role="alert">
                <div class="d-flex flex-wrap align-items-center">
                    <i class="material-icons mr-8pt">error_outline</i>
                    <div class="media-body"
                         style="min-width: 180px">
                        <small class="text-black-100">There are no trainings scheduled at this time</small>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            @foreach($overview['upcomingTrainings'] as $training)
                <div class="col-lg-6">
                    <a class="card" href="{{ route('training-schedules.show', $training->id) }}">
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
