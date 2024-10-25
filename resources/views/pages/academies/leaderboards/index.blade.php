@extends('layouts.master')
@section('title')
    Leaderboard
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container d-flex flex-column">
                <h2 class="mb-0">@yield('title')</h2>
                <ol class="breadcrumb p-0 m-0">
                    @if(isAllAdmin())
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    @elseif(isCoach())
                        <li class="breadcrumb-item"><a href="{{ route('coach.dashboard') }}">Home</a></li>
                    @endif
                    <li class="breadcrumb-item active">
                        @yield('title')
                    </li>
                </ol>
            </div>
        </div>

        <div class="container page__container page-section">
            {{--    Overview    --}}
            <div class="page-separator">
                <div class="page-separator__text">Team Leaderboard</div>
                <a href="" id="addTeamScorer" class="btn btn-white btn-outline-secondary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>
            </div>

            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="teamsLeaderboardTable">
                            <thead>
                            <tr>
                                <th>Pos.</th>
                                <th>Teams</th>
                                <th>Match Played</th>
                                <th>Won</th>
                                <th>Drawn</th>
                                <th>Lost</th>
                                <th>Goals</th>
                                <th>Goals Conceded</th>
                                <th>Clean Sheets</th>
                                <th>Own Goal</th>
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
                <div class="page-separator__text">Player Leaderboard</div>
            </div>

            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="playersLeaderboardTable">
                            <thead>
                            <tr>
                                <th>Pos.</th>
                                <th>Name</th>
                                <th>Team</th>
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
        </div>
    @endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            const teamsLeaderboard = $('#teamsLeaderboardTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    @if(isAllAdmin())
                    url: '{!! route('leaderboards.teams') !!}',
                    @elseif(isCoach())
                    url: '{!! route('coach.leaderboards.teams') !!}',
                    @endif
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'match', name: 'match' },
                    { data: 'won', name: 'won'},
                    { data: 'drawn', name: 'drawn'},
                    { data: 'lost', name: 'lost'},
                    { data: 'goals', name: 'goals'},
                    { data: 'goalsConceded', name: 'goalsConceded'},
                    { data: 'cleanSheets', name: 'cleanSheets'},
                    { data: 'ownGoals', name: 'ownGoals'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[3, 'desc']]
            });

            const playersLeaderboard = $('#playersLeaderboardTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    @if(isAllAdmin())
                    url: '{!! route('leaderboards.players') !!}',
                    @elseif(isCoach())
                    url: '{!! route('coach.leaderboards.players') !!}',
                    @endif
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'teams', name: 'teams' },
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
                order: [[4, 'desc']]
            });
        });
    </script>
@endpush
