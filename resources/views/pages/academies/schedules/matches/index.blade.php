@extends('layouts.master')
@section('title')
    Match Schedules
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

@push('addon-script')
    <script>
        $(document).ready(function () {
            processWithConfirmation(
                '.delete',
                "{{ route('match-schedules.destroy', ['match' => ':id']) }}",
                "{{ route('match-schedules.index') }}",
                'DELETE',
                "Are you sure to delete this match?",
                "Something went wrong when deleting this match!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.cancelBtn',
                "{{ route('match-schedules.cancel', ['match' =>':id']) }}",
                "{{ route('match-schedules.index') }}",
                'PATCH',
                "Are you sure to cancel this match?",
                "Something went wrong when cancelling this match!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.scheduled-btn',
                "{{ route('match-schedules.scheduled', ['match' =>':id']) }}",
                "{{ route('match-schedules.index') }}",
                'PATCH',
                "Are you sure to set this match to scheduled?",
                "Something went wrong when set this match to scheduled!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush

