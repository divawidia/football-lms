@extends('layouts.master')
@section('title')
    Match Histories
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="container">
        <h2 class="mb-0">@yield('title')</h2>
        <ol class="breadcrumb p-0 m-0">
            <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
            <li class="breadcrumb-item active">@yield('title')</li>
        </ol>
    </div>

    <div class="container page-section">
        <x-match-tables :route="$tableRoute" tableId="tables"/>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Calendar</h4>
            </div>
            <div class="card-body">
                <x-schedule-calendar :events="$events" calendarId="calendar"/>
            </div>
        </div>
    </div>
@endsection
