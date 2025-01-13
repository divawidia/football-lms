@extends('layouts.master')
@section('title')
    Training Schedules
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
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        @if(isAllAdmin() || isCoach())
            <x-buttons.basic-button icon="add" text="Add New" additionalClass="add-training-btn" margin="mb-3"/>
{{--            <a href="{{  route('training-schedules.create') }}" class="btn btn-primary mb-3" id="add-new">--}}
{{--                    <span class="material-icons mr-2">--}}
{{--                        add--}}
{{--                    </span>--}}
{{--                Add New--}}
{{--            </a>--}}
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
@endsection

@push('addon-script')
    <script type="module">
        import { processWithConfirmation } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;

        $(document).ready(function () {
            processWithConfirmation(
                '.delete',
                "{{ route('training-schedules.destroy', ['schedule' => ':id']) }}",
                "{{ route('training-schedules.index') }}",
                'DELETE',
                "Are you sure to delete this training?",
                "Something went wrong when deleting this training!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.cancelBtn',
                "{{ route('training-schedules.cancel', ['schedule' =>':id']) }}",
                "{{ route('training-schedules.index') }}",
                'PATCH',
                "Are you sure to cancel this training?",
                "Something went wrong when cancelling this training!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.scheduled-btn',
                "{{ route('training-schedules.scheduled', ['schedule' =>':id']) }}",
                "{{ route('training-schedules.index') }}",
                'PATCH',
                "Are you sure to set this training to scheduled?",
                "Something went wrong when set this training to scheduled!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
