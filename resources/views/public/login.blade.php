@extends('public.layout.layout')

@section('page_title', 'Login')

@section('og:title', 'Login | Student Attendance Management')
@section('description', '')
@section('og:description', '')


@section('style')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        #auth-layout {
            background-size: cover;
            background-repeat: no-repeat;
            background-position: bottom center;
            background-attachment: fixed;
            height: 100vh;
        }

        #Login-Bg {
            position: relative;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: bottom center;
            background-attachment: fixed;
            min-height: 100vh;
            height: 100vh;
            width: 100vw;
        }

        .big-con,
        .card-header,
        .card-con {
            min-height: 69vh;
            height: 69vh;
        }

        .bar {
            height: 30px;
        }

        .bar2 {
            height: 0px;
        }

        .bar3 {
            height: 25px;
        }


        @media (max-width: 768px) {
            .big-con {
                margin: 0 !important;
                padding: 0 !important;
                width: 100vw;
                height: 100vh;
                overflow: hidden;

                @supports (-webkit-touch-callout: none) {
                    height: -webkit-fill-available;
                }
            }

            .con {
                border-radius: 0 !important;
            }

            .card-header {
                min-width: none;
                min-height: 0px;
                height: auto;
                padding: 25px;
                margin: 0 !important;
                border-top-left-radius: 0 !important;
                border-top-right-radius: 0 !important;
            }

            .card-header .auth-logo-container {
                width: 200px;
                z-index: 0;
                min-width: 200px;
            }

            .card-con {
                min-width: 100%;

            }

            .big-con .con {
                width: 100vw;
                height: 100vh;
                min-height: 100vh;
                overflow: hidden;

                @supports (-webkit-touch-callout: none) {
                    height: -webkit-fill-available; // Fix for mobile browsers
                    width: -webkit-fill-available; // Fix for mobile browsers
                }
            }

            .bar,
            .bar2,
            .bar3 {
                height: 0px;
            }

        }
    </style>
@endsection


@section('content')
    <div class="background-image-holder position-relative p-0 "
        style="background-image:url({{ asset('img/public/loginBg.png') }});" id="Login-Bg">
        <div class="row p-0 m-0">
            <div class="big-con p-0 m-0 position-absolute top-50 start-50 translate-middle">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-0 col-lg-2"></div>
                    <div class="con col-12 col-lg-8 card rounded-4 rounded-sm-none rounded-md-4">
                        <div class="card-con row">
                            <div
                                class="card-header bg-primary text-white d-flex flex-column justify-content-center align-items-center rounded-top-sm-none rounded-4 col-12 col-md-5 shadow">

                                <div class="auth-logo-container mb-3 position-md-absolute" style="max-width: 300px">
                                    <img class="w-100 mt-3 mb-3 d-md-block" src="{{ asset('img/public/LoginLogo.png') }}"
                                        id="auth-logo">
                                </div>
                                <div class="bar"></div>
                                <h2 class="fw-bold fs-4 fs-md-3 justify-content-center text-left text-white text-md-center">
                                    Student Attendance Management</h2>
                            </div>
                            <div class="card-body pt-50 mb-0 col-12 col-md-7 mt-1 mt-md-3">
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show my-3">
                                        <span>{{ Session::get('error') }}</span>
                                    </div>
                                @elseif (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show my-3">
                                        <span>{{ Session::get('success') }}</span>
                                    </div>
                                @endif

                                <div class="d-flex flex-column w-100">
                                    <form id="form" class="d-flex align-items-center flex-column w-100" method="POST"
                                        action="{{ route('login.request') }}">
                                        @csrf
                                        <div class="form-text">
                                            <h2 class="fw-bold d-flex justify-content-center">Login</h2>
                                        </div>
                                        <div class="bar3"></div>
                                        <div class="mt-sm-1 mt-3 w-100">
                                            <div class="form-group mb-4 mb-md-5">
                                                <label class="form-label mb-sm-3 mb-md-4 ms-2" for="name">Name</label>
                                                <input type="text" id="username" class="form-control"
                                                    placeholder="username" required name="username"
                                                    value="{{ old('username') }}">
                                            </div>

                                            <div class="form-group mb-4 mb-md-5">
                                                <label class="form-label mb-sm-3 mb-md-4 ms-2"
                                                    for="password">Password</label>
                                                <input type="password" id="password" class="form-control"
                                                    placeholder="password" required name="password"
                                                    value="{{ old('password') }}">
                                            </div>
                                            <div class="bar2"></div>
                                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-1 fw-bold"
                                                style="box-shadow: 1px 5px rgb(221, 221, 221);">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-0 col-lg-2"></div>
                </div>
            </div>


        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#form').validate({
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            })
        });
    </script>
@endsection
