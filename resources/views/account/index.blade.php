@extends('layout/layout')

@section('page_title', 'Account')

@section('content')
    <div id="admin-account">
        <div class="row mt-2">
            <div class="col text-muted">
                <ul class="breadcrumb mb-2 mb-md-1">
                    <li class="breadcrumb-item">
                        Dashboard
                    </li>
                    <li class="breadcrumb-item">
                        Manage Account
                    </li>
                </ul>

            </div>
        </div>

        <div class="row align-items-center g-2">
            <div class="col">
                <h4 class="header-title">Manage Account</h4>
            </div>
            <div class="col-12 col-md-auto mt-0 mt-md-1">
                <div class="d-flex float-end align-items-center">
                    <button type="button" class="btn btn-success text-white rounded-4" data-bs-toggle="modal"
                        data-bs-target="#add-account-modal">
                        Add
                    </button>
                </div>
            </div>
        </div>

        <div>
            @livewire('account-list')
        </div>
    </div>


    <div class="modal fade" id="add-account-modal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Add New Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.store') }}" id="form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-5 col-lg-4">
                                <div class="card border-0" style="background-color: transparent">
                                    <h5 class="mb-0 mt-2">Profile Image</h5>

                                    <div class="card-body mt-3">
                                        <div class="d-flex justify-content-center">
                                            <div class="circle-img-lg-wrap rounded-circle border">
                                                <img src="{{ asset('img/default-avatar.png') }}" id="profile-image-display"
                                                    onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.png') }}'"
                                                    data-initial-image="{{ asset('img/default-avatar.png') }}">
                                            </div>
                                        </div>

                                        <input type="file" id="profile-image" name="profile_image"
                                            accept=".png,.jpeg,.jpg" hidden>

                                        <div class="text-center mt-4">
                                            <button type="button" onclick="uploadProfileImage()" class="btn btn-primary">
                                                Upload
                                            </button>

                                            <button type="button" onclick="removeProfileImage()"
                                                id="remove-profile-image-btn" class="btn btn-danger d-none">
                                                Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-7 col-lg-8 mt-3 mt-md-0">
                                <div class="card border-0" style="background-color: transparent">
                                    <h5 class="mb-0 mt-2">Basic Information</h5>

                                    <div class="card-body">
                                        <div class="form-group mb-4">
                                            <label class="form-label" for="username">Name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                value="{{ old('username') }}" placeholder="Enter username" required>
                                        </div>


                                        <div class="form-group mb-4">
                                            <label class="form-label" for="email">Email<span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ old('email') }}" placeholder="Enter email" required>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="form-label" for="password">Password<span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Enter password" required>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="form-label" for="password_confirmation">Confirm Password<span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation" placeholder="Enter confirm password" required>
                                        </div>

                                    </div>
                                </div>
                            </div>



                            <div class="text-end pe-2 mt-5">
                                <button type="submit" class="btn btn-success text-white rounded-4">Submit</button>
                            </div>
                    </form>


                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function() {
            $('#form').validate({
                rules: {
                    'password': {
                        minlength: 8
                    },
                    'password_confirmation': {
                        equalTo: "#password"
                    },
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
                },
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        notifier.show('Warning!', 'Please check all the fields.', 'warning',
                            '', 4000);
                    }
                },
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
