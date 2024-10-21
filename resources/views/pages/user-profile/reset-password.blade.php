@extends('layouts.master')
@section('title')
    Reset Account's Password
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
                <li class="breadcrumb-item">
                    <a href="{{ checkRoleDashboardRoute() }}">Home</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('reset-password.update') }}" method="POST">
                    @method('PUT')
                    @csrf
                    <div class="page-separator">
                        <div class="page-separator__text">Account Profile</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label" for="old_password">Current Password</label>
                                <small class="text-danger">*</small>
                                <input type="text"
                                       class="form-control @error('old_password') is-invalid @enderror"
                                       id="old_password"
                                       name="old_password"
                                       required
                                       placeholder="Input your current password ...">
                                @error('old_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label" for="password">New Password</label>
                                <small class="text-danger">*</small>
                                <input type="text"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       required
                                       placeholder="Input account's last name ...">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label" for="confirm_password">Confirm Password</label>
                                <small class="text-danger">*</small>
                                <input type="text"
                                       class="form-control @error('confirm_password') is-invalid @enderror"
                                       id="confirm_password"
                                       name="confirm_password"
                                       required
                                       placeholder="Retype your new password ...">
                                @error('confirm_password')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
