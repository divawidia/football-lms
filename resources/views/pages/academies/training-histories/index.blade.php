@extends('layouts.master')
@section('title')
    Training Histories
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.trainings.add-training/>
    <x-modal.trainings.edit-training/>
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        @if(isAllAdmin() || isCoach())
            <x-buttons.basic-button icon="add" text="Add New" additionalClass="add-training-btn" margin="mb-3"/>
        @endif
        <x-tables.training-tables :route="$tableRoute" tableId="tables"/>

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
