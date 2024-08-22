<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>@yield('title') | Football LMS</title>
        <!-- Prevent the demo from appearing in search engines -->
        <meta name="robots" content="noindex">
        @include('includes.style')
    </head>

    <body class="layout-default layout-login-centered-boxed">
        @yield('content')

        @include('includes.scripts')
    </body>
</html>
