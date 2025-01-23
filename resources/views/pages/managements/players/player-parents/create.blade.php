@extends('layouts.master')
@section('title')
    Create {{ getUserFullName($data->user) }}'s Parent/Guardian
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
                <li class="breadcrumb-item"><a href="{{ route('player-managements.index') }}">Players Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('player-managements.show', $data->hash) }}">{{ getUserFullName($data->user) }}</a></li>
                <li class="breadcrumb-item active">Create Parent/Guardian</li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('player-managements.player-parents.store', $data->hash) }}" method="post">
                    @csrf
                    <div class="page-separator">
                        <div class="page-separator__text">Profile</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <x-forms.basic-input
                                type="text"
                                name="firstName"
                                label="First Name"
                                placeholder="Input parent/guardian's first name ..."
                            />
                            <x-forms.basic-input
                                type="text"
                                name="lastName"
                                label="Last Name"
                                placeholder="Input parent/guardian's last name ..."
                            />
                        </div>
                        <div class="col-lg-6">
                            <x-forms.basic-input
                                type="email"
                                name="email"
                                label="Email"
                                placeholder="Input parent/guardian's email ..."
                            />
                            <x-forms.basic-input
                                type="text"
                                name="phoneNumber"
                                label="Phone Number"
                                placeholder="Input parent/guardian's phone number ..."
                            />
                        </div>
                    </div>
                    <x-forms.select name="relations" label="Relation to Player" :select2="true">
                        <option disabled selected>Select relation to player</option>
                        @foreach(['Father', 'Mother', 'Brother', 'Sister', 'Others'] AS $relation)
                            <option value="{{ $relation }}" @selected(old('relations') == $relation)>{{ $relation }}</option>
                        @endforeach
                    </x-forms.select>

                    <div class="page-separator"></div>
                    <div class="d-flex justify-content-end">
                        <x-buttons.link-button color="secondary" margin="mr-2" :href="route('player-managements.show', $data->hash)" icon="close" text="Cancel"/>
                        <x-buttons.basic-button icon="add" text="Submit" color="primary" type="submit"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
