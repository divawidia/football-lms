@extends('layouts.master')
@section('title')
    Create Competition
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <!-- Modal Create Opponent Team -->
    <div class="modal fade" id="createNewOpponentTeamModal" tabindex="-1" aria-labelledby="createNewOpponentTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('opponentTeam-managements.apiStore') }}" method="post" enctype="multipart/form-data" id="formCreateNewOpponentTeam">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create new opponent team</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Team Logo</label>
                                <small class="text-black-100">(Optional)</small>
                                <div class="media align-items-center mb-2">
                                    <img src="{{ Storage::url('images/undefined-user.png') }}"
                                         alt="people"
                                         width="54"
                                         height="54"
                                         id="opponentTeamPreview"
                                         class="mr-16pt rounded-circle img-object-fit-cover" />
                                    <div class="media-body">
                                        <div class="custom-file">
                                            <input type="file"
                                                   class="custom-file-input"
                                                   name="logo"
                                                   id="add_logo">
                                            <label class="custom-file-label" for="logo">Choose file</label>
                                            <span class="invalid-feedback logo_error" role="alert">
                                                <strong></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group ">
                                    <label class="form-label" for="add_teamName">Team Name</label>
                                    <small class="text-danger">*</small>
                                    <input type="text"
                                           id="add_teamName"
                                           name="teamName"
                                           required
                                           value="{{ old('teamName') }}"
                                           class="form-control"
                                           placeholder="Input team's name ...">
                                    <span class="invalid-feedback teamName_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="add_ageGroup">Age Group</label>
                                    <small class="text-danger">*</small>
                                    <select class="form-control form-select" id="add_ageGroup" name="ageGroup" required data-toggle="select">
                                        <option disabled selected>Select player's age group</option>
                                        @foreach(['U-6', 'U-7', 'U-8', 'U-9', 'U-10', 'U-11', 'U-12', 'U-13', 'U-14', 'U-15', 'U-16', 'U-17', 'U-18', 'U-19', 'U-20', 'U-21'] AS $ageGroup)
                                            <option value="{{ $ageGroup }}" @selected(old('ageGroup') == $ageGroup)>{{ $ageGroup }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback ageGroup_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Create Team -->
    <div class="modal fade" id="createNewTeamModal" tabindex="-1" aria-labelledby="createNewTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('team-managements.apiStore') }}" method="post" enctype="multipart/form-data" id="formCreateNewTeam">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create new team</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Team Logo</label>
                                <small class="text-black-100">(Optional)</small>
                                <div class="media align-items-center mb-2">
                                    <img src="{{ Storage::url('images/undefined-user.png') }}"
                                         alt="people"
                                         width="54"
                                         height="54"
                                         id="teamPreview"
                                         class="mr-16pt rounded-circle img-object-fit-cover" />
                                    <div class="media-body">
                                        <div class="custom-file">
                                            <input type="file"
                                                   class="custom-file-input"
                                                   name="logo"
                                                   id="add_logoTeam">
                                            <label class="custom-file-label" for="logo">Choose file</label>
                                            <span class="invalid-feedback logo_error" role="alert">
                                                <strong></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group ">
                                    <label class="form-label" for="add_teamName">Team Name</label>
                                    <small class="text-danger">*</small>
                                    <input type="text"
                                           id="add_teamName"
                                           name="teamName"
                                           required
                                           value="{{ old('teamName') }}"
                                           class="form-control"
                                           placeholder="Input team's name ...">
                                    <span class="invalid-feedback teamName_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="add_ageGroup">Age Group</label>
                                    <small class="text-danger">*</small>
                                    <select class="form-control form-select" id="add_ageGroup" name="ageGroup" required data-toggle="select">
                                        <option disabled selected>Select player's age group</option>
                                        @foreach(['U-6', 'U-7', 'U-8', 'U-9', 'U-10', 'U-11', 'U-12', 'U-13', 'U-14', 'U-15', 'U-16', 'U-17', 'U-18', 'U-19', 'U-20', 'U-21'] AS $ageGroup)
                                            <option value="{{ $ageGroup }}" @selected(old('ageGroup') == $ageGroup)>{{ $ageGroup }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback ageGroup_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="add_players">Players</label>
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
                                        <select class="form-control form-select" id="add_players" name="players[]" data-toggle="select" multiple>
                                            <option disabled>Select players to play in this team</option>
                                            @foreach($players as $player)
                                                <option value="{{ $player->id }}" @selected(old('players') == $player->id) data-avatar-src="{{ Storage::url($player->user->foto) }}">
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
                                    <span class="invalid-feedback players_error" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="add_coaches">Coaches</label>
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
                                        <select class="form-control form-select" id="add_coaches" name="coaches[]" data-toggle="select" multiple>
                                            <option disabled>Select coaches to manage this team</option>
                                            @foreach($coaches as $coach)
                                                <option value="{{ $coach->id }}" @selected(old('coaches') == $coach->id) data-avatar-src="{{ Storage::url($coach->user->foto) }}">
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
                                    <span class="invalid-feedback coaches_error" role="alert">
                                    <strong></strong>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left">
                <div class="flex d-flex flex-column flex-sm-row align-items-center">
                    <div class="mb-24pt mb-sm-0 mr-sm-24pt text-sm-start">
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
            </div>
        </div>

        <div class="container page__container page-section">
            <div class="list-group">
                <form action="{{ route('competition-managements.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="list-group-item">
                        <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                            <div class="page-separator">
                                <div class="page-separator__text">Competition Info</div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="form-label">Competition Logo</label>
                                    <small class="text-black-100">(Optional)</small>
                                    <div class="media align-items-center mb-2">
                                            <img src="{{ Storage::url('images/undefined-user.png') }}"
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
                                                @error('logo')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="type">Competition Type</label>
                                        <small class="text-danger">*</small>
                                        <select class="form-control form-select @error('type') is-invalid @enderror" id="type" name="type" required data-toggle="select">
                                            <option disabled selected>Select competition type</option>
                                            @foreach(['League', 'Tournament'] AS $type)
                                                <option value="{{ $type }}" @selected(old('type') == $type)>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                        @error('ageGroup')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="startDate">Start Date</label>
                                        <small class="text-danger">*</small>
                                        <input type="date"
                                               class="form-control @error('startDate') is-invalid @enderror"
                                               id="startDate"
                                               name="startDate"
                                               required
                                               value="{{ old('startDate') }}">
                                        @error('startDate')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="contactName">Contact Name</label>
                                        <small class="text-sm">(Optional)</small>
                                        <input type="text"
                                               class="form-control @error('contactName') is-invalid @enderror"
                                               id="contactName"
                                               name="contactName"
                                               value="{{ old('contactName') }}"
                                            placeholder="Input competition's contact name ...">
                                        @error('contactName')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label" for="name">Competition Name</label>
                                        <small class="text-danger">*</small>
                                        <input type="text"
                                               id="name"
                                               name="name"
                                               required
                                               value="{{ old('name') }}"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="Input competition's name ...">
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="location">Location</label>
                                        <small class="text-danger">*</small>
                                        <input type="text"
                                               id="location"
                                               name="location"
                                               required
                                               value="{{ old('location') }}"
                                               class="form-control @error('location') is-invalid @enderror"
                                               placeholder="Input competition's location ...">
                                        @error('location')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="endDate">End Date</label>
                                        <small class="text-danger">*</small>
                                        <input type="date"
                                               class="form-control @error('endDate') is-invalid @enderror"
                                               id="endDate"
                                               name="endDate"
                                               required
                                               value="{{ old('endDate') }}">
                                        @error('endDate')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="contactPhone">Contact Phone</label>
                                        <small class="text-sm">(Optional)</small>
                                        <input type="text"
                                               class="form-control @error('contactPhone') is-invalid @enderror"
                                               id="contactPhone"
                                               name="contactPhone"
                                               value="{{ old('contactPhone') }}"
                                               placeholder="Input competition's contact name ...">
                                        @error('contactPhone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="description">Description</label>
                                        <small class="text-sm">(Optional)</small>
                                        <textarea
                                            class="ckeditor form-control h-100 @error('description') is-invalid @enderror"
                                            id="description"
                                            name="description" rows="10">{{ old('description') }}</textarea>
                                        @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="page-separator">
                                <div class="page-separator__text">Division</div>
{{--                                <a href="#" class="btn btn-primary ml-auto btn-sm" id="add-division">--}}
{{--                                    <span class="material-icons mr-2">--}}
{{--                                        add--}}
{{--                                    </span>--}}
{{--                                    Add Another Division--}}
{{--                                </a>--}}
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label" for="groupName">Division Name</label>
                                        <small class="text-danger">*</small>
                                        <input type="text"
                                               id="groupName"
                                               name="groupName"
                                               required
                                               value="{{ old('groupName') }}"
                                               class="form-control @error('groupName') is-invalid @enderror"
                                               placeholder="Ex: U-16 Group A ...">
                                        @error('groupName')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="d-flex flex-row align-items-center mb-2">
                                            <label class="form-label mb-0" for="teams">Our Teams</label>
                                            <small class="text-danger">*</small>
                                            <button type="button" id="addNewTeam" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add new team</button>
                                        </div>

                                        @if(count($teams) == 0)
                                            <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                                 role="alert">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <i class="material-icons mr-8pt">error_outline</i>
                                                    <div class="media-body" style="min-width: 180px">
                                                        <small class="text-black-100">Curently you haven't create any team in your academy, please create your team</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <select class="form-control form-select @error('teams') is-invalid @enderror" id="teams" name="teams[]" data-toggle="select">
                                                <option selected disabled>Select our team to play in this division</option>
                                                @foreach($teams as $team)
                                                    <option value="{{ $team->id }}" @selected(old('teams') == $team->id) data-avatar-src="{{ Storage::url($team->logo) }}">
                                                        {{ $team->teamName }}
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
                                    <div class="form-group mb-3">
                                        <div class="d-flex flex-row align-items-center mb-2">
                                            <label class="form-label mb-0" for="opponentTeams">Opponent Teams</label>
                                            <small class="text-danger">*</small>
                                            <a href="" id="addNewOpponentTeam" class="btn btn-primary btn-sm ml-auto"><span class="material-icons mr-2">add</span> Add new team</a>
                                        </div>
                                        @if(count($opponentTeams) == 0)
                                            <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                                 role="alert">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <i class="material-icons mr-8pt">error_outline</i>
                                                    <div class="media-body"
                                                         style="min-width: 180px">
                                                        <small class="text-black-100">Currently you haven't created any opponent team, please create your opponent team</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <select class="form-control form-select @error('opponentTeams') is-invalid @enderror" id="opponentTeams" name="opponentTeams[]" data-toggle="select" multiple>
                                                <option disabled>Select your opponent team who play in this division</option>
                                                @foreach($opponentTeams as $team)
                                                    <option value="{{ $team->id }}" @selected(old('opponentTeams') == $team->id) data-avatar-src="{{ Storage::url($team->logo) }}">
                                                    {{ $team->teamName }} ~ {{ $team->ageGroup }}
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
                    <div class="list-group-item d-flex justify-content-end">
                        <a class="btn btn-secondary mx-2" href="{{ route('competition-managements.index') }}"><span class="material-icons mr-2">close</span> Cancel</a>
                        <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    @endsection
    @push('addon-script')
        <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
        <script>
            $(document).ready(function () {
                $('#addNewOpponentTeam').on('click', function(e) {
                    e.preventDefault();
                    $('#createNewOpponentTeamModal').modal('show');
                });
                $('#addNewTeam').on('click', function(e) {
                    e.preventDefault();
                    $('#createNewTeamModal').modal('show');
                });

                // insert data opponent team
                $('#formCreateNewOpponentTeam').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        method: $(this).attr('method'),
                        url: $(this).attr('action'),
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        success: function(res) {
                            $('#createNewOpponentTeamModal').modal('hide');
                            Swal.fire({
                                title: 'Team successfully added!',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: "#1ac2a1",
                                confirmButtonText:
                                    'Ok!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr) {
                            const response = JSON.parse(xhr.responseText);
                            $.each(response.errors, function(key, val) {
                                $('span.' + key + '_error').text(val[0]);
                                $("input#add_" + key).addClass('is-invalid');
                            });
                        }
                    });
                });

                // insert data team
                $('#formCreateNewTeam').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        method: $(this).attr('method'),
                        url: $(this).attr('action'),
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        success: function(res) {
                            $('#createNewTeamModal').modal('hide');
                            Swal.fire({
                                title: 'Team successfully added!',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: "#1ac2a1",
                                confirmButtonText:
                                    'Ok!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr) {
                            const response = JSON.parse(xhr.responseText);
                            $.each(response.errors, function(key, val) {
                                $('span.' + key + '_error').text(val[0]);
                                $("input#add_" + key).addClass('is-invalid');
                            });
                        }
                    });
                });

                const editor = document.querySelector('.ckeditor');
                ClassicEditor.create(editor, {
                    toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList'],
                }).catch(error => {
                    alert(error);
                });


                // var i=1;
                // $('#add').click(function(){
                //     i++;
                //     $('#dynamic_field').append(' <div class="row pt-3" id="row'+i+'"><div class="col-10"><input type="text" name="features[]" placeholder="Masukan fitur" class="form-control features_list" /></div><div class="col-auto"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove"><i class="bx bx-x"></i></button></div></div>');
                // });
                //
                // $(document).on('click', '.btn_remove', function(){
                //     var button_id = $(this).attr("id");
                //     $('#row'+button_id+'').remove();
                // });

            });
        </script>
    @endpush
