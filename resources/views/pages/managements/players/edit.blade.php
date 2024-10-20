@extends('layouts.master')
@section('title')
    Edit {{ $fullName }} Profile
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
                <li class="breadcrumb-item"><a href="{{ route('player-managements.index') }}">Players Management</a>
                </li>
                <li class="breadcrumb-item"><a
                        href="{{ route('player-managements.show', ['player' => $data->id]) }}">{{ $fullName }}</a>
                </li>
                <li class="breadcrumb-item active">
                    Edit
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('player-managements.update', ['player' => $data]) }}" method="post"
                      enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="page-separator">
                        <div class="page-separator__text">Account Profile</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="form-label">Profile photo</label>
                            <small class="text-black-100">(Optional)</small>
                            <div class="media align-items-center mb-2">
                                <img src="{{ Storage::url($data->user->foto) }}"
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
                                               id="foto">
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
                                       value="{{ old('firstName', $data->user->firstName) }}"
                                       placeholder="Input player's first name ...">
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
                                       value="{{ old('lastName', $data->user->lastName) }}"
                                       placeholder="Input player's last name ...">
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
                                       value="{{ old('dob', $data->user->dob) }}"
                                       placeholder="Input player's date of birth">
                                @error('dob')
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
                                       value="{{ old('email', $data->user->email) }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Input player account's email address ...">
                                @error('email')
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
                                    <option disabled selected>Select player's gender</option>
                                    @foreach(['male' => 'Male', 'female' => 'Female', 'others' => 'Others'] AS $jenisKelamin => $jenisKelaminLabel)
                                        <option
                                            value="{{ $jenisKelamin }}" @selected(old('gender', $data->user->gender) == $jenisKelamin)>{{ $jenisKelaminLabel }}</option>
                                    @endforeach
                                </select>
                                @error('gender')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="joinDate">Join Date</label>
                                <small class="text-danger">*</small>
                                <input type="date"
                                       id="joinDate"
                                       name="joinDate"
                                       required
                                       value="{{ old('joinDate', $data->joinDate) }}"
                                       class="form-control @error('joinDate') is-invalid @enderror"
                                       placeholder="Input player's join date ...">
                                @error('joinDate')
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
                                    placeholder="Input account's address ...">{{old('address', $data->user->address)}}</textarea>
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
                                       value="{{ old('phoneNumber', $data->user->phoneNumber) }}"
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
                                       value="{{ old('zipCode', $data->user->zipCode) }}"
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
                                            value="{{ $country['id'] }}" @selected($data->user->country_id == $country['id'])>{{ $country['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('country')
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
                        <div class="page-separator__text">Skills Information</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label" for="positionId">Position</label>
                                <small class="text-danger">*</small>
                                <select class="form-control form-select @error('position') is-invalid @enderror"
                                        id="positionId" name="positionId" required>
                                    <option disabled selected>Select player's position</option>
                                    @foreach($positions AS $position)
                                        <option
                                            value="{{ $position->id }}" @selected(old('positionId', $data->positionId) == $position->id)>{{ $position->name }}</option>
                                    @endforeach
                                </select>
                                @error('positionId')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label" for="skill">Skills</label>
                                <small class="text-danger">*</small>
                                <select class="form-control form-select @error('skill') is-invalid @enderror" id="skill"
                                        name="skill" required>
                                    <option disabled selected>Select player's skill</option>
                                    @foreach(['Beginner', 'Intermediate', 'Advance'] AS $skill)
                                        <option
                                            value="{{ $skill }}" @selected(old('skill', $data->skill) == $skill)>{{ $skill }}</option>
                                    @endforeach
                                </select>
                                @error('skill')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label" for="strongFoot">Strong Foot</label>
                                <small class="text-danger">*</small>
                                <select class="form-control form-select @error('strongFoot') is-invalid @enderror"
                                        id="strongFoot" name="strongFoot" required>
                                    <option disabled selected>Select player's strong foot</option>
                                    @foreach(['left' => 'Left', 'right' => 'Right'] AS $foot => $footLabel)
                                        <option
                                            value="{{ $foot }}" @selected(old('strongFoot', $data->strongFoot) == $foot)>{{ $footLabel }}</option>
                                    @endforeach
                                </select>
                                @error('strongFoot')
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
                                           value="{{ old('height', $data->height) }}"
                                           placeholder="Input player's height ...">
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
                                           value="{{ old('weight', $data->weight) }}"
                                           placeholder="Input player's weight ...">
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
                    <a class="btn btn-secondary mx-2" href="{{ url()->previous() }}"><span class="material-icons mr-2">close</span>
                        Cancel</a>
                    <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span> Submit
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
            const idCountry = $('.country-form option:selected').val();
            const idState = {{ $data->user->state_id }};
            const idCity = {{ $data->user->city_id }};
            $.ajax({
                url: "{{url('api/states')}}",
                data: {
                    fields: 'states',
                    "filters[country_id]": idCountry,
                },
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    $.each(result.data, function (key, value) {
                        $('#state_id').append('<option value="' + value.id + '">' + value.name + '</option>');
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
                success: function (result) {
                    $.each(result.data, function (key, value) {
                        $('#city_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    $('#city_id option[value="' + idCity + '"]').attr('selected', 'selected');
                }
            });

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
                        $('#city_id').html('<option disabled selected>Select City</option>');
                        $.each(result.data, function (key, value) {
                            $('#state_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });
            $('#state_id').on('change', function () {
                const idState = this.value;
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
