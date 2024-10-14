@extends('public.layout.layout')

@section('page_title', 'Login')

@section('og:title', 'Login | Task Planner')
@section('description', '')
@section('og:description', '')

@section('content')
    <div class="row p-2">
        <div class="col-sm-2 col-md-3"></div>
        <div class="col card border-5">
            <div class="card-header bg-light d-flex flex-column justify-content-center align-items-center mb-4 mt-4">
                <div class="auth-logo-container mb-3" style="max-width: 300px">
                    <img class="w-100" src="{{ asset('img/logo.png') }}" id="auth-logo">
                </div>
                <h1 class="fw-bold" style="text-shadow: 1px 5px rgb(221, 221, 221);">Login</h1>
            </div>
            <div class="card-body pt-0 mb-4">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-3">
                        <span>{{ Session::get('error') }}</span>
                    </div>
                @elseif (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3">
                        <span>{{ Session::get('success') }}</span>
                    </div>
                @endif


                <form id="form" method="POST" action="{{ route('login.request') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" id="name" class="form-control" placeholder="name" required name="name"
                            value="{{ old('name') }}">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" class="form-control" placeholder="password" required
                            name="password" value="{{ old('password') }}">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 fw-bold"
                        style="box-shadow: 1px 5px rgb(221, 221, 221);">Login</button>
                </form>
            </div>
        </div>

        <div class="col-sm-2 col-md-3"></div>
    </div>


    <div class="text-center mt-3">
        Don't have an account yet? <br class="d-md-none">
        <a href="{{ route('register.index') }}" class="fw-bold text-dark">
            Register Now
        </a>
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
