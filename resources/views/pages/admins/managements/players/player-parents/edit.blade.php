@extends('includes.admins.master')
@section('title')
    Edit Player's Parent/Guardian ({{ $fullname }})
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
                        <li class="breadcrumb-item"><a href="{{ route('player-managements.index') }}">Players
                                Management</a></li>
                        <li class="breadcrumb-item"><a
                                    href="{{ route('player-managements.show', $player->id) }}">{{ $fullname }}</a></li>
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
            <form action="{{ route('player-parents.update', ['player'=>$player->id,'parent'=>$parent->id]) }}"
                  method="post">
                @method('PUT')
                @csrf
                <div class="list-group-item d-flex justify-content-end">
                    <a class="btn btn-secondary mx-2" href="{{ route('player-managements.show', $player->id) }}"><span
                                class="material-icons mr-2">close</span> Cancel</a>
                    <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span> Submit
                    </button>
                </div>
                <div class="list-group-item">
                    <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                        <div class="page-separator">
                            <div class="page-separator__text">Profile</div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="firstName">First name</label>
                                    <small class="text-danger">*</small>
                                    <input type="text"
                                           class="form-control @error('firstName') is-invalid @enderror"
                                           id="firstName"
                                           name="firstName"
                                           required
                                           value="{{ old('firstName', $parent->firstName) }}"
                                           placeholder="Input parent/guardian's first name ...">
                                    @error('firstName')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="email">Email</label>
                                    <small class="text-danger">*</small>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           required
                                           value="{{ old('email', $parent->email) }}"
                                           placeholder="Input parent/guardian's email ...">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="lastName">Last name</label>
                                    <small class="text-danger">*</small>
                                    <input type="text"
                                           class="form-control @error('lastName') is-invalid @enderror"
                                           id="lastName"
                                           name="lastName"
                                           required
                                           value="{{ old('lastName', $parent->lastName) }}"
                                           placeholder="Input parent/guardian's last name ...">
                                    @error('lastName')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="phoneNumber">Phone Number</label>
                                    <small class="text-danger">*</small>
                                    <input type="text"
                                           class="form-control @error('phoneNumber') is-invalid @enderror"
                                           id="phoneNumber"
                                           name="phoneNumber"
                                           required
                                           value="{{ old('phoneNumber', $parent->phoneNumber) }}"
                                           placeholder="Input parent/guardian's phone number ...">
                                    @error('phoneNumber')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="relations">Relation to Player</label>
                                    <small class="text-danger">*</small>
                                    <select class="form-control form-select @error('relations') is-invalid @enderror"
                                            id="relations" name="relations" required>
                                        <option disabled selected>Select relation to player</option>
                                        @foreach(['Father', 'Mother', 'Brother', 'Sister', 'Others'] AS $relation)
                                            <option value="{{ $relation }}" @selected(old('relations', $parent->relations) == $relation)>{{ $relation }}</option>
                                        @endforeach
                                    </select>
                                    @error('relations')
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
