@extends('layouts.master-without-nav')
@section('title')
    Forgot Password
@endsection
@section('page-title')
    Forgot Password
@endsection

@section('content')
    <div class="layout-login-centered-boxed__form card">
        <div class="d-flex flex-column justify-content-center align-items-center mt-2 mb-5 text-center">
            <a href=""
               class="navbar-brand flex-column mb-2 align-items-center mr-0"
               style="min-width: 0">
                @if(academyData()->logo)
                    <img src="{{ Storage::url(academyData()->logo) }}" alt="" height="75">
                @else
                    LOGO
                @endif
            </a>
            <strong class="h4">{{ academyData()->academyName }}</strong>
            <p class="m-0">Reset Your Account's Password</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label class="text-label" for="email_2">Email Address:</label>
                <div class="input-group input-group-merge">
                    <input id="email_2" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control form-control-prepended @error('email') is-invalid @enderror" placeholder="Input your email">
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
                <button class="btn btn-block btn-primary" type="submit">Send Password Reset Link</button>
            </div>
        </form>
    </div>
@endsection
