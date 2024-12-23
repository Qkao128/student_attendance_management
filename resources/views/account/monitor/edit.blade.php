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
                        Monitor Details
                    </li>
                    <li class="breadcrumb-item">
                        Edit Monitor Account
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

        <div class="container mt-4" class="edit-user-form" id="edit-user-section-{{ $monitor->student_id }}">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title fw-bold mt-0 ps-2">Edit Monitor Account</h5>
                        <hr class="my-3">
                        <form id="form"
                            action="{{ route('user.monitor.update', ['teacherId' => $monitor->teacher_user_id, 'id' => $monitor->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="row" style="margin-bottom: 100px;">
                                <div class="col-12 col-md-4">
                                    <div class="card border-0">
                                        <h5 class="mb-3 px-3 py-2">Profile Image</h5>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center">
                                                <div class="circle-img-lg-wrap rounded-circle border">
                                                    <img src="{{ $monitor->profile_image ? asset('storage/profile_image/' . $monitor->profile_image) : asset('img/default-student-avatar.png') }}"
                                                        id="profile-image-display"
                                                        onerror="this.onerror=null;this.src='{{ asset('img/default-teacher-avatar.png') }}'"
                                                        data-initial-image="{{ $monitor->profile_image ? asset('storage/profile_image/' . $monitor->profile_image) : asset('img/default-student-avatar.png') }}">
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
                                                    class="btn btn-danger {{ $monitor->profile_image ? '' : 'd-none' }}">
                                                    Reset
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-8 mt-3 mt-md-0">
                                    <div class="card border-0">
                                        <h5 class="mb-3 pe-3 py-0 mt-2 mb-0">Basic Information</h5>

                                        <div class="row">
                                            <div class="form-group mb-3 col-12">
                                                <label class="form-label" for="username">Username<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username" name="username"
                                                    value="{{ $monitor->username }}" placeholder="Enter username" required>
                                            </div>


                                            <div class="form-group mb-3 col-12 col-md-6">
                                                <label class="form-label" for="email">Email<span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ $monitor->monitor_email }}" placeholder="Enter email"
                                                    required>
                                            </div>

                                            <div class="form-group mb-3 col-12 col-md-6">
                                                <label class="form-label" for="course_id">Course<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="course_id" required style="width:100%;">
                                                    <option value="{{ $monitor->course_id }}">
                                                        {{ $monitor->course_name }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3 col-12 col-md-6">
                                                <label class="form-label" for="class_id">Class<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="class_id" required style="width:100%;">
                                                    <option value="{{ $monitor->class_id }}">
                                                        {{ $monitor->class_name }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3 col-12 col-md-6">
                                                <label class="form-label" for="student_id">Student<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="student_id" name="student_id" required
                                                    style="width:100%;">
                                                    <option value="{{ $monitor->student_id }}">
                                                        {{ $monitor->monitor_name }}
                                                    </option>
                                                </select>
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

            $("#course_id").select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select course',
                ajax: {
                    url: "{{ route('course.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        var query = {
                            search_term: params.term,
                            page: params.page,
                            _token: "{{ csrf_token() }}"
                        }
                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },

                }
            }).on('change', function() {
                $('#class_id').val(null).trigger('change');
                $('#student_id').val(null).trigger('change');
            });;

            $('#class_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select class',
                ajax: {
                    url: "{{ route('class.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search_term: params.term,
                            page: params.page,
                            course_id: $('#course_id').val() ?? false
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                }
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    }
                }
            }).on('change', function() {
                $('#student_id').val(null).trigger('change');
            });;

            $('#student_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select student',
                ajax: {
                    url: "{{ route('student.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search_term: params.term,
                            page: params.page,
                            class_id: $('#class_id').val() ?? false
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                }
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    }
                }
            });

        });

        var initialImage = $("#profile-image-display").data("initial-image");
        var defaultImage = '{{ asset('img/default-student-avatar.png') }}';

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
