@extends('layouts.master')
@section('title')
    Create Competition
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <!-- Modal Create Opponent Team -->
{{--    <x-modal.teams.create-opponent-team/>--}}

    <!-- Modal Create Team -->
{{--    <x-modal.teams.create-academy-team/>--}}
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">
                @yield('title')
            </h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('competition-managements.index') }}">Competition</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('competition-managements.store') }}" method="post" enctype="multipart/form-data" id="createCompetitionForm">
                    @csrf
                    <div class="page-separator">
                        <div class="page-separator__text">Competition Info</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <x-forms.image-input name="logo" label="Competition Logo"/>

                            <x-forms.select name="type" label="Competition Type">
                                <option disabled selected>Select competition type</option>
                                @foreach(['League', 'Knockout', 'Friendly'] AS $type)
                                    <option value="{{ $type }}" @selected(old('type') == $type)>{{ $type }}</option>
                                @endforeach
                            </x-forms.select>

                            <x-forms.basic-input type="date" name="startDate" label="Start Date"/>

{{--                            <x-forms.basic-input type="text" name="contactName" label="Contact Name" placeholder="Input competition's contact name ..."/>--}}
                        </div>
                        <div class="col-lg-6">
                            <x-forms.basic-input type="text" name="name" label="Competition Name" placeholder="Input competition's name ..."/>

                            <x-forms.basic-input type="text" name="location" label="Location" placeholder="Input competition's location ..."/>

                            <x-forms.basic-input type="date" name="endDate" label="End Date"/>

                            <x-forms.select name="isInternal" label="Is Internal Competition?">
                                <option disabled selected>Select yes or no</option>
                                @foreach(['Yes' => 1, 'No' => 0] AS $label => $value)
                                    <option value="{{ $value }}" @selected(old('isInternal') == $label)>{{ $label }}</option>
                                @endforeach
                            </x-forms.select>
{{--                            <x-forms.basic-input type="text" name="contactPhone" label="Contact Phone" placeholder="Input competition's contact phone ..."/>--}}
                        </div>
{{--                        <div class="col-12">--}}
{{--                            <x-forms.textarea name="description" label="Description" placeholder="Input competition's description ..." :required="false"/>--}}
{{--                        </div>--}}
                    </div>
{{--                    <div class="page-separator">--}}
{{--                        <div class="page-separator__text">Division</div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-lg-6">--}}
{{--                            <x-forms.basic-input type="text" name="groupName" label="Group Division Name" placeholder="E.g. : U-16 Group A ..."/>--}}

{{--                            <div class="form-group mb-3">--}}
{{--                                <div class="d-flex flex-row align-items-center mb-2">--}}
{{--                                    <label class="form-label mb-0" for="teams">Our Teams</label>--}}
{{--                                    <small class="text-danger">*</small>--}}
{{--                                    <x-buttons.basic-button type="button" id="addNewTeam" size="sm" margin="ml-auto" icon="add" text="Add new team"/>--}}
{{--                                </div>--}}

{{--                                @if(count($teams) == 0)--}}
{{--                                    <x-warning-alert text="Currently you haven't create any team in your academy, please create your team by clicking the add new team button"/>--}}
{{--                                @else--}}
{{--                                    <x-forms.select name="teams[]" id="team" :label="null" :multiple="true">--}}
{{--                                        <option disabled>Select our team to play in this division</option>--}}
{{--                                        @foreach($teams as $team)--}}
{{--                                            <option value="{{ $team->id }}" @selected(old('teams') == $team->id) data-avatar-src="{{ Storage::url($team->logo) }}">--}}
{{--                                                {{ $team->teamName }} ~ {{ $team->ageGroup }}--}}
{{--                                            </option>--}}
{{--                                        @endforeach--}}
{{--                                    </x-forms.select>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-lg-6">--}}
{{--                            <div class="form-group mb-3">--}}
{{--                                <div class="d-flex flex-row align-items-center mb-2">--}}
{{--                                    <label class="form-label mb-0" for="opponentTeams">Opponent Teams</label>--}}
{{--                                    <small class="text-danger">*</small>--}}
{{--                                    <x-buttons.basic-button type="button" id="addNewOpponentTeam" size="sm" margin="ml-auto" icon="add" text="Add new team"/>--}}
{{--                                </div>--}}
{{--                                @if(count($opponentTeams) == 0)--}}
{{--                                    <x-warning-alert text="Currently you haven't create any opponent team, please create your opponent team by clicking the add new team button"/>--}}
{{--                                @else--}}
{{--                                    <x-forms.select name="opponentTeams[]" id="opponentTeams" :label="null" :multiple="true">--}}
{{--                                        <option disabled>Select your opponent team to play in this division</option>--}}
{{--                                        @foreach($opponentTeams as $team)--}}
{{--                                            <option value="{{ $team->id }}" @selected(old('opponentTeams') == $team->id) data-avatar-src="{{ Storage::url($team->logo) }}">--}}
{{--                                                {{ $team->teamName }} ~ {{ $team->ageGroup }}--}}
{{--                                            </option>--}}
{{--                                        @endforeach--}}
{{--                                    </x-forms.select>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="page-separator"></div>
                    <div class="d-flex justify-content-end">
                        <x-buttons.link-button color="secondary" margin="mx-2" :href="url()->previous()" icon="close" text="Cancel"/>
                        <x-buttons.basic-button type="submit" icon="add" text="Submit"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
