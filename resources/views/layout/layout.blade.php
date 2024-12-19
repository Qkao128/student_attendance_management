<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page_title') | Student Attendance Management</title>

    {{-- <link rel="apple-touch-icon" sizes="128x128" href="{{ asset('img/app-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/app-icon.png') }}"> --}}

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

    @yield('style')
    @livewireStyles
</head>

<body id="admin-portal">
    @include('layout/side-bar')

    <div id="overlay" class="d-none" onclick="toggleMobileNavigation()"></div>

    <div class="main">
        <div class="container-fluid px-1 px-md-2">
            @include('layout/top-bar')
            <div class="d-lg-none d-block">
                <h3 class="fw-bold mt-1" style="margin-top: -10px;">Student Attendance Management</h2>
            </div>

            @yield('content')
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

    @yield('script')

    @livewireScripts

    <script>
        $('.sidebar-item').each(function() {
            const $item = $(this);
            const $img = $item.find('#dashboard-icon');

            if ($img.length) {
                $item.hover(
                    function() {
                        $img.attr('src', $img.data('hover'));
                    },
                    function() {
                        if (!$item.hasClass('active')) {
                            $img.attr('src', $img.data('original'));
                        }
                    }
                );

                if ($item.hasClass('active')) {
                    $img.attr('src', $img.data('hover'));
                }
            }
        });

        @if (Session::has('success'))
            notifier.show('Success!', '{!! Session::get('success') !!}', 'success',
                '', 4000);
        @elseif (session('error'))
            notifier.show('Error!', '{!! Session::get('error') !!}', 'danger',
                '', 4000);
        @endif

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


        function toggleMobileNavigation() {
            const sidebar = $("#sidebar");
            const overlay = $("#overlay");

            if (sidebar.hasClass("show")) {
                sidebar.removeClass("show");
                overlay.addClass("d-none");
                $("body").css("overflow-y", "auto");
            } else {
                sidebar.addClass("show");
                overlay.removeClass("d-none");
                $("body").css("overflow-y", "hidden");
            }
        }
    </script>
</body>

</html>
