@extends('layouts.master-without-nav')
@section('title')
    Reset Password
@endsection
@section('page-title')
    Reset Password
@endsection
@section('content')
    <div class="layout-login-centered-boxed__form card">
        <div class="d-flex flex-column justify-content-center align-items-center mt-2 mb-5 text-center">
            <a href=""
               class="navbar-brand flex-column mb-2 align-items-center mr-0"
               style="min-width: 0">
                @if(academyData()!= null)
                    @if(academyData()->logo != null)
                        <img src="{{ Storage::url(academyData()->logo) }}" alt="" height="75">
                    @else
                        LOGO
                    @endif
                    <strong class="h4">{{ academyData()->academyName }}</strong>
                @else
                    LOGO
                @endif
            </a>
            <p class="m-0">Reset Your Account's Password</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label class="text-label" for="email">Email Address:</label>
                <div class="input-group input-group-merge">
                    <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus autocomplete="email" class="form-control form-control-prepended @error('email') is-invalid @enderror" placeholder="Input your email">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="far fa-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="text-label" for="password">New Password:</label>
                <div class="input-group input-group-merge">
                    <input id="password" type="password" name="password" required autofocus autocomplete="new-password" class="form-control form-control-prepended @error('password') is-invalid @enderror" placeholder="Input your new password">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-key"></span>
                        </div>
                    </div>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    @include('components.texts.password-rule')
                </div>
            </div>
            <div class="form-group">
                <label class="text-label" for="password-confirm">Confirm Password:</label>
                <div class="input-group input-group-merge">
                    <input id="password-confirm" type="password" name="password_confirmation" required autofocus autocomplete="new-password" class="form-control form-control-prepended @error('password') is-invalid @enderror" placeholder="Retype your new password">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-key"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-block btn-primary" type="submit">Reset Password</button>
            </div>
        </form>
    </div>
@endsection
