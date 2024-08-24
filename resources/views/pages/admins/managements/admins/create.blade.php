@extends('layouts.master')
@section('title')
    Create Admin Account
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
                            <li class="breadcrumb-item"><a href="{{ route('admin-managements.index') }}">Admins Management</a></li>
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
                <form action="{{ route('admin-managements.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="list-group-item d-flex justify-content-end">
                        <a class="btn btn-secondary mx-2" href="{{ route('admin-managements.index') }}"><span class="material-icons mr-2">close</span> Cancel</a>
                        <button type="button" class="btn btn-primary"><span class="material-icons mr-2">add</span> Submit</button>
                    </div>
                    <div class="list-group-item">
                        <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                            <div class="page-separator">
                                <div class="page-separator__text">Account Profile</div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="form-label">Profile photo</label>
                                    <div class="media align-items-center mb-2">
                                        <a href="" class="media-left mr-16pt">
                                            <img src="{{ Storage::url('images/undefined-user.png') }}"
                                                 alt="people"
                                                 width="54"
                                                 height="54"
                                                 id="preview"
                                                 class="rounded-circle" />
                                        </a>
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
                                        <input type="text"
                                               class="form-control @error('firstName') is-invalid @enderror"
                                               id="firstName"
                                               name="firstName"
                                               placeholder="Input account's first name ...">
                                        @error('firstName')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="lastName">Last name</label>
                                        <input type="text"
                                               class="form-control @error('lastName') is-invalid @enderror"
                                               id="lastName"
                                               name="lastName"
                                               placeholder="Input account's last name ...">
                                        @error('lastName')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="dob">Date of Birth</label>
                                        <input type="date"
                                               class="form-control @error('dob') is-invalid @enderror"
                                               id="dob"
                                               name="dob"
                                               placeholder="Input account's date of birth">
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
                                        <input type="email"
                                               id="email"
                                               name="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               placeholder="Input account's email address ...">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="password">Password</label>
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               name="password"
                                               id="password"
                                               placeholder="Input account's password ...">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="password-confirm">Confirm Password</label>
                                        <input type="password"
                                               class="form-control @error('password_confirmation') is-invalid @enderror"
                                               name="password_confirmation" required id="password-confirm"
                                               placeholder="Retype inputted password ...">
                                        @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="gender">Gender</label>
                                        <select class="form-control form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            <option disabled selected>Select Gender</option>
                                            @foreach(['male' => 'Male', 'female' => 'Female', 'others' => 'Others'] AS $jenisKelamin => $jenisKelaminLabel)
                                                <option value="{{ $jenisKelamin }}">{{ $jenisKelaminLabel }}</option>
                                            @endforeach
                                        </select>
                                        @error('gender')
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
                                        <input type="text"
                                               class="form-control @error('address') is-invalid @enderror"
                                               name="address"
                                               id="address"
                                               placeholder="Input account's address ...">
                                        @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label" for="phoneNumber">Phone Number</label>
                                        <input type="text"
                                               id="phoneNumber"
                                               name="phoneNumber"
                                               class="form-control @error('phoneNumber') is-invalid @enderror"
                                               placeholder="Input account's phone number ...">
                                        @error('phoneNumber')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="zipCode">Zip <Code></Code></label>
                                        <input type="number"
                                               id="zipCode"
                                               name="zipCode"
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
                                        <label class="form-label" for="country">Country</label>
                                        <select class="form-control form-select country-form @error('country') is-invalid @enderror" id="country" name="country" required>
                                            <option selected disabled>Select Country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country['id'] }}">{{ $country['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('country')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="state">State</label>
                                        <select class="form-control form-select @error('state') is-invalid @enderror" id="state" name="state" required>
                                            <option disabled selected>Select State</option>
                                        </select>
                                        @error('state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="city">City</label>
                                        <select class="form-control form-select @error('city') is-invalid @enderror" id="city" name="city" required>
                                            <option disabled selected>Select State</option>
                                        </select>
                                        @error('city')
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
                $('.country-form').on('change', function (){
                    var idCountry = this.value;
                    $('#state').html('');
                    $.ajax({
                        url: "https://laravel-world.com/api/states?fields=states&filters[country_id]=" + idCountry,
                        type: 'GET',
                        dataType: 'json',
                        success: function (result){
                            $('#state').html('<option disabled selected>Select State</option>');
                            $.each(result.data, function (key, value){
                                $('#state').append('<option value="'+value.id+'">'+value.name+'</option>');
                            });
                        }
                    });
                });
                $('#state').on('change', function (){
                    var idState = this.value;
                    $('#city').html('');
                    $.ajax({
                        url: "https://laravel-world.com/api/states?fields=cities&filters[state_id]=" + idState,
                        type: 'GET',
                        dataType: 'json',
                        success: function (result){
                            $('#city').html('<option disabled selected>Select City</option>');
                            $.each(result.data, function (key, value){
                                $('#city').append('<option value="'+value.id+'">'+value.name+'</option>');
                            });
                        }
                    });
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
