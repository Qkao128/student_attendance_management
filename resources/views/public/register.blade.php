@extends('public.layout.layout')

@section('page_title', 'Register')

@section('og:title', 'Register | DooDooHanZi')
@section('description', '')
@section('og:description', '')

@section('content')

    <div class="row p-2">
        <div class="col-sm-2 col-md-3"></div>

        <div class="col card border-5">
            <div class="card-header bg-light text-center mb-4 mt-4">
                <h1 class="fw-bold" style="text-shadow: 1px 5px rgb(221, 221, 221);">Register</h1>
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

                <form id="form" method="POST" action="{{ route('register.request') }}" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <div>
                            <h5 class="mb-0">Profile Image</h5>
                        </div>

                        <div id="profile_image" class="my-2">
                            <div class="d-flex justify-content-center">
                                <div class="circle-img-lg-wrap rounded-circle border">
                                    <img src="{{ asset('img/default.png') }}" id="profile-image-display"
                                        onerror="this.onerror=null;this.src='{{ asset('img/default.png') }}'"
                                        data-initial-image="{{ asset('img/default.png') }}">
                                </div>
                            </div>

                            <input type="file" id="profile-image" name="profile_image" accept=".png,.jpeg,.jpg" hidden>

                            <div class="text-center mt-4">
                                <button type="button" onclick="uploadProfileImage()" class="btn btn-primary"
                                    style="box-shadow: 1px 5px rgb(221, 221, 221);">
                                    Upload
                                </button>

                                <button type="button" onclick="removeProfileImage()" id="remove-profile-image-btn"
                                    class="btn btn-danger d-none" style="box-shadow: 1px 5px rgb(221, 221, 221);">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>

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

                    <div class="form-group mb-3">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" class="form-control"
                            placeholder="password confirmation" required name="password_confirmation"
                            value="{{ old('password_confirmation') }}">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-4 fw-bold rounded-3 fs-4"
                        style="box-shadow: 1px 5px rgb(221, 221, 221);">Register</button>

                </form>
            </div>
        </div>

        <div class="col-sm-2 col-md-3"></div>
    </div>

    <div class="text-center mt-3">
        Already have an account? <br class="d-md-none">
        <a href="{{ route('login.index') }}" class="fw-bold text-dark">Login</a>
    </div>


@endsection

@section('script')
    <script>
        $(function() {
            $('#form').validate({
                rules: {
                    'password': {
                        required: true,
                        minlength: 5
                    },
                    'password_confirmation': {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    'password_confirmation': {
                        equalTo: "Password not match."
                    }
                },
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

            $("#profile-image").change(function() {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        $("#profile-image-display")
                            .attr("src", event.target.result);
                        $("#remove-profile-image-btn").removeClass("d-none");
                    };
                    reader.readAsDataURL(file);
                } else {
                    var initialImage = $("#profile-image-display").data("initial-image");

                    $("#profile-image-display")
                        .attr("src", initialImage);
                    $("#remove-profile-image-btn").addClass("d-none");
                }
            });
        });

        function uploadProfileImage() {
            $("#profile-image").click();
        }

        function removeProfileImage() {
            $("#profile-image").val(null);

            var initialImage = $("#profile-image-display").data("initial-image");

            $("#profile-image-display")
                .attr("src", initialImage);
            $("#remove-profile-image-btn").addClass("d-none");
        }
    </script>
@endsection
