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
                        Manage Course
                    </li>
                    <li class="breadcrumb-item">
                        Edit Course
                    </li>
                </ul>

            </div>
        </div>

        <div class="row align-items-center g-2">
            <div class="col">
                <h4 class="header-title">Edit Course</h4>
            </div>
            <div class="col-12 col-md-auto mt-0">
                <div class="d-flex float-end align-items-center">
                    <a href="{{ route('course.index') }}" class="btn btn-dark rounded-4 text-white">
                        <i class="fa-solid fa-angle-left text-white"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>


        <div class="container mt-4" id="edit-course-section-{{ $course->id }}">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title fw-bold mt-0 ps-2">Edit Course</h5>
                        <hr class="my-3">

                        <form action="{{ route('course.update', ['id' => $course->id]) }}" id="form" method="POST">
                            @method('PATCH')
                            @csrf
                            <div class="row w-100 p-2 g-3" id="edit-course-form-content">
                                <div class="form-group col-12">
                                    <label class="form-label" for="course">Name</label>
                                    <input type="text" class="form-control" id="course" name="course"
                                        value="{{ $course->course }}" placeholder="Enter name" required>
                                </div>
                            </div>

                            <div class="text-end pe-3 mt-5">
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
    </script>

@endsection
