<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') | Football LMS</title>
    <!-- Prevent the demo from appearing in search engines -->
    <meta name="robots" content="noindex">
    @stack('prepend-style')
    @include('includes.style')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/datatables.min.css" rel="stylesheet">
    @stack('addon-style')
</head>

<body class="layout-app ">
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
    <div class="mdk-drawer-layout__content page-content">
        @include('includes.header')

        @yield('content')

        @include('includes.footer')

    </div>
    @include('includes.admins.navigation')
</div>

@stack('prepend-script')
@include('includes.scripts')
@include('sweetalert::alert')
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/datatables.min.js"></script>
@stack('addon-script')
</body>
</html>
