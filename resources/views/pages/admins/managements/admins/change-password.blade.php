@extends('layouts.master')
@section('title')
    Change {{ $fullName }} Account's Password
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
                <form action="{{ route('admin-managements.change-password', ['admin' => $user->id]) }}" method="POST" enctype="multipart/form-data">
                    @method('PATCH')
                    @csrf
                    <div class="list-group-item d-flex justify-content-end">
                        <a class="btn btn-secondary mx-2" href="{{ route('admin-managements.index') }}"><span class="material-icons mr-2">close</span> Cancel</a>
                        <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">save</span> Save</button>
                    </div>
                    <div class="list-group-item">
                        <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="password">New Password</label>
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               required
                                               placeholder="Input account's new password ...">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="password-confirm">Confirm Password</label>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection

