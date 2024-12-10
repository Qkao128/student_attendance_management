@extends('layout/layout')

@section('page_title', 'Account Details')

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
        <div class="col">
            <h4 class="header-title">Account Details</h4>
        </div>

        <div class="col-12 col-md-auto mt-0 mt-md-1">
            <div class="d-flex float-end align-items-center gap-2">
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
                                <div class="text-muted">Name</div>
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
                                        placeholder="Enter new password" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label" for="password_confirmation">Confirm New Password:<span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Confirm new password" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>

                </div>
            </form>
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
        });
    </script>
@endsection
