@extends('layout/layout')

@section('page_title', 'Account')

@section('style')
    <style>
        .form-control {
            background-color: #FAFAFA !important;
        }
    </style>
@endsection

@section('content')
    <div id="admin-course">
        <div class="row mt-2">
            <div class="col text-muted">
                <ul class="breadcrumb mb-2 mb-md-1">
                    <li class="breadcrumb-item">
                        Dashboard
                    </li>
                    <li class="breadcrumb-item">
                        Manage Account
                    </li>
                    <li class="breadcrumb-item">
                        Edit Account
                    </li>
                </ul>

            </div>
        </div>


        <div class="text-end">
            <a href="{{ route('user.show', ['id' => $user->id]) }}" class="btn btn-dark rounded-4 text-white">
                <i class="fa-solid fa-angle-left text-white"></i>
                Back
            </a>
        </div>

        <div class="container mt-4" class="edit-user-form" id="edit-user-section-{{ $user->id }}">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title fw-bold mt-0 ps-2">Edit Account</h5>
                        <hr class="my-3">

                        <form id="form" action="{{ route('user.update', ['id' => $user->id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="card border-0">
                                        <h5 class="mb-3 px-3 py-2">Profile Image</h5>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center">
                                                <div class="circle-img-lg-wrap rounded-circle border">
                                                    <img src="{{ $user['profile_image'] ? asset('storage/profile_image/' . $user['profile_image']) : asset('img/default-teacher-avatar.png') }}"
                                                        id="profile-image-display"
                                                        onerror="this.onerror=null;this.src='{{ asset('img/default-teacher-avatar.png') }}'"
                                                        data-initial-image="{{ $user['profile_image'] ? asset('storage/profile_image/' . $user['profile_image']) : asset('img/default-teacher-avatar.png') }}">
                                                </div>
                                            </div>

                                            <input type="file" id="profile-image" name="profile_image"
                                                accept=".png,.jpeg,.jpg" hidden>


                                            <div class="text-center mt-4">
                                                <button type="button" onclick="uploadProfileImage()"
                                                    class="btn btn-primary">
                                                    Upload
                                                </button>


                                                <button type="button" onclick="removeProfileImage()"
                                                    id="remove-profile-image-btn"
                                                    class="btn btn-danger {{ $user->profile_image ? '' : 'd-none' }}">
                                                    Reset
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-8 mt-3 mt-md-0">
                                    <div class="card border-0">
                                        <h5 class="mb-3 px-3 py-2">Basic Information</h5>
                                        <div class="card-body">
                                            <div class="form-group mb-4">
                                                <label class="form-label" for="username">Username<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username" name="username"
                                                    value="{{ $user->username }}" placeholder="Enter username" required>
                                            </div>


                                            <div class="form-group mb-4">
                                                <label class="form-label" for="email">Email<span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ $user->email }}" placeholder="Enter email" required>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="text-end pe-2 mt-3">
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





        });

        var initialImage = $("#profile-image-display").data("initial-image");
        var defaultImage = '{{ asset('img/default-teacher-avatar.png') }}';

        $("#profile-image").change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $("#profile-image-display").attr("src", event.target.result);
                    $("#remove-profile-image-btn").removeClass("d-none");
                };
                reader.readAsDataURL(file);
            } else {
                var initialImage = $("#profile-image-display").data("initial-image");
                $("#profile-image-display").attr("src", initialImage);

                if (!initialImage || initialImage === defaultImage) {
                    $("#remove-profile-image-btn").addClass("d-none");
                }
            }
        });


        function uploadProfileImage() {
            $("#profile-image").click();
        }

        function removeProfileImage() {
            $("#profile-image").val(null);

            $("#profile-image-display").attr("src", defaultImage);

            $("#remove-profile-image-btn").addClass("d-none");

            if (!$("#profile-image-reset").length) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'profile-image-reset',
                    name: 'profile_image',
                    value: ''
                }).appendTo('#form');
            }
        }
    </script>

@endsection
