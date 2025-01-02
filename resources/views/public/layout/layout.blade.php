<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('page_title') | Student Attendance Management</title>
    <meta name="description" content="@yield('description', '')" />

    <meta property="og:locale" content="@yield('og:locale', 'en_US')" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('og:title', 'Student Attendance Management')" />
    <meta property="og:description" content="@yield('og:description', '')" />
    <meta property="og:url" content="@yield('og:url', Request::url())" />
    <meta property="og:site_name" content="@yield('og:site_name', 'Student Attendance Management')" />
    <meta property="og:image" content="@yield('og:image', asset('img/social-preview.png'))" />
    <meta property="og:image:width" content="@yield('og:image:width', '1200')" />
    <meta property="og:image:height" content="@yield('og:image:height', '630')" />
    <meta property="og:image:type" content="@yield('og:image:type', 'image/png')" />

    <link rel="apple-touch-icon" sizes="128x128" href="{{ asset('img/app-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/app-icon.png') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

    @yield('style')
</head>

<body class="p-2" id="auth-layout" style="background-image: url('{{ asset('img/background.png') }}');">
    @yield('content')


    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

    @yield('script')

    <script>
        @if (Session::has('success_confirm'))
            Swal.fire({
                title: '{!! Session::get('success_confirm') !!}',
                icon: 'success',
                confirmButtonColor: '#002FA7',
            })
        @elseif (session('error_confirm'))
            Swal.fire({
                title: '{!! Session::get('error_confirm') !!}',
                icon: 'error',
                confirmButtonColor: '#002FA7',
            })
        @endif
    </script>

</body>

</html>
