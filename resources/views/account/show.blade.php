@php
    use App\Enums\UserType;
@endphp

@extends('layout/layout')

@section('page_title', 'Account Details')

@section('style')
    <style>
        .select2-container .select2-selection--single {
            background-color: #F4F6FA !important;
        }
    </style>
@endsection

@section('content')
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
                    Account Details
                </li>
            </ul>

        </div>
    </div>

    <div class="row align-items-center mb-3">
        <div class="col {{ Auth::user()->hasRole(UserType::Monitor()->key) ? 'my-2' : '' }}">
            <h4 class="header-title">Account Details</h4>
        </div>

        <div class="col-12 col-md-auto mt-0 mt-md-1">
            <div class="d-flex float-end align-items-center gap-2">
                @hasrole('Admin')
                    <!-- 如果是 Admin，這段代碼會被隱藏 -->
                @else
                    <a href="{{ route('user.index') }}" class="btn btn-dark rounded-4 text-white">
                        <i class="fa-solid fa-angle-left text-white"></i>
                        Back
                    </a>

                    <form method="post" action={{ route('user.destroy', ['id' => $user->id]) }}>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-4" onclick="deleteFormConfirmation(event)">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </form>

                    <a href="{{ route('user.edit', ['id' => $user->id]) }}" class="btn btn-warning text-dark rounded-4">
                        <i class="fa-solid fa-pen-nib"></i>
                        Edit
                    </a>
                @endhasrole
            </div>
        </div>
    </div>


    <div class="card mb-3">
        <div class="card-body py-0">
            <ul class="nav nav-tabs profile-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#basic-information-tab">
                        <i class="fa-solid fa-user me-2"></i>Basic Information
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#change-password-tab">
                        <i class="fa-solid fa-lock me-2"></i> Change Password
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane show active" id="basic-information-tab">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card border-0">
                        <div class="card-header">
                            <h5 class="mb-0 py-1">Profile Image</h5>
                        </div>
                        <div class="card-body py-4 card-shadow">
                            <div class="d-flex justify-content-center">
                                <div class="circle-img-lg-wrap rounded-circle border">
                                    <img src="{{ $user['profile_image'] ? asset('storage/profile_image/' . $user['profile_image']) : asset('img/default-teacher-avatar.png') }}"
                                        onerror="this.onerror=null;this.src='{{ asset('img/default-teacher-avatar.png') }}'">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-8 mt-3 mt-md-0">
                    <div class="card border-0">
                        <div class="card-header">
                            <h5 class="mb-0 py-1">Basic Information</h5>
                        </div>
                        <div class="card-body card-shadow py-4">
                            <div>
                                <div class="text-muted">Userame</div>
                                <div class="fw-bold mt-1">{{ $user->username }}</div>
                            </div>

                            <hr class="text-muted">

                            <div>
                                <div class="text-muted">Email</div>
                                <div class="fw-bold mt-1">{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="change-password-tab">
            <form id="change-password-form" action="{{ route('user.password.update', ['id' => $user->id]) }}"
                method="POST">
                @csrf
                @method('PATCH')

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="my-5 text-center">
                                    <i class="fa-solid fa-lock" style="font-size:150px;"></i>
                                </div>
                            </div>

                            <div class="col-12 col-md-8 pt-2">
                                <div class="form-group mb-4">
                                    <label class="form-label" for="password">New Password:<span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter new password" required style="background: #FAFAFA">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label" for="password_confirmation">Confirm New Password:<span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Confirm new password" required
                                        style="background: #FAFAFA">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>

                </div>
            </form>

            <input type="hidden" id="user_id" value="{{ $user->id }}">
        </div>
    </div>


    <div class="row align-items-center g-2 mt-4">
        <div class="col">
            <h4 class="header-title">Manage Account Monitor</h4>
        </div>
        <div class="col-12 col-md-auto mt-0 mt-md-1">
            <div class="d-flex float-end align-items-center">
                <button type="button" class="btn btn-success text-white rounded-4" data-bs-toggle="modal"
                    data-bs-target="#add-account-monitor-modal">
                    Add
                </button>
            </div>
        </div>
    </div>

    <div>
        @livewire('account-monitor-list', ['teacherId' => $user->id])
    </div>

    <div class="modal fade" id="add-account-monitor-modal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Add New Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="overflow-y: scroll; height: 620px;">
                    <form action="{{ route('user.monitor.store', ['teacherId' => $user->id]) }}" id="form"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-5 col-lg-4">
                                <div class="card border-0" style="background-color: transparent">
                                    <h5 class="mb-0 mt-2">Profile Image</h5>

                                    <div class="card-body mt-3">
                                        <div class="d-flex justify-content-center">
                                            <div class="circle-img-lg-wrap rounded-circle border">
                                                <img src="{{ asset('img/default-teacher-avatar.png') }}"
                                                    id="profile-image-display"
                                                    onerror="this.onerror=null;this.src='{{ asset('img/default-teacher-avatar.png') }}'"
                                                    data-initial-image="{{ asset('img/default-teacher-avatar.png') }}">
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
                                        <div class="row">

                                            <div class="form-group col-12 col-sm-6 mb-4">
                                                <label class="form-label" for="username">Username<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username"
                                                    name="username" value="{{ old('username') }}"
                                                    placeholder="Enter username" required>
                                            </div>


                                            <div class="form-group mb-4 col-12 col-sm-6">
                                                <label class="form-label" for="email">Email<span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ old('email') }}" placeholder="Enter email" required>
                                            </div>

                                            <div class="form-group mb-4 col-12 col-sm-6">
                                                <label class="form-label" for="course_id">Course<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="course_id" required style="width:100%;">
                                                </select>
                                            </div>

                                            <div class="form-group mb-4 col-12 col-sm-6">
                                                <label class="form-label" for="class_id">Class<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="class_id" required style="width:100%;">
                                                </select>
                                            </div>

                                            <div class="form-group mb-4 col-12 col-sm-6">
                                                <label class="form-label" for="student_id">Student<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="student_id" name="student_id" required
                                                    style="width:100%;">
                                                </select>
                                            </div>

                                            <div class="form-group mb-4 col-12 col-sm-6">
                                                <label class="form-label" for="email">Permission<span
                                                        class="text-danger">*</span></label>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="permission"
                                                        id="permission_super_admin"
                                                        value="{{ UserType::Monitor()->key }}" required
                                                        @if (old('permission') == UserType::Monitor()->key) checked @endif checked>

                                                    <label class="form-check-label" for="permission_super_admin">
                                                        Monitor
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12 mb-4">
                                            <label class="form-label" for="password">Password<span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Enter password" required>
                                        </div>

                                        <div class="form-group col-12 mb-4">
                                            <label class="form-label" for="password_confirmation">Confirm
                                                Password<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation" placeholder="Enter confirm password"
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end pe-2 mt-2 mt-md-5">
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
            $('#change-password-form').validate({
                rules: {
                    'password': {
                        required: true,
                        minlength: 8
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
                },
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        notifier.show('Warning!', 'Please check all the fields.', 'warning', '', 4000);
                    }
                },
            })



            $("#course_id").select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select course',
                dropdownParent: $('#add-account-monitor-modal'),
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
            });

            $('#class_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select class',
                dropdownParent: $('#add-account-monitor-modal'),
                ajax: {
                    url: "{{ route('class.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search_term: params.term,
                            page: params.page,
                            course_id: $('#course_id').val() ?? false,
                            teacher_id: $('#user_id').val() // 傳遞 teacher_id
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
            });

            $('#student_id').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select student',
                dropdownParent: $('#add-account-monitor-modal'),
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


        let isScrolling;
        $('#form').on('scroll', function() {
            clearTimeout(isScrolling);
            $('#course_id, #class_id, #student_id').select2('close'); // 滾動時關閉
            isScrolling = setTimeout(function() {
                $('#course_id, #class_id, #student_id').select2('open'); // 滾動結束後打開
            }, 200);
        });
    </script>
@endsection
