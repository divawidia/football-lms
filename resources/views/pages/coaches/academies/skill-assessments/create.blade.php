@extends('layouts.master')
@section('title')
    Update {{ $data->user->firstName  }} {{ $data->user->lastName  }} Skill Stats
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-0">
                @yield('title')
            </h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('coach.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('coach.skill-assessments.index') }}">Skill Assessments</a></li>
                <li class="breadcrumb-item"><a href="{{ route('coach.skill-assessments.skill-stats', $data->id) }}">{{ $data->user->firstName  }} {{ $data->user->lastName  }}</a></li>
                <li class="breadcrumb-item active">
                    Update
                </li>
            </ol>
        </div>
    </div>
    <div class="container page__container page-section">
        <div class="list-group">
            <form method="POST" action="{{ route('coach.skill-assessments.store',$data->id) }}">
                @csrf
                <div class="list-group-item">
                    <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="controlling">Controlling : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="controlling" name="controlling" class="skills-range-slider" value="{{ old('controlling') }}" required/>
                                </div>
                            </div>
                            @error('controlling')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="recieving">Receiving : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="recieving" name="recieving" class="skills-range-slider" value="{{ old('recieving') }}" required/>
                                </div>
                            </div>
                            @error('recieving')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="dribbling">Dribbling : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="dribbling" name="dribbling" class="skills-range-slider" value="{{ old('dribbling') }}" required/>
                                </div>
                            </div>
                            @error('dribbling')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="passing">Passing : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="passing" name="passing" class="skills-range-slider" value="{{ old('passing') }}" required/>
                                </div>
                            </div>
                            @error('passing')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="shooting">Shooting : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="shooting" name="shooting" class="skills-range-slider" value="{{ old('shooting') }}" required/>
                                </div>
                            </div>
                            @error('shooting')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="crossing">Crossing : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="crossing" name="crossing" class="skills-range-slider" value="{{ old('crossing') }}" required/>
                                </div>
                            </div>
                            @error('crossing')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="turning">Turning : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="turning" name="turning" class="skills-range-slider" value="{{ old('turning') }}" required/>
                                </div>
                            </div>
                            @error('turning')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="ballHandling">Ball Handling : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="ballHandling" name="ballHandling" class="skills-range-slider" value="{{ old('ballHandling') }}" required/>
                                </div>
                            </div>
                            @error('ballHandling')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="powerKicking">Power Kicking : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="powerKicking" name="powerKicking" class="skills-range-slider" value="{{ old('powerKicking') }}" required/>
                                </div>
                            </div>
                            @error('powerKicking')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="goalKeeping">Goal Keeping : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="goalKeeping" name="goalKeeping" class="skills-range-slider" value="{{ old('goalKeeping') }}" required/>
                                </div>
                            </div>
                            @error('goalKeeping')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="offensivePlay">Offensive Play : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="offensivePlay" name="offensivePlay" class="skills-range-slider" value="{{ old('offensivePlay') }}" required/>
                                </div>
                            </div>
                            @error('offensivePlay')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row d-flex flex-row align-items-center mb-2">
                                <div class="col-md-3">
                                    <label class="form-label" for="defensivePlay">Defensive Play : </label>
                                    <small class="text-danger">*</small>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="defensivePlay" name="defensivePlay" class="skills-range-slider" value="{{ old('defensivePlay') }}" required/>
                                </div>
                            </div>
                            @error('defensivePlay')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".skills-range-slider").ionRangeSlider({
                min: 0,
                max: 100,
                step: 25,
                grid: true,
                values: [
                    "Poor", "Needs Work", "Average Fair", "Good", "Excellent"
                ]
            });
        });
    </script>
@endpush
