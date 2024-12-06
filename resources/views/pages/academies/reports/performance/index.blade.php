@extends('layouts.master')
@section('title')
    Performance Report
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container d-flex flex-column">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page__container page-section">
        {{--    Overview    --}}
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
            {{--                <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>--}}
        </div>

        <div class="row mb-3">
            @if(isAllAdmin() || isCoach())
                @include('components.stats-card', ['title' => 'Match Played','data' => $overviewStats['totalMatchPlayed'], 'dataThisMonth' => $overviewStats['thisMonthTotalMatchPlayed']])
                @include('components.stats-card', ['title' => 'Goals','data' => $overviewStats['totalGoals'], 'dataThisMonth' => $overviewStats['thisMonthTotalGoals']])
                @include('components.stats-card', ['title' => 'Goals Conceded','data' => $overviewStats['totalGoalsConceded'], 'dataThisMonth' => $overviewStats['thisMonthTotalGoalsConceded']])
                @include('components.stats-card', ['title' => 'Goals Difference','data' => $overviewStats['goalsDifference'], 'dataThisMonth' => $overviewStats['thisMonthGoalsDifference']])
                @include('components.stats-card', ['title' => 'Clean Sheets','data' => $overviewStats['totalCleanSheets'], 'dataThisMonth' => $overviewStats['thisMonthTotalCleanSheets']])
                @include('components.stats-card', ['title' => 'Own Goals','data' => $overviewStats['totalOwnGoals'], 'dataThisMonth' => $overviewStats['thisMonthTotalOwnGoals']])
                @include('components.stats-card', ['title' => 'Wins','data' => $overviewStats['totalWins'], 'dataThisMonth' => $overviewStats['thisMonthTotalWins']])
                @include('components.stats-card', ['title' => 'Losses','data' => $overviewStats['totalLosses'], 'dataThisMonth' => $overviewStats['thisMonthTotalLosses']])
                @include('components.stats-card', ['title' => 'Draws','data' => $overviewStats['totalDraws'], 'dataThisMonth' => $overviewStats['thisMonthTotalDraws']])

            @elseif(isPlayer())
                @include('components.stats-card', ['title' => 'Match Played','data' => $overviewStats['matchPlayed'], 'dataThisMonth' => $overviewStats['thisMonthMatchPlayed']])
                @include('components.stats-card', ['title' => 'Minutes Played','data' => $overviewStats['statsData']['minutesPlayed'], 'dataThisMonth' => $overviewStats['statsData']['minutesPlayedThisMonth']])
                @include('components.stats-card', ['title' => 'Fouls','data' => $overviewStats['statsData']['fouls'], 'dataThisMonth' => $overviewStats['statsData']['foulsThisMonth']])
                @include('components.stats-card', ['title' => 'Saves','data' => $overviewStats['statsData']['saves'], 'dataThisMonth' => $overviewStats['statsData']['savesThisMonth']])
                @include('components.stats-card', ['title' => 'Goals','data' => $overviewStats['statsData']['goals'], 'dataThisMonth' => $overviewStats['statsData']['goalsThisMonth']])
                @include('components.stats-card', ['title' => 'Assists','data' => $overviewStats['statsData']['assists'], 'dataThisMonth' => $overviewStats['statsData']['assistsThisMonth']])
                @include('components.stats-card', ['title' => 'Own Goals','data' => $overviewStats['statsData']['ownGoal'], 'dataThisMonth' => $overviewStats['statsData']['ownGoalThisMonth']])
                @include('components.stats-card', ['title' => 'Shots','data' => $overviewStats['statsData']['shots'], 'dataThisMonth' => $overviewStats['statsData']['shotsThisMonth']])
                @include('components.stats-card', ['title' => 'Passes','data' => $overviewStats['statsData']['passes'], 'dataThisMonth' => $overviewStats['statsData']['passesThisMonth']])
                @include('components.stats-card', ['title' => 'Fouls','data' => $overviewStats['statsData']['fouls'], 'dataThisMonth' => $overviewStats['statsData']['foulsThisMonth']])
                @include('components.stats-card', ['title' => 'Yellow Cards','data' => $overviewStats['statsData']['yellowCards'], 'dataThisMonth' => $overviewStats['statsData']['yellowCardsThisMonth']])
                @include('components.stats-card', ['title' => 'Red Cards','data' => $overviewStats['statsData']['redCards'], 'dataThisMonth' => $overviewStats['statsData']['redCardsThisMonth']])
                @include('components.stats-card', ['title' => 'Wins','data' => $overviewStats['statsData']['Win'], 'dataThisMonth' => $overviewStats['statsData']['WinThisMonth']])
                @include('components.stats-card', ['title' => 'Losses','data' => $overviewStats['statsData']['Lose'], 'dataThisMonth' => $overviewStats['statsData']['LoseThisMonth']])
                @include('components.stats-card', ['title' => 'Draws','data' => $overviewStats['statsData']['Draw'], 'dataThisMonth' => $overviewStats['statsData']['DrawThisMonth']])
            @endif

        </div>

        @if(isPlayer())
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
                        <div class="page-separator__text">Latest Match</div>
                    </div>
                    @foreach($latestMatches as $latestMatch)
                            <x-match-card :match="$latestMatch" :latestMatch="true"/>
                    @endforeach
                </div>
            </div>
        @elseif(isAllAdmin() || isCoach())
            <div class="page-separator">
                <div class="page-separator__text">Latest Match</div>
            </div>
            <div class="row">
                @foreach($latestMatches as $latestMatch)
                    <div class="col-sm-6 flex-column">
                        <x-match-card :match="$latestMatch" :latestMatch="true"/>
                    </div>
                @endforeach
            </div>
        @endif


        <div class="page-separator">
            <div class="page-separator__text">Match History</div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="matchHistoryTable">
                        <thead>
                        <tr>
                            <th>Teams</th>
                            <th>Opponent Teams</th>
                            <th>Score</th>
                            <th>Match Date</th>
                            <th>Location</th>
                            <th>Competition</th>
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
            <div class="page-separator__text">Competition Leaderboard</div>
            <div class="form-group ml-auto w-25 mb-0">
                <select class="form-control form-select" id="competitionFilter" data-toggle="select">
                    <option selected disabled>Filter by Competition
                    </option>
                    @foreach($allCompetitions as $competition)
                        <option value="{{ $competition->id }}" data-avatar-src="{{ Storage::url($competition->logo) }}">
                            {{ $competition->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @foreach($activeCompetitions as $competition)
            <div class="page-separator">
                <div class="page-separator__text">{{ $competition->name }}</div>
            </div>
            @foreach($competition->groups as $group)
                <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                    <div class="card-header">
                        <h5 class="mb-0">{{ $group->groupName }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="classTable{{ $group->id }}">
                                <thead>
                                <tr>
                                    <th>Team</th>
                                    <th>Match Played</th>
                                    <th>won</th>
                                    <th>drawn</th>
                                    <th>lost</th>
                                    <th>goals For</th>
                                    <th>goals Againts</th>
                                    <th>goals Difference</th>
                                    <th>red Cards</th>
                                    <th>yellow Cards</th>
                                    <th>points</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            const matchHistory = $('#matchHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! $matchHistoryRoutes !!}',
                },
                columns: [
                    {data: 'team', name: 'team'},
                    {data: 'opponentTeam', name: 'opponentTeam'},
                    {data: 'score', name: 'score'},
                    {data: 'date', name: 'date'},
                    {data: 'place', name: 'place'},
                    {data: 'competition', name: 'competition'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            @foreach($activeCompetitions as $competition)
                @foreach($competition->groups as $group)
                    const classTable{{$group->id}} = $('#classTable{{$group->id}}').DataTable({
                        processing: true,
                        serverSide: true,
                        ordering: true,
                        ajax: {
                            url: '{!! route('division-managements.index', ['competition'=>$competition->id,'group'=>$group->id]) !!}',
                        },
                        columns: [
                            {data: 'teams', name: 'teams'},
                            {data: 'pivot.matchPlayed', name: 'pivot.matchPlayed'},
                            {data: 'pivot.won', name: 'pivot.won'},
                            {data: 'pivot.drawn', name: 'pivot.drawn'},
                            {data: 'pivot.lost', name: 'pivot.lost'},
                            {data: 'pivot.goalsFor', name: 'pivot.goalsFor'},
                            {data: 'pivot.goalsAgaints', name: 'pivot.goalsAgaints'},
                            {data: 'pivot.goalsDifference', name: 'pivot.goalsDifference'},
                            {data: 'pivot.redCards', name: 'pivot.redCards'},
                            {data: 'pivot.yellowCards', name: 'pivot.yellowCards'},
                            {data: 'pivot.points', name: 'pivot.points'},
                        ],
                        order: [[10, 'desc']]
                    });
                @endforeach
            @endforeach

            {{--$('#competitionFilter').on('change', function (e){--}}
            {{--    e.preventDefault();--}}
            {{--    const competitionId = $(this).val();--}}

            {{--    $.ajax({--}}
            {{--        url: "{{ route('division-managements.index', ['competition'=>$competition->id,'group'=>$group->id]) }}",--}}
            {{--        data: {--}}
            {{--            fields: 'states',--}}
            {{--            "filters[country_id]": idCountry,--}}
            {{--        },--}}
            {{--        type: 'GET',--}}
            {{--        dataType: 'json',--}}
            {{--        success: function (result) {--}}
            {{--            $('#state_id').html('<option disabled selected>Select State</option>');--}}
            {{--            $.each(result.data, function (key, value) {--}}
            {{--                $('#state_id').append('<option value="' + value.id + '">' + value.name + '</option>');--}}
            {{--            });--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}
        });
    </script>
@endpush
