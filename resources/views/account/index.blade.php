@extends('layout/layout')

@section('page_title', 'Dashboard')

@section('content')
    <div id="admin-course">
        <div class="row mt-2">
            <div class="col text-muted">
                <ul class="breadcrumb mb-2 mb-md-1">
                    <li class="breadcrumb-item">
                        Dashboard
                    </li>
                    <li class="breadcrumb-item">
                        Manage Course
                    </li>
                </ul>

            </div>
        </div>

        <div class="row align-items-center g-2">
            <div class="col">
                <h4 class="header-title">Manage Course</h4>
            </div>
            <div class="col-12 col-md-auto mt-0 mt-md-1">
                <div class="d-flex float-end align-items-center">
                    <button type="button" class="btn btn-success text-white rounded-4" data-bs-toggle="modal"
                        data-bs-target="#add-course-modal">
                        Add
                    </button>
                </div>
            </div>
        </div>

        <div>
            @livewire('course-list')
        </div>
    </div>


    <div class="modal fade" id="add-course-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Add New Course
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.store') }}" id="form" method="POST">
                        @csrf
                        <div class="row w-100 p-2 g-3">
                            <div class="form-group col-12">
                                <label class="form-label" for="username">Username<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="{{ old('username') }}" placeholder="Enter username" required>
                            </div>

                            <div class="form-group col-12">
                                <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter password" required>
                            </div>

                            <div class="form-group col-12">
                                <label class="form-label" for="password_confirmation">Confirm Password<span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Enter confirm password" required>
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


        });
    </script>
@endsection
