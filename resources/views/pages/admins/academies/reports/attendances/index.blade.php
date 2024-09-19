@extends('layouts.master')
@section('title')
    Attendance
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left">
                <div class="flex d-flex flex-column flex-sm-row align-items-center">
                    <div class="mr-sm-24pt text-sm-left">
                        <h2 class="mb-0">@yield('title')</h2>
                        <ol class="breadcrumb p-0 m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">
                                @yield('title')
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container page__container page-section">
            {{--    Overview    --}}
            <div class="page-separator">
                <div class="page-separator__text">Overview</div>
                <a href="" id="addTeamScorer" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Filter</a>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-header">
                            <h4 class="card-title">Player Attendance</h4>
                        </div>
                        <div class="card-body">
                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-header">
                            <h4 class="card-title">Player Attendance</h4>
                        </div>
                        <div class="card-body">
                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="page-separator">
                        <div class="page-separator__text">most attended player</div>
                    </div>
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-body d-flex align-items-center flex-row text-left">
                            <img src=""
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="player-photo">
                            <div class="flex ml-3">
                                <h5 class="mb-0">name</h5>
                                <p class="text-50 lh-1 mb-0">Team</p>
                            </div>
                            <div class="h2 mb-0 mr-3">12</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Event Attended</div>
                                <p class="card-subtitle text-50">
                                    90& of total event
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="page-separator">
                        <div class="page-separator__text">most absent player</div>
                    </div>
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-body d-flex align-items-center flex-row text-left">
                            <img src=""
                                 width="50"
                                 height="50"
                                 class="rounded-circle img-object-fit-cover"
                                 alt="player-photo">
                            <div class="flex ml-3">
                                <h5 class="mb-0">name</h5>
                                <p class="text-50 lh-1 mb-0">Team</p>
                            </div>
                            <div class="h2 mb-0 mr-3">12</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Event Absent</div>
                                <p class="card-subtitle text-50">
                                    90& of total event
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-separator">
                <div class="page-separator__text">player attendance</div>
            </div>
            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Teams</th>
                                <th>Match</th>
                                <th>Training</th>
                                <th>Attended</th>
                                <th>Absent</th>
                                <th>Injured</th>
                                <th>Illness</th>
                                <th>Others</th>
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
