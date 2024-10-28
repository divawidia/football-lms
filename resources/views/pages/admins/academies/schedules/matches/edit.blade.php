@extends('layouts.master')
@section('title')
    Edit Match {{ $data->teams[0]->teamName }} Vs. {{ $data->teams[1]->teamName }} Schedule
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="container page__container d-flex flex-column pt-32pt">
            <h2 class="mb-0">
                @yield('title')
            </h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('match-schedules.index') }}">Match Schedule</a></li>
                <li class="breadcrumb-item"><a href="{{ route('match-schedules.show', $data->id) }}">Match {{ $data->teams[0]->teamName }} Vs. {{ $data->teams[1]->teamName }}</a></li>
                <li class="breadcrumb-item active">
                    Edit
                </li>
            </ol>
        </div>

        <div class="container page-section">
            <div class="list-group">
                <form action="{{ route('match-schedules.update', $data->id) }}" method="post">
                    @method('PUT')
                    @csrf
                    <div class="list-group-item">
                        <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                            <div class="row">
{{--                                <div class="col-lg-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label class="form-label" for="matchType">Match Type</label>--}}
{{--                                        <small class="text-danger">*</small>--}}
{{--                                        <select class="form-control form-select matchType-form @error('matchType') is-invalid @enderror" id="matchType" name="matchType" data-toggle="select" required>--}}
{{--                                            <option disabled selected>Select match type</option>--}}
{{--                                            @foreach(['Friendly Match', 'Competition'] AS $type)--}}
{{--                                                <option value="{{ $type }}" @selected(old('attendanceStatus', $data->matchType) == $type)>{{ $type }}</option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                        @error('matchType')--}}
{{--                                        <span class="invalid-feedback" role="alert">--}}
{{--                                            <strong>{{ $message }}</strong>--}}
{{--                                        </span>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
{{--                                    <div class="form-group competition-section">--}}
{{--                                        <label class="form-label" for="competitionId">Competition</label>--}}
{{--                                        <small class="text-danger">*</small>--}}
{{--                                        @if(count($competitions) == 0)--}}
{{--                                            @include('components.alerts.warning',[ 'text' => "Currently you haven't created any competition in your academy, please create competition first", 'createRoute' => route('competition-managements.create')])--}}
{{--                                        @else--}}
{{--                                            <select class="form-control form-select @error('competitionId') is-invalid @enderror" id="competitionId" name="competitionId">--}}
{{--                                                <option disabled selected>Select match competition</option>--}}
{{--                                                @foreach($competitions AS $competition)--}}
{{--                                                    <option value="{{ $competition->id }}" @selected(old('competitionId', $data->competitionId) == $competition->id) data-avatar-src="{{ Storage::url($competition->logo) }}">{{ $competition->name }}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                        @endif--}}
{{--                                        @error('competitionId')--}}
{{--                                        <span class="invalid-feedback" role="alert">--}}
{{--                                            <strong>{{ $message }}</strong>--}}
{{--                                        </span>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
{{--                                    <div class="form-group">--}}
{{--                                        <div class="d-flex flex-row align-items-center mb-2">--}}
{{--                                            <label class="form-label mb-0" for="teamId">Teams</label>--}}
{{--                                            <small class="text-danger">*</small>--}}
{{--                                        </div>--}}
{{--                                        <select class="form-control form-select @error('teamId') is-invalid @enderror" id="teamId" name="teamId" data-toggle="select">--}}
{{--                                        </select>--}}
{{--                                        @error('teamId')--}}
{{--                                        <span class="invalid-feedback" role="alert">--}}
{{--                                            <strong>{{ $message }}</strong>--}}
{{--                                        </span>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
{{--                                    <div class="form-group">--}}
{{--                                        <div class="d-flex flex-row align-items-center mb-2">--}}
{{--                                            <label class="form-label mb-0" for="opponentTeamId">Opponent Teams</label>--}}
{{--                                            <small class="text-danger">*</small>--}}
{{--                                        </div>--}}
{{--                                        <select class="form-control form-select @error('opponentTeamId') is-invalid @enderror" id="opponentTeamId" name="opponentTeamId" data-toggle="select">--}}
{{--                                        </select>--}}
{{--                                        @error('opponentTeamId')--}}
{{--                                        <span class="invalid-feedback" role="alert">--}}
{{--                                            <strong>{{ $message }}</strong>--}}
{{--                                        </span>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="date">Match Date</label>
                                        <small class="text-danger">*</small>
                                        <input type="hidden"
                                               class="form-control flatpickr-input @error('date') is-invalid @enderror"
                                               id="date"
                                               name="date"
                                               required
                                               value="{{ $data->date }}"
                                               data-toggle="flatpickr">
                                        @error('date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label" for="startTime">Start Time</label>
                                                <small class="text-danger">*</small>
                                                <input type="text"
                                                       id="startTime"
                                                       name="startTime"
                                                       required
                                                       value="{{ old('startTime', $data->startTime) }}"
                                                       class="form-control @error('startTime') is-invalid @enderror"
                                                       placeholder="Input training's start time ..."
                                                       data-toggle="flatpickr"
                                                       data-flatpickr-enable-time="true"
                                                       data-flatpickr-no-calendar="true"
                                                       data-flatpickr-alt-format="H:i"
                                                       data-flatpickr-date-format="H:i">
                                                @error('startTime')
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label" for="endTime">End Time</label>
                                                <small class="text-danger">*</small>
                                                <input type="text"
                                                       id="endTime"
                                                       name="endTime"
                                                       required
                                                       value="{{ old('endTime', $data->endTime) }}"
                                                       class="form-control @error('endTime') is-invalid @enderror"
                                                       placeholder="Input training's end time ..."
                                                       data-toggle="flatpickr"
                                                       data-flatpickr-enable-time="true"
                                                       data-flatpickr-no-calendar="true"
                                                       data-flatpickr-alt-format="H:i"
                                                       data-flatpickr-date-format="H:i">
                                                @error('endTime')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="place">Match Location</label>
                                        <small class="text-danger">*</small>
                                        <input type="text"
                                               class="form-control @error('place') is-invalid @enderror"
                                               id="place"
                                               name="place"
                                               value="{{ old('place', $data->place) }}"
                                               placeholder="E.g. : Football field ...">
                                        @error('place')
                                        <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item d-flex justify-content-end">
                        <a class="btn btn-secondary mx-2" href="{{ url()->previous() }}"><span class="material-icons mr-2">close</span> Cancel</a>
                        <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    @endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            {{--const teamId = {{ $data->teams[0]->id }};--}}
            {{--const opponentTeamId = {{ $data->teams[1]->id }};--}}

            {{--function getCompetitionTeam(teamId, opponentTeamId, competitionId){--}}
            {{--    return $.ajax({--}}
            {{--        url: "{{route('match-schedules.get-competition-team', ['competition' => ':id']) }}".replace(':id', competitionId),--}}
            {{--        type: 'GET',--}}
            {{--        dataType: 'json',--}}
            {{--        success: function (result) {--}}
            {{--            $('#teamId').html('<option disabled selected>Select team in this match</option>');--}}
            {{--            $.each(result.data.teams, function (key, value) {--}}
            {{--                $.each(value, function (key, value) {--}}
            {{--                    // console.log(value);--}}
            {{--                    $('#teamId').append('<option value=' + value.id + '>' + value.teamName + '</option>');--}}
            {{--                });--}}
            {{--            });--}}
            {{--            $('#teamId option[value="' + teamId + '"]').attr('selected', 'selected');--}}

            {{--            $('#opponentTeamId').html('<option disabled selected>Select opponent team in this match</option>');--}}
            {{--            $.each(result.data.opponentTeams, function (key, value) {--}}
            {{--                $.each(value, function (key, value) {--}}
            {{--                    $('#opponentTeamId').append('<option value=' + value.id + '>' + value.teamName + '</option>');--}}
            {{--                });--}}
            {{--                $('#opponentTeamId option[value="' + opponentTeamId + '"]').attr('selected', 'selected');--}}

            {{--            });--}}
            {{--        }--}}
            {{--    });--}}
            {{--}--}}

            {{--function getFriendlyMatchTeam(teamId, opponentTeamId){--}}
            {{--    return $.ajax({--}}
            {{--        url: "{{route('match-schedules.get-friendlymatch-team')}}",--}}
            {{--        type: 'GET',--}}
            {{--        dataType: 'json',--}}
            {{--        success: function (result) {--}}
            {{--            $('#teamId').html('<option disabled>Select team in this match</option>');--}}
            {{--            $.each(result.data.teams, function (key, value) {--}}
            {{--                $('#teamId').append('<option value=' + value.id + '>' + value.teamName + '</option>');--}}
            {{--            });--}}
            {{--            $('#teamId option[value="' + teamId + '"]').attr('selected', 'selected');--}}

            {{--            $('#opponentTeamId').html('<option disabled>Select opponent team in this match</option>');--}}
            {{--            $.each(result.data.opponentTeams, function (key, value) {--}}
            {{--                $('#opponentTeamId').append('<option value=' + value.id + '>' + value.teamName + '</option>');--}}
            {{--            })--}}
            {{--            $('#opponentTeamId option[value="' + opponentTeamId + '"]').attr('selected', 'selected');--}}

            {{--        }--}}
            {{--    });--}}
            {{--}--}}

            {{--@if($data->matchType == 'Competition')--}}
            {{--    $('.competition-section').show();--}}
            {{--    getCompetitionTeam(teamId, opponentTeamId, {{ $data->competitionId }});--}}
            {{--@elseif($data->matchType == 'Friendly Match')--}}
            {{--    $('.competition-section').hide();--}}
            {{--    getFriendlyMatchTeam(teamId, opponentTeamId);--}}
            {{--@endif--}}

            {{--$('.matchType-form').on('change', function () {--}}
            {{--    const type = this.value;--}}
            {{--    if (type === 'Competition'){--}}
            {{--        $('.competition-section').show();--}}
            {{--        getCompetitionTeam(teamId, opponentTeamId, {{ $data->competitionId }});--}}
            {{--    }else if (type === 'Friendly Match'){--}}
            {{--        $('.competition-section').hide();--}}
            {{--        getFriendlyMatchTeam(teamId, opponentTeamId);--}}
            {{--    }--}}
            {{--});--}}

            {{--$('#competitionId').on('change', function () {--}}
            {{--    const id = this.value;--}}
            {{--    getCompetitionTeam(teamId, opponentTeamId, id);--}}
            {{--});--}}
        });
    </script>
@endpush
