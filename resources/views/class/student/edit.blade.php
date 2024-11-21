@extends('layout/layout')

@section('page_title', 'Class')

@section('content')
    <div id="admin-course">
        <div class="row mt-2">
            <div class="col text-muted">
                <ul class="breadcrumb mb-2 mb-md-1">
                    <li class="breadcrumb-item">
                        Dashboard
                    </li>
                    <li class="breadcrumb-item">
                        Manage Class
                    </li>
                    <li class="breadcrumb-item">
                        Edit Student
                    </li>
                </ul>

            </div>
        </div>


        <div class="text-end">
            <a href="{{ route('class.show', ['id' => $class->id]) }}" class="btn btn-dark rounded-4 text-white">
                <i class="fa-solid fa-angle-left text-white"></i>
                Back
            </a>
        </div>

        <div class="container mt-4" class="edit-student-form" id="edit-student-section-{{ $student->id }}">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title fw-bold mt-0 ps-2">Edit Student</h5>
                        <hr class="my-3">

                        <form id="form"
                            action="{{ route('student.update', ['classId' => $class->id, 'id' => $student->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="card border-0">
                                        <h5 class="mb-3 px-3 py-2">Profile Image</h5>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center">
                                                <div class="circle-img-lg-wrap rounded-circle border">
                                                    <img src="{{ $student['profile_image'] ? asset('storage/profile_image/' . $student['profile_image']) : asset('img/default-avatar.png') }}"
                                                        id="profile-image-display"
                                                        onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.png') }}'"
                                                        data-initial-image="{{ $student['profile_image'] ? asset('storage/profile_image/' . $student['profile_image']) : asset('img/default-avatar.png') }}">
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
                                                    class="btn btn-danger {{ $student->profile_image ? '' : 'd-none' }}">
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
                                                <label class="form-label" for="name">Name<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="{{ $student['name'] }}" placeholder="Enter name" required>
                                            </div>

                                            <div class="form-group mb-4">
                                                <label class="form-label">Gender<span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="gender"
                                                                id="gender_male" value="male" required
                                                                @if (old('gender', $student['gender']) == 'male') checked @endif>
                                                            <label class="form-check-label" for="gender_male">
                                                                Male
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="gender"
                                                                id="gender_female" value="female" required
                                                                @if (old('gender', $student['gender']) == 'female') checked @endif>
                                                            <label class="form-check-label" for="gender_female">
                                                                Female
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end pe-3 mt-3 mt-sm-5">
                                                <button type="submit"
                                                    class="btn btn-success text-white rounded-4">Submit</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
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
        var defaultImage = '{{ asset('img/default-avatar.png') }}';

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
