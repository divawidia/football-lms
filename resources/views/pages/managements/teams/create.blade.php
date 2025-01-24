@extends('layouts.master')
@section('title')
    Create Team
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('team-managements.index') }}">Teams Management</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('team-managements.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="page-separator">
                        <div class="page-separator__text">Team Profile</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <x-forms.image-input name="logo" label="Team logo"/>
                            <x-forms.basic-input type="text" name="teamName" label="Team name" placeholder="Input team's name ..."/>

                            <x-forms.select name="ageGroup" label="Age Group" :select2="true">
                                <option disabled selected>Select team's Age Group</option>
                                @foreach(['U-6', 'U-7', 'U-8', 'U-9', 'U-10', 'U-11', 'U-12', 'U-13', 'U-14', 'U-15', 'U-16', 'U-17', 'U-18', 'U-19', 'U-20', 'U-21', 'Senior'] AS $ageGroup)
                                    <option value="{{ $ageGroup }}" @selected(old('ageGroup') == $ageGroup)>{{ $ageGroup }}</option>
                                @endforeach
                            </x-forms.select>
                        </div>
                        <div class="col-lg-6">
                            @if(count($coaches) < 1)
                                <x-warning-alert text="Currently you haven't create any player in your academy, please create your team" :createRoute="route('player-managements.create')"/>
                            @endif
                            <x-forms.select name="players[]" label="Team players" :select2="true" :multiple="true">
                                <option disabled>Select players to play in this team</option>
                                @foreach($players as $player)
                                    <option value="{{ $player->id }}" @selected(old('players') == $player->id) data-avatar-src="{{ Storage::url($player->user->foto) }}">
                                        {{ $player->user->firstName }} {{ $player->user->lastName }} - {{ $player->position->name }}
                                        @if(count($player->teams) == 0)
                                            - No Team
                                        @else
                                            @foreach($player->teams as $team)- {{ $team->teamName }}@endforeach
                                        @endif
                                    </option>
                                @endforeach
                            </x-forms.select>

                            @if(count($coaches) < 1)
                                <x-warning-alert text="Currently you haven't create any coach in your academy, please create your team" :createRoute="route('coach-managements.create')"/>
                            @endif
                            <x-forms.select name="coaches[]" label="Team coaches" :select2="true" :multiple="true">
                                <option disabled>Select coaches to play in this team</option>
                                @foreach($coaches as $coach)
                                    <option value="{{ $coach->id }}" @selected(old('coaches') == $coach->id) data-avatar-src="{{ Storage::url($coach->user->foto) }}">
                                        {{ $coach->user->firstName }} {{ $coach->user->lastName }} - {{ $coach->specialization->name }}
                                        @if(count($coach->teams) == 0)
                                            - No Team
                                        @else
                                            @foreach($coach->teams as $team)- {{ $team->teamName }}@endforeach
                                        @endif
                                    </option>
                                @endforeach
                            </x-forms.select>
                        </div>
                    </div>
                    <div class="page-separator"></div>
                    <div class="d-flex justify-content-end">
                        <x-buttons.link-button color="secondary" margin="mr-2" :href="route('team-managements.index')" icon="close" text="Cancel"/>
                        <x-buttons.basic-button icon="add" text="Submit" color="primary" type="submit"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
