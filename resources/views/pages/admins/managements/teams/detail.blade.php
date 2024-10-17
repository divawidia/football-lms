@extends('layouts.master')
@section('title')
    {{ $team->teamName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    @if(Auth::user()->hasRole('admin'))
                        <a href="{{ route('team-managements.index') }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Team Lists
                        </a>
                    @elseif(Auth::user()->hasRole('coach'))
                        <a href="{{ route('coach.team-managements.index') }}" class="nav-link text-70">
                            <i class="material-icons icon--left">keyboard_backspace</i>
                            Back to Team Lists
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($team->logo) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $team->teamName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $team->ageGroup }}</p>
            </div>

            @if(Auth::user()->hasRole('admin'))
                <div class="dropdown">
                    <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                        <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('team-managements.edit', $team->id) }}"><span class="material-icons">edit</span> Edit Team Profile</a>
                        @if($team->status == '1')
                            <form action="{{ route('deactivate-team', $team->id) }}" method="POST">
                                @method("PATCH")
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <span class="material-icons">block</span> Deactivate Team
                                </button>
                            </form>
                        @else
                            <form action="{{ route('activate-team', $team->id) }}" method="POST">
                                @method("PATCH")
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <span class="material-icons">check_circle</span> Activate Team
                                </button>
                            </form>
                        @endif
                        <button type="button" class="dropdown-item delete-team" id="{{$team->id}}">
                            <span class="material-icons">delete</span> Delete Team
                        </button>
                    </div>
                </div>
            @elseif(Auth::user()->hasRole('coach'))
                <a class="btn btn-outline-white">
                    <span class="material-icons mr-3">
                        edit
                    </span>
                    Action
                </a>
            @endif
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>
        <div class="row card-group-row mb-4">
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $overview['matchPlayed'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Match Played</div>
                                @if($overview['thisMonthMatchPlayed'] > 0)
                                    <p class="card-subtitle text-50">
                                        {{ $overview['thisMonthMatchPlayed'] }}
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Month
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $overview['goals'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Goals</div>
                                @if($overview['thisMonthGoals'] > 0)
                                    <p class="card-subtitle text-50">
                                        {{ $overview['thisMonthGoals'] }}
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Month
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $overview['goalsConceded'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">goals conceded</div>
                                @if($overview['thisMonthGoalsConceded'] > 0)
                                    <p class="card-subtitle text-50">
                                        {{ $overview['thisMonthGoalsConceded'] }}
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Month
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $overview['goalsDifference'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">goal difference</div>
                                @if($overview['thisMonthGoalDifference'] > 0)
                                    <p class="card-subtitle text-50">
                                        {{ $overview['thisMonthGoalDifference'] }}
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Month
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $overview['cleanSheets'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">clean sheets</div>
                                @if($overview['thisMonthCleanSheets'] > 0)
                                    <p class="card-subtitle text-50">
                                        {{ $overview['thisMonthCleanSheets'] }}
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Month
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $overview['ownGoals'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">own goals</div>
                                @if($overview['thisMonthOwnGoals'] > 0)
                                    <p class="card-subtitle text-50">
                                        {{ $overview['thisMonthOwnGoals'] }}
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Month
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $overview['wins'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">wins</div>
                                @if($overview['thisMonthWins'] > 0)
                                    <p class="card-subtitle text-50">
                                        {{ $overview['thisMonthWins'] }}
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Month
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $overview['losses'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">losses</div>
                                @if($overview['thisMonthLosses'] > 0)
                                    <p class="card-subtitle text-50">
                                        {{ $overview['thisMonthLosses'] }}
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Month
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 card-group-row__col flex-column mb-2">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">{{ $overview['draws'] }}</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">draws</div>
                                @if($overview['thisMonthDraws'] > 0)
                                    <p class="card-subtitle text-50">
                                        {{ $overview['thisMonthDraws'] }}
                                        <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                        From Last Month
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row card-group-row">
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Team Profile</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                            <div class="ml-auto p-2 text-muted">
                                @if ($team->status == '1')
                                    <span class="badge badge-pill badge-success">Aktif</span>
                                @elseif($team->status == '0')
                                    <span class="badge badge-pill badge-danger">Non Aktif</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Total Players :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ count($team->players) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Total Staffs/Coaches :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ count($team->coaches) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y. h:i A', strtotime($team->created_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y. h:i A', strtotime($team->updated_at)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Latest Match</div>
                </div>
                @if(count($latestMatches) == 0)
                    <div class="alert alert-light border-left-accent" role="alert">
                        <div class="d-flex flex-wrap align-items-center">
                            <i class="material-icons mr-8pt">error_outline</i>
                            <div class="media-body"
                                 style="min-width: 180px">
                                <small class="text-black-100">There are no latest matches record on this team</small>
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
                                        <h2 class="mb-0">{{ $match->teams[0]->pivot->teamScore }} - {{ $match->teams[1]->pivot->teamScore }}</h2>
                                    </div>
                                    <div class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
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
                                        {{ date('h:i A', strtotime($match->startTime)) }} - {{ date('h:i A', strtotime($match->endTime)) }}
                                    </div>
                                    <div>
                                        <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                                        {{ $match->place }}
                                    </div>
                                </div>
                            </div>
                        </a>
                @endforeach
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Players</div>
            <a href="{{ route('team-managements.addPlayerTeam', $team->id) }}" class="btn btn-primary ml-auto btn-sm">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="playersTable">
                        <thead>
                        <tr>
                            <th>Pos.</th>
                            <th>Name</th>
                            <th>Strong Foot</th>
                            <th>Age</th>
                            <th>Minutes Played</th>
                            <th>Apps</th>
                            <th>Goals</th>
                            <th>Assists</th>
                            <th>Own Goals</th>
                            <th>Shots</th>
                            <th>Passes</th>
                            <th>Fouls Conceded</th>
                            <th>Yellow Cards</th>
                            <th>Red Cards</th>
                            <th>Saves</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Coaches/Staffs</div>
            <a href="{{ route('team-managements.addCoachesTeam', $team->id) }}" class="btn btn-primary ml-auto btn-sm">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="coachesTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Joined Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Competitions/Events</div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="competitionsTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Group Division</th>
                            <th>Competition Date</th>
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Upcoming Matches</div>
        </div>
        @if(count($upcomingMatches) == 0)
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
        @foreach($upcomingMatches as $match)
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
                        <div class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
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
                            {{ date('h:i A', strtotime($match->startTime)) }} - {{ date('h:i A', strtotime($match->endTime)) }}
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
        </div>
        @if(count($upcomingTrainings) == 0)
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
            @foreach($upcomingTrainings as $training)
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
                                        {{ date('h:i A', strtotime($training->startTime)) }} - {{ date('h:i A', strtotime($training->endTime)) }}
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

        <div class="page-separator">
            <div class="page-separator__text">Match Histories</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="matchHistoryTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Opponent Team</th>
                            <th>competition</th>
                            <th>Match Date</th>
                            <th>Team Score</th>
                            <th>Opponent Team Score</th>
                            <th>Location</th>
                            <th>Note</th>
                            <th>Match Status</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Training Histories</div>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="trainingHistoryTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Training/Practice</th>
                            <th>training date</th>
                            <th>Location</th>
                            <th>Training Status</th>
                            <th>Note</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#playersTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    @if(Auth::user()->hasRole('admin'))
                        url: '{!! url()->route('team-managements.teamPlayers', $team->id) !!}',
                    @elseif(Auth::user()->hasRole('coach'))
                        url: '{!! url()->route('coach.team-managements.teamPlayers', $team->id) !!}',
                    @endif
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'strongFoot', name: 'strongFoot' },
                    { data: 'age', name: 'age'},
                    { data: 'minutesPlayed', name: 'minutesPlayed'},
                    { data: 'apps', name: 'apps'},
                    { data: 'goals', name: 'goals'},
                    { data: 'assists', name: 'assists'},
                    { data: 'ownGoals', name: 'ownGoals'},
                    { data: 'shots', name: 'shots'},
                    { data: 'passes', name: 'passes'},
                    { data: 'fouls', name: 'fouls'},
                    { data: 'yellowCards', name: 'yellowCards'},
                    { data: 'redCards', name: 'redCards'},
                    { data: 'saves', name: 'saves'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[5, 'desc']]
            });
            $('#coachesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    @if(Auth::user()->hasRole('admin'))
                        url: '{!! url()->route('team-managements.teamCoaches', $team->id) !!}',
                    @elseif(Auth::user()->hasRole('coach'))
                        url: '{!! url()->route('coach.team-managements.teamCoaches', $team->id) !!}',
                    @endif
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'age', name: 'age' },
                    { data: 'gender', name: 'gender' },
                    { data: 'joinedDate', name: 'joinedDate'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            $('#competitionsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    @if(Auth::user()->hasRole('admin'))
                        url: '{!! url()->route('team-managements.teamCompetitions', $team->id) !!}',
                    @elseif(Auth::user()->hasRole('coach'))
                        url: '{!! url()->route('coach.team-managements.teamCompetitions', $team->id) !!}',
                    @endif
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'divisions', name: 'divisions' },
                    { data: 'date', name: 'date'},
                    { data: 'location', name: 'location'},
                    { data: 'contact', name: 'contact' },
                    { data: 'status', name: 'status' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[3, 'desc']],
            });

            $('#trainingHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    @if(Auth::user()->hasRole('admin'))
                        url: '{!! url()->route('team-managements.training-histories', $team->id) !!}',
                    @elseif(Auth::user()->hasRole('coach'))
                        url: '{!! url()->route('coach.team-managements.training-histories', $team->id) !!}',
                    @endif
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'eventName', name: 'eventName' },
                    { data: 'date', name: 'date' },
                    { data: 'place', name: 'place'},
                    { data: 'status', name: 'status' },
                    { data: 'note', name: 'note' },
                    { data: 'last_updated', name: 'last_updated' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[2, 'desc']],
            });

            $('#matchHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    @if(Auth::user()->hasRole('admin'))
                        url: '{!! url()->route('team-managements.match-histories', $team->id) !!}',
                    @elseif(Auth::user()->hasRole('coach'))
                        url: '{!! url()->route('coach.team-managements.match-histories', $team->id) !!}',
                    @endif
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'opponentTeam', name: 'opponentTeam' },
                    { data: 'competition', name: 'competition' },
                    { data: 'date', name: 'date' },
                    { data: 'teamScore', name: 'teamScore' },
                    { data: 'opponentTeamScore', name: 'opponentTeamScore' },
                    { data: 'place', name: 'place' },
                    { data: 'status', name: 'status' },
                    { data: 'note', name: 'note' },
                    { data: 'last_updated', name: 'last_updated' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[2, 'desc']],
            });

            $('.delete-team').on('click', function() {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('team-managements.destroy', ['team' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Team successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('team-managements.index') }}";
                                    }
                                });
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Something went wrong when deleting data!",
                                });
                            }
                        });
                    }
                });
            });

            $('body').on('click', '.remove-player', function() {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, remove it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('team-managements.removePlayer', ['team' => $team->id, 'player' => ':id']) }}".replace(':id', id),
                            type: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Player successfully removed!",
                                });
                                playersTable.ajax.reload();
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Something went wrong when deleting data!",
                                });
                            }
                        });
                    }
                });
            });

            $('body').on('click', '.remove-coach', function() {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, remove it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('team-managements.removeCoach', ['team' => $team->id, 'coach' => ':id']) }}".replace(':id', id),
                            type: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Coach successfully removed!",
                                });
                                coachesTable.ajax.reload();
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Something went wrong when deleting data!",
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
