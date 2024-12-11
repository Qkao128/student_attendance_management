    @extends('public.layout.layout')

    @section('page_title', 'Login')
    
    @section('og:title', 'Login | Student Attendance Management')
    @section('description', '')
    @section('og:description', '')

    @section('content')
        <div class="background-image-holder position-relative p-0 " style="background-image:url({{ asset('img/loginBg.png') }});" id="Login-Bg">
            <div class="row p-0 m-0">
        <div class="big-con p-0 m-0 position-absolute top-50 start-50 translate-middle">
            <div class="row d-flex justify-content-center">
                <div class="col-sm-0 col-md-3"></div>
                <div class="con col-md-6 col-12 card rounded-4 rounded-sm-none rounded-md-4">
                    <div class="card-con row">
                    <div class="card-header bg-primary text-white d-flex flex-column justify-content-center align-items-center rounded-top-sm-none rounded-4 col-12 col-md-5 shadow">
                        <div class="auth-logo-container mb-3 position-md-absolute" style="max-width: 300px">
                            <img class="w-100 d-md-block" src="{{ asset('img/LoginLogo.png') }}" id="auth-logo">
                        </div>
                        <h1 class="fw-bold fs-4 fs-md-3 justify-content-center text-left text-md-center 
                        ">Student Attendance Management</h1>
                    </div>
                    <div class="card-body pt-50 mb-4 col-12 col-md-7">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mt-3">
                                <span>{{ Session::get('error') }}</span>
                            </div>
                        @elseif (session('success'))
                            <div class="alert alert-success alert-dismissible fade show mt-3">
                                <span>{{ Session::get('success') }}</span>
                            </div>
                        @endif

                        <div>
                        <form id="form" method="POST" action="{{ route('login.request') }}">
                            @csrf
                            <h1 class="fw-bold d-flex justify-content-center" style="">Login</h1>
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
                    </div>
                </div>

                <div class="col-sm-0 col-md-3"></div>
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
