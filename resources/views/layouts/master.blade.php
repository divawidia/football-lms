<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') | Football LMS</title>
    <!-- Prevent the demo from appearing in search engines -->
    <meta name="robots" content="noindex">

    <!-- style -->
    @stack('prepend-style')
    @include('includes.style')
    @stack('addon-style')

    @vite(['resources/sass/app.scss', 'resources/js/app.js', ])
</head>

<body class="layout-app">
<div class="preloader">
    <div class="sk-chase">
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
    </div>
</div>

<div class="mdk-drawer-layout js-mdk-drawer-layout" data-push data-responsive-width="992px">
    @yield('modal')
    <div class="mdk-drawer-layout__content page-content">
        @include('includes.topbar')

        @yield('content')

        @include('includes.footer')

    </div>

    @if(Auth::user()->hasRole('admin'))
        @include('includes.admins.sidebar')
    @elseif(Auth::user()->hasRole('coach'))
        @include('includes.coaches.sidebar')
    @elseif(Auth::user()->hasRole('player'))
        @include('includes.players.sidebar')
    @endif
</div>

<!-- script -->
@stack('prepend-script')
@include('includes.scripts')
@include('sweetalert::alert')
@stack('addon-script')

</body>
</html>
