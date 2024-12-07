@extends('layouts.master')
@section('title')
    Training Schedules
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        @if(isAllAdmin() || isCoach())
            <a href="{{  route('training-schedules.create') }}" class="btn btn-primary mb-3" id="add-new">
                    <span class="material-icons mr-2">
                        add
                    </span>
                Add New
            </a>
        @endif
        <x-training-tables :route="$tableRoute" tableId="tables"/>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Calendar</h4>
            </div>
            <div class="card-body">
                <x-schedule-calendar :events="$events" calendarId="calendar"/>
            </div>
        </div>
    </div>

    <x-process-data-confirmation btnClass=".delete"
                                 :processRoute="route('training-schedules.destroy', ['schedule' => ':id'])"
                                 :routeAfterProcess="route('training-schedules.index')"
                                 method="DELETE"
                                 confirmationText="Are you sure to delete this training session?"
                                 successText="Successfully deleted training session!"
                                 errorText="Something went wrong when deleting training session!"/>

    <x-process-data-confirmation btnClass=".cancelTrainingBtn"
                                 :processRoute="route('cancel-training', ['schedule' => ':id'])"
                                 :routeAfterProcess="route('training-schedules.index')"
                                 method="PATCH"
                                 confirmationText="Are you sure to cancel competition?"
                                 successText="Training session successfully mark as cancelled!"
                                 errorText="Something went wrong when marking training session as cancelled!"/>
@endsection
