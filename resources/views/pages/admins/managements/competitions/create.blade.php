@extends('layouts.master')
@section('title')
    Create Competition
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <!-- Modal -->
    <div class="modal fade" id="createNewTeamModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('opponentTeam-managements.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create new opponent team</h5>
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
                                                   class="custom-file-input @error('logo') is-invalid @enderror"
                                                   name="logo"
                                                   id="teamLogo">
                                            <label class="custom-file-label" for="logo">Choose file</label>
                                        </div>
                                    </div>
                                    @error('logo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group ">
                                    <label class="form-label" for="teamName">Team Name</label>
                                    <small class="text-danger">*</small>
                                    <input type="text"
                                           id="teamName"
                                           name="teamName"
                                           required
                                           value="{{ old('teamName') }}"
                                           class="form-control @error('teamName') is-invalid @enderror"
                                           placeholder="Input team's name ...">
                                    @error('teamName')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label class="form-label" for="academyName">Academy Name</label>
                                    <small class="text-danger">*</small>
                                    <input type="text"
                                           id="academyName"
                                           name="academyName"
                                           required
                                           value="{{ old('academyName') }}"
                                           class="form-control @error('academyName') is-invalid @enderror"
                                           placeholder="Input team's name ...">
                                    @error('academyName')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label class="form-label" for="coachName">Coach Name</label>
                                    <small class="text-black-100">(Optional)</small>
                                    <input type="text"
                                           id="coachName"
                                           name="coachName"
                                           required
                                           value="{{ old('coachName') }}"
                                           class="form-control @error('coachName') is-invalid @enderror"
                                           placeholder="Input team's coach name ...">
                                    @error('coachName')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group ">
                                    <label class="form-label" for="directorName">Director Name</label>
                                    <small class="text-black-100">(Optional)</small>
                                    <input type="text"
                                           id="directorName"
                                           name="directorName"
                                           required
                                           value="{{ old('directorName') }}"
                                           class="form-control @error('directorName') is-invalid @enderror"
                                           placeholder="Input team's director name ...">
                                    @error('directorName')
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
                                            <option value="{{ $ageGroup }}" @selected(old('ageGroup') == $ageGroup)>{{ $ageGroup }}</option>
                                        @endforeach
                                    </select>
                                    @error('ageGroup')
                                    <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label class="form-label" for="totalPlayers">Total Players</label>
                                    <small class="text-black-100">(Optional)</small>
                                    <div class="input-group input-group-merge">
                                        <input type="number"
                                               id="totalPlayers"
                                               name="totalPlayers"
                                               required
                                               value="{{ old('totalPlayers') }}"
                                               class="form-control @error('totalPlayers') is-invalid @enderror"
                                               placeholder="Input team's total player ...">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                Player(s)
                                            </div>
                                        </div>
                                    </div>
                                    @error('totalPlayers')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
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
                <div class="flex d-flex flex-column flex-sm-row align-items-center mb-24pt mb-md-0">
                    <div class="mb-24pt mb-sm-0 mr-sm-24pt">
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
                    <div class="list-group-item d-flex justify-content-end">
                        <a class="btn btn-secondary mx-2" href="{{ route('competition-managements.index') }}"><span class="material-icons mr-2">close</span> Cancel</a>
                        <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span> Submit</button>
                    </div>
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
                                            </div>
                                        </div>
                                        @error('logo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="type">Competition Type</label>
                                        <small class="text-danger">*</small>
                                        <select class="form-control form-select @error('type') is-invalid @enderror" id="type" name="type" required data-toggle="select">
                                            <option disabled selected>Select competition type</option>
                                            @foreach(['League', 'Tournament'] AS $type)
                                                <option value="{{ $type }}" @selected(old('ageGroup') == $type)>{{ $type }}</option>
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
                                        <div style="height: 150px"
                                             data-toggle="quill"
                                             data-quill-placeholder="Input competition's description ...">
                                        <textarea
                                            class="form-control @error('description') is-invalid @enderror"
                                            id="description"
                                            name="description">{{ old('description') }}</textarea>
                                        </div>
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
                                        <label class="form-label" for="division">Division Name</label>
                                        <small class="text-danger">*</small>
                                        <input type="text"
                                               id="division"
                                               name="division"
                                               required
                                               value="{{ old('division') }}"
                                               class="form-control @error('division') is-invalid @enderror"
                                               placeholder="Ex: U-16 Group A ...">
                                        @error('division')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="teams">Our Team</label>
                                        <small class="text-danger">*</small>
                                        @if(count($teams) == 0)
                                            <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                                 role="alert">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <i class="material-icons mr-8pt">error_outline</i>
                                                    <div class="media-body"
                                                         style="min-width: 180px">
                                                        <small class="text-black-100">Curently you haven't create any team in your academy, please create your team</small>
                                                    </div>
                                                    <div class="ml-8pt mt-2 mt-sm-0">
                                                        <a href="{{ route('team-managements.create') }}"
                                                           class="btn btn-link btn-sm">Create Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <select class="form-control form-select @error('teams') is-invalid @enderror" id="teams" name="teams" data-toggle="select">
                                                <option selected disabled>Select team to play in this division</option>
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
                                            <button type="button" class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#createNewTeamModal"><span class="material-icons mr-2">add</span> Add new team</button>
                                        </div>
                                        @if(count($opponentTeams) == 0)
                                            <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                                 role="alert">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <i class="material-icons mr-8pt">error_outline</i>
                                                    <div class="media-body"
                                                         style="min-width: 180px">
                                                        <small class="text-black-100">Curently you haven't create any opponent team, please create your opponent team</small>
                                                    </div>
{{--                                                    <div class="ml-8pt mt-2 mt-sm-0">--}}
{{--                                                        <a href="{{ route('opponentTeams-managements.create') }}"--}}
{{--                                                           class="btn btn-link btn-sm">Create Now</a>--}}
{{--                                                    </div>--}}
                                                </div>
                                            </div>
                                        @else
                                            <select class="form-control form-select @error('opponentTeams') is-invalid @enderror" id="opponentTeams" name="opponentTeams" data-toggle="select" multiple>
                                                <option disabled>Select your opponent team who play in this division</option>
                                                @foreach($opponentTeams as $team)
                                                    <option value="{{ $team->id }}" @selected(old('opponentTeams') == $team->id) data-avatar-src="{{ Storage::url($team->logo) }}">
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
                function imagePreview(inputId, imageId){
                    document.getElementById(inputId).onchange = evt => {
                        preview = document.getElementById(imageId);
                        preview.style.display = 'block';
                        const [file] = document.getElementById(inputId).files
                        if (file) {
                            preview.src = URL.createObjectURL(file)
                        }
                    }
                }

                imagePreview('logo', 'preview');
                imagePreview('teamLogo', 'teamPreview');


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
