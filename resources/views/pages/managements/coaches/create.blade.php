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
            <h2 class="mb-0">@yield('title')</h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('coach-managements.index') }}">Coach Management</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
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
                            <x-forms.image-input name="foto" label="Profile photo"/>
                            <x-forms.basic-input type="text" name="firstName" label="First name" placeholder="Input coach's first name ..."/>
                            <x-forms.basic-input type="text" name="lastName" label="Last name" placeholder="Input coach's last name ..."/>
                            <x-forms.basic-input type="date" name="dob" label="Date of Birth" placeholder="Input coach's date of birth ..."/>
                            <x-forms.select name="gender" label="Gender" :select2="true">
                                <option disabled selected>Select coach's gender</option>
                                @foreach(['male' => 'Male', 'female' => 'Female', 'others' => 'Others'] AS $jenisKelamin => $jenisKelaminLabel)
                                    <option value="{{ $jenisKelamin }}" @selected(old('gender') == $jenisKelamin)>{{ $jenisKelaminLabel }}</option>
                                @endforeach
                            </x-forms.select>
                            <x-forms.basic-input type="date" name="hireDate" label="Hire Date" placeholder="Input coach's hire date ..."/>
                        </div>
                        <div class="col-lg-6">
                            @if(count($teams) < 1)
                                <x-warning-alert text="Currently you haven't create any team in your academy" :createRoute="route('team-managements.create')"/>
                            @else
                                <x-forms.select name="team[]" label="Teams Managed" :select2="true" :multiple="true">
                                    <option disabled>Select Managed Teams</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" @selected(old('team') == $team->id) data-avatar-src="{{ Storage::url($team->logo) }}">
                                            {{ $team->teamName }}
                                        </option>
                                    @endforeach
                                </x-forms.select>
                            @endif

                            <x-forms.basic-input type="email" name="email" label="Email address" placeholder="Input coach's account email address ..."/>
                            <x-forms.basic-input type="password" name="password" label="Password" placeholder="Input coach's account password ..."/>
                            @include('components.texts.password-rule')
                            <x-forms.basic-input type="password" name="password_confirmation" label="Confirm Password" placeholder="Retype inputted account password ..."/>
                        </div>
                    </div>

                    <div class="page-separator mt-3">
                        <div class="page-separator__text">Contact Information</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <x-forms.textarea name="address" label="address" placeholder="Input coach's address here" row="3"/>
                            <x-forms.basic-input type="text" name="phoneNumber" label="Phone Number" placeholder="Input coach's Phone Number ..."/>
                            <x-forms.basic-input type="number" name="zipCode" label="First name" placeholder="Input coach's address zip code ..."/>
                        </div>
                        <div class="col-lg-6">
                            <x-forms.select name="country_id" label="Country" :select2="true">
                                <option selected disabled>Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country['id'] }}">{{ $country['name'] }}</option>
                                @endforeach
                            </x-forms.select>

                            <x-forms.select name="state_id" label="State" :select2="true"></x-forms.select>
                            <x-forms.select name="city_id" label="City" :select2="true"></x-forms.select>
                        </div>
                    </div>

                    <div class="page-separator mt-3">
                        <div class="page-separator__text">Coaching Information</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <x-forms.select name="certificationId" label="Certification Level" :select2="true">
                                <option disabled selected>Select coach's certification level</option>
                                @foreach($certifications AS $certification)
                                    <option value="{{ $certification->id }}" @selected(old('certificationId') == $certification->id)>{{ $certification->name }}</option>
                                @endforeach
                            </x-forms.select>
                        </div>
                        <div class="col-lg-6">
                            <x-forms.select name="specializationId" label="Specialization" :select2="true">
                                <option disabled selected>Select coach's specialization</option>
                                @foreach($specializations AS $specialization)
                                    <option value="{{ $specialization->id }}" @selected(old('specializationId') == $specialization->id)>{{ $specialization->name }}</option>
                                @endforeach
                            </x-forms.select>
                        </div>
                    </div>

                    <div class="page-separator mt-3">
                        <div class="page-separator__text">Physic Information</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <x-forms.input-with-prepend-append name="height" label="height" placeholder="Input coach's height ..." text="Cm"/>
                        </div>
                        <div class="col-lg-6">
                            <x-forms.input-with-prepend-append name="weight" label="Weight" placeholder="Input coach's weight ..." text="Kg"/>
                        </div>
                    </div>
                    <div class="page-separator"></div>
                    <div class="d-flex justify-content-end">
                        <x-buttons.link-button color="secondary" margin="mr-2" :href="route('coach-managements.index')" icon="close" text="Cancel"/>
                        <x-buttons.basic-button icon="add" text="Submit" color="primary" type="submit"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#country_id').on('change', function () {
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
