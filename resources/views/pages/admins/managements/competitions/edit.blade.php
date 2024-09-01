@extends('layouts.master')
@section('title')
    Edit Team {{ $team->teamName }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left">
                <div class="flex d-flex flex-column flex-sm-row align-items-center mb-24pt mb-md-0">
                    <div class="mb-24pt mb-sm-0 mr-sm-24pt">
                        <h2 class="mb-0">
                            @yield('title')
                        </h2>
                        <ol class="breadcrumb p-0 m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('team-managements.index') }}">Teams Management</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('team-managements.show', $team->id) }}">{{ $team->teamName }}</a></li>
                            <li class="breadcrumb-item active">
                                @yield('title')
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container page__container page-section">
            <div class="list-group">
                <form action="{{ route('team-managements.update', ['team' => $team]) }}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="list-group-item d-flex justify-content-end">
                        <a class="btn btn-secondary mx-2" href="{{ url()->previous() }}"><span class="material-icons mr-2">close</span> Cancel</a>
                        <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">save</span> Save</button>
                    </div>
                    <div class="list-group-item">
                        <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                            <div class="page-separator">
                                <div class="page-separator__text">Team Profile</div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="form-label">Team Logo</label>
                                    <small class="text-black-100">(Optional)</small>
                                    <div class="media align-items-center mb-2">
                                        <img src="{{ Storage::url($team->logo) }}"
                                             alt="people"
                                             width="54"
                                             height="54"
                                             id="preview"
                                             class="mr-16pt rounded-circle img-object-fit-cover" />
                                        <div class="media-body">
                                            <div class="custom-file">
                                                <input type="file"
                                                       class="custom-file-input @error('logo') is-invalid @enderror"
                                                       name="logo"
                                                       id="logo">
                                                <label class="custom-file-label" for="logo">Choose file</label>
                                            </div>
                                        </div>
                                        @error('logo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="ageGroup">Age Group</label>
                                        <small class="text-danger">*</small>
                                        <select class="form-control form-select @error('ageGroup') is-invalid @enderror" id="ageGroup" name="ageGroup" required data-toggle="select">
                                            <option disabled selected>Select player's age group</option>
                                            @foreach(['U-6', 'U-7', 'U-8', 'U-9', 'U-10', 'U-11', 'U-12', 'U-13', 'U-14', 'U-15', 'U-16', 'U-17', 'U-18', 'U-19', 'U-20', 'U-21'] AS $ageGroup)
                                                <option value="{{ $ageGroup }}" @selected(old('ageGroup', $team->ageGroup) == $ageGroup)>{{ $ageGroup }}</option>
                                            @endforeach
                                        </select>
                                        @error('ageGroup')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="players">Players</label>
                                        <small class="text-black-100">(Optional)</small>
                                        @if(count($players) == 0)
                                            <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                                 role="alert">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <i class="material-icons mr-8pt">error_outline</i>
                                                    <div class="media-body"
                                                         style="min-width: 180px">
                                                        <small class="text-black-100">Curently you haven't create any player in your academy, please create your team</small>
                                                    </div>
                                                    <div class="ml-8pt mt-2 mt-sm-0">
                                                        <a href="{{ route('team-managements.create') }}"
                                                           class="btn btn-link btn-sm">Create Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <select class="form-control form-select @error('players') is-invalid @enderror" id="players" name="players[]" data-toggle="select" multiple>
                                                <option disabled>Select players to play in this team</option>
                                                @foreach($players as $player)
                                                    <option value="{{ $player->id }}" @selected(old('players') == in_array($player->id, $player_id)) data-avatar-src="{{ Storage::url($player->user->foto) }}">
                                                        {{ $player->user->firstName }} {{ $player->user->lastName }} - {{ $player->position->name }} -
                                                        @if(count($player->teams) == 0)
                                                            No Team
                                                        @else
                                                            @foreach($player->teams as $team)
                                                                <span class="badge badge-pill badge-danger mr-1">{{ $team->teamName }}</span>
                                                            @endforeach
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                        @error('team')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label" for="teamName">Team Name</label>
                                        <small class="text-danger">*</small>
                                        <input type="text"
                                               id="teamName"
                                               name="teamName"
                                               required
                                               value="{{ old('teamName', $team->teamName) }}"
                                               class="form-control @error('teamName') is-invalid @enderror"
                                               placeholder="Input team's name ...">
                                        @error('teamName')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="coaches">Coaches</label>
                                        <small class="text-black-100">(Optional)</small>
                                        @if(count($coaches) == 0)
                                            <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                                 role="alert">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <i class="material-icons mr-8pt">error_outline</i>
                                                    <div class="media-body"
                                                         style="min-width: 180px">
                                                        <small class="text-black-100">Curently you haven't create any coach in your academy, please create your team</small>
                                                    </div>
                                                    <div class="ml-8pt mt-2 mt-sm-0">
                                                        <a href="{{ route('coach.managements.create') }}"
                                                           class="btn btn-link btn-sm">Create Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <select class="form-control form-select @error('coaches') is-invalid @enderror" id="coaches" name="coaches[]" data-toggle="select" multiple>
                                                <option disabled>Select coaches to manage this team</option>
                                                @foreach($coaches as $coach)
                                                    <option value="{{ $coach->id }}" @selected(old('coaches') == in_array($coach->id, $coach_id)) data-avatar-src="{{ Storage::url($coach->user->foto) }}">
                                                        {{ $coach->user->firstName }} {{ $coach->user->lastName }} - {{ $coach->specializations->name }} -
                                                        @if(count($coach->teams) == 0)
                                                            No Team
                                                        @else
                                                            @foreach($coach->teams as $team)
                                                                <span class="badge badge-pill badge-danger mr-1">{{ $team->teamName }}</span>
                                                            @endforeach
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                        @error('team')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection
    @push('addon-script')
        <script>
            $(document).ready(function () {
                const idCountry = $('.country-form option:selected').val();
                const idState = {{ $coach->state_id }};
                const idCity = {{ $coach->city_id }};
                $.ajax({
                    url: "{{url('api/states')}}",
                    data: {
                        fields: 'states',
                        "filters[country_id]": idCountry,
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function (result){
                        $.each(result.data, function (key, value){
                            $('#state_id').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                        $('#state_id option[value="' + idState + '"]').attr('selected', 'selected');
                    }
                });
                $.ajax({
                    url: "{{url('api/cities')}}",
                    data: {
                        fields: 'cities',
                        "filters[state_id]": idState,
                    },
                    dataType: 'json',
                    success: function (result){
                        $.each(result.data, function (key, value){
                            $('#city_id').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                        $('#city_id option[value="' + idCity + '"]').attr('selected', 'selected');
                    }
                });
                foto.onchange = evt => {
                    preview = document.getElementById('preview');
                    preview.style.display = 'block';
                    const [file] = foto.files
                    if (file) {
                        preview.src = URL.createObjectURL(file)
                    }
                }
            });
        </script>
    @endpush
