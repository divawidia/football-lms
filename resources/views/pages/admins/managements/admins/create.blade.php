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
                        <h2 class="mb-0">@yield('title')</h2>
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
                                                 class="rounded-circle" />
                                        </a>
                                        <div class="media-body">
                                            <div class="custom-file">
                                                <input type="file"
                                                       class="custom-file-input"
                                                       name="foto"
                                                       id="inputGroupFile01">
                                                <label class="custom-file-label"
                                                       for="inputGroupFile01">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">First name</label>
                                        <input type="text"
                                               class="form-control"
                                               name="firstName"
                                               placeholder="Input account's first name ...">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Last name</label>
                                        <input type="text"
                                               class="form-control"
                                               name="lastName"
                                               placeholder="Input account's last name ...">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date"
                                               class="form-control"
                                               name="dob"
                                               placeholder="Input account's date of birth">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label">Email address</label>
                                        <input type="email"
                                               class="form-control"
                                               placeholder="Input account's email address ...">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Password</label>
                                        <input type="password"
                                               class="form-control"
                                               name="password"
                                               placeholder="Input account's password ...">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password"
                                               class="form-control"
                                               name="password_confirmation" required id="password-confirm"
                                               placeholder="Retype inputted password ...">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="gender">Gender</label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            @foreach(['male' => 'Male', 'female' => 'Female', 'others' => 'Others'] AS $jenisKelamin => $jenisKelaminLabel)
                                                <option value="{{ $jenisKelamin }}">{{ $jenisKelaminLabel }}</option>
                                            @endforeach
                                        </select>
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
                                               class="form-control"
                                               name="address"
                                               id="address"
                                               placeholder="Input account's address ...">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="country">Country</label>
                                        <select class="form-control form-select @error('country') is-invalid @enderror" id="country" name="country" required>
                                            @foreach($countries as $country)
                                                <option value="{{ $country['name'] }}">{{ $country['name'] }}</option>
                                            @endforeach
                                        </select>
                                    <div class="form-group">
                                        <label class="form-label">State</label>
                                        <input type="text"
                                               class="form-control"
                                               value="Alexander"
                                               placeholder="Your first name ...">
                                    </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Phone Number</label>
                                        <input type="email"
                                               class="form-control"
                                               placeholder="Retype inputted password ...">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label">City</label>
                                        <input type="email"
                                               class="form-control"
                                               placeholder="Input account's email address ...">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Zip <Code></Code></label>
                                        <input type="email"
                                               class="form-control"
                                               placeholder="Input account's password ...">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="email"
                                               class="form-control"
                                               placeholder="Retype inputted password ...">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Gender</label>
                                        <input type="email"
                                               class="form-control"
                                               placeholder="Retype inputted password ...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection
{{--    @push('addon-script')--}}
{{--        <script>--}}
{{--            // AJAX DataTable--}}
{{--            var datatable = $('#table').DataTable({--}}
{{--                processing: true,--}}
{{--                serverSide: true,--}}
{{--                ordering: true,--}}
{{--                ajax: {--}}
{{--                    url: '{!! url()->current() !!}',--}}
{{--                },--}}
{{--                columns: [--}}
{{--                    { data: 'name', name: 'name' },--}}
{{--                    { data: 'user.email', name: 'user.email'},--}}
{{--                    { data: 'user.phoneNumber', name: 'user.phoneNumber' },--}}
{{--                    { data: 'user.dob', name: 'user.dob' },--}}
{{--                    { data: 'age', name: 'age' },--}}
{{--                    { data: 'user.gender', name: 'user.gender' },--}}
{{--                    { data: 'user.address', name: 'user.address' },--}}
{{--                    { data: 'status', name: 'status' },--}}
{{--                    {--}}
{{--                        data: 'action',--}}
{{--                        name: 'action',--}}
{{--                        orderable: false,--}}
{{--                        searchable: false,--}}
{{--                        width: '15%'--}}
{{--                    },--}}
{{--                ]--}}
{{--            });--}}
{{--        </script>--}}
{{--    @endpush--}}
