@extends('layouts.master')
@section('title')
    Create Coach Account
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
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('coach-managements.index') }}">Coach Management</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('coach-managements.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="page-separator">
                        <div class="page-separator__text">Account Profile</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="form-label">Profile photo</label>
                            <small class="text-black-100">(Optional)</small>
                            <div class="media align-items-center mb-2">
                                <img src="{{ Storage::url('images/undefined-user.png') }}"
                                     alt="people"
                                     width="54"
                                     height="54"
                                     id="preview"
                                     class="mr-16pt rounded-circle img-object-fit-cover"/>
                                <div class="media-body">
                                    <div class="custom-file">
                                        <input type="file"
                                               class="custom-file-input @error('foto') is-invalid @enderror"
                                               name="foto"
                                               id="foto"
                                               accept="image/jpg, image/jpeg, image/png">
                                        <label class="custom-file-label" for="foto">Choose file</label>
                                    </div>
                                </div>
                                @error('foto')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="firstName">First name</label>
                                <small class="text-danger">*</small>
                                <input type="text"
                                       class="form-control @error('firstName') is-invalid @enderror"
                                       id="firstName"
                                       name="firstName"
                                       required
                                       value="{{ old('firstName') }}"
                                       placeholder="Input coach's first name ...">
                                @error('firstName')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="lastName">Last name</label>
                                <small class="text-danger">*</small>
                                <input type="text"
                                       class="form-control @error('lastName') is-invalid @enderror"
                                       id="lastName"
                                       name="lastName"
                                       required
                                       value="{{ old('lastName') }}"
                                       placeholder="Input coach's last name ...">
                                @error('lastName')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="dob">Date of Birth</label>
                                <small class="text-danger">*</small>
                                <input type="date"
                                       class="form-control @error('dob') is-invalid @enderror"
                                       id="dob"
                                       name="dob"
                                       required
                                       value="{{ old('dob') }}"
                                       placeholder="Input coach's date of birth">
                                @error('dob')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label" for="team">Teams Managed</label>
                                <small class="text-black-100">(Optional)</small>
                                @if(count($teams) == 0)
                                    <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                         role="alert">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <i class="material-icons mr-8pt">error_outline</i>
                                            <div class="media-body"
                                                 style="min-width: 180px">
                                                <small class="text-black-100">Curently you haven't create any team in
                                                    your academy, please create your team</small>
                                            </div>
                                            <div class="ml-8pt mt-2 mt-sm-0">
                                                <a href="{{ route('team-managements.create') }}"
                                                   class="btn btn-link btn-sm">Create Now</a>
                                            </div>
                                        </div>
                                    </div>
                                @elseif(count($teams) > 0)
                                    <select class="form-control form-select @error('team') is-invalid @enderror"
                                            id="team" name="team[]" data-toggle="select" multiple>
                                        <option disabled>Select Managed Teams</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}"
                                                    @selected(old('team') == $team->id) data-avatar-src="{{ Storage::url($team->logo) }}">
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
                            <div class="form-group mb-4">
                                <label class="form-label" for="email">Email address</label>
                                <small class="text-danger">*</small>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       required
                                       value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Input coach account's email address ...">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="password">Password</label>
                                <small class="text-danger">*</small>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       id="password"
                                       required
                                       value="{{ old('password') }}"
                                       placeholder="Input coach account's password ...">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="password-confirm">Confirm Password</label>
                                <small class="text-danger">*</small>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password_confirmation" required id="password-confirm"
                                       placeholder="Retype inputted password ...">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="gender">Gender</label>
                                <small class="text-danger">*</small>
                                <select class="form-control form-select @error('gender') is-invalid @enderror"
                                        id="gender" name="gender" required>
                                    <option disabled selected>Select coach's gender</option>
                                    @foreach(['male' => 'Male', 'female' => 'Female', 'others' => 'Others'] AS $jenisKelamin => $jenisKelaminLabel)
                                        <option
                                            value="{{ $jenisKelamin }}" @selected(old('gender') == $jenisKelamin)>{{ $jenisKelaminLabel }}</option>
                                    @endforeach
                                </select>
                                @error('gender')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="hireDate">Hire Date</label>
                                <small class="text-danger">*</small>
                                <input type="date"
                                       id="hireDate"
                                       name="hireDate"
                                       required
                                       value="{{ old('hireDate') }}"
                                       class="form-control @error('hireDate') is-invalid @enderror"
                                       placeholder="Input coach's hire date ...">
                                @error('hireDate')
                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="page-separator mt-3">
                        <div class="page-separator__text">Contact Information</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="address">Address</label>
                                <small class="text-danger">*</small>
                                <textarea
                                    class="form-control @error('address') is-invalid @enderror"
                                    name="address"
                                    id="address"
                                    required
                                    placeholder="Input account's address ...">{{old('address')}}</textarea>
                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label" for="phoneNumber">Phone Number</label>
                                <small class="text-danger">*</small>
                                <input type="text"
                                       id="phoneNumber"
                                       name="phoneNumber"
                                       required
                                       value="{{ old('phoneNumber') }}"
                                       class="form-control @error('phoneNumber') is-invalid @enderror"
                                       placeholder="Input account's phone number ...">
                                @error('phoneNumber')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="zipCode">Zip Code</label>
                                <small class="text-danger">*</small>
                                <input type="number"
                                       id="zipCode"
                                       name="zipCode"
                                       required
                                       value="{{ old('zipCode') }}"
                                       class="form-control @error('zipCode') is-invalid @enderror"
                                       placeholder="Input address zip code ...">
                                @error('zipCode')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="country_id">Country</label>
                                <small class="text-danger">*</small>
                                <select
                                    class="form-control form-select country-form @error('country_id') is-invalid @enderror"
                                    id="country_id" name="country_id" required>
                                    <option selected disabled>Select Country</option>
                                    @foreach($countries as $country)
                                        <option
                                            value="{{ $country['id'] }}">{{ $country['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="state_id">State</label>
                                <small class="text-danger">*</small>
                                <select class="form-control form-select @error('state_id') is-invalid @enderror"
                                        id="state_id" name="state_id" required>
                                    <option disabled selected>Select State</option>
                                </select>
                                @error('state_id')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="city_id">City</label>
                                <small class="text-danger">*</small>
                                <select class="form-control form-select @error('city_id') is-invalid @enderror"
                                        id="city_id" name="city_id" required>
                                    <option disabled selected>Select City</option>
                                </select>
                                @error('city_id')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="page-separator mt-3">
                        <div class="page-separator__text">Coaching Information</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="certificationLevel">Certification Level</label>
                                <small class="text-danger">*</small>
                                <select
                                    class="form-control form-select @error('certificationLevel') is-invalid @enderror"
                                    id="certificationLevel" name="certificationLevel" required>
                                    <option disabled selected>Select coach's certification level</option>
                                    @foreach($certifications AS $certification)
                                        <option
                                            value="{{ $certification->id }}" @selected(old('certificationLevel') == $certification->id)>{{ $certification->name }}</option>
                                    @endforeach
                                </select>
                                @error('certificationLevel')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="specialization">Specialization</label>
                                <small class="text-danger">*</small>
                                <select class="form-control form-select @error('specialization') is-invalid @enderror"
                                        id="specialization" name="specialization" required>
                                    <option disabled selected>Select coach's specialization</option>
                                    @foreach($specializations AS $specialization)
                                        <option
                                            value="{{ $specialization->id }}" @selected(old('specialization') == $specialization->id)>{{ $specialization->name }}</option>
                                    @endforeach
                                </select>
                                @error('specialization')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="page-separator mt-3">
                        <div class="page-separator__text">Physic Information</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="height">Height</label>
                                <small class="text-danger">*</small>
                                <div class="input-group input-group-merge">
                                    <input type="number"
                                           class="form-control @error('height') is-invalid @enderror"
                                           id="height"
                                           name="height"
                                           required
                                           value="{{ old('height') }}"
                                           placeholder="Input coach's height ...">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            CM
                                        </div>
                                    </div>
                                </div>
                                @error('height')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="weight">Weight</label>
                                <small class="text-danger">*</small>
                                <div class="input-group input-group-merge">
                                    <input type="number"
                                           class="form-control @error('weight') is-invalid @enderror"
                                           id="weight"
                                           name="weight"
                                           required
                                           value="{{ old('weight') }}"
                                           placeholder="Input coach's weight ...">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            KG
                                        </div>
                                    </div>
                                </div>
                                @error('weight')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="page-separator"></div>
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-secondary mx-2" href="{{ url()->previous() }}"><span
                                class="material-icons mr-2">close</span> Cancel</a>
                        <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span>
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            $('.country-form').on('change', function () {
                const idCountry = this.value;
                $.ajax({
                    url: "{{url('api/states')}}",
                    data: {
                        fields: 'states',
                        "filters[country_id]": idCountry,
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function (result) {
                        $('#state_id').html('<option disabled selected>Select State</option>');
                        $.each(result.data, function (key, value) {
                            $('#state_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });
            $('#state_id').on('change', function () {
                var idState = this.value;
                $.ajax({
                    url: "{{url('api/cities')}}",
                    data: {
                        fields: 'cities',
                        "filters[state_id]": idState,
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#city_id').html('<option disabled selected>Select City</option>');
                        $.each(result.data, function (key, value) {
                            $('#city_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });
        });
    </script>
@endpush