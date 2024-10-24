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
                        Course Management
                    </li>
                </ul>

            </div>
        </div>

        <div class="row align-items-center g-2">
            <div class="col">
                <h4 class="mb-0">Manage Course</h4>
            </div>
            <div class="col-12 col-md-auto mt-0 mt-md-1">
                <div class="d-flex float-end align-items-center">
                    <button type="button" class="btn btn-success text-white py-2 px-3" style="border-radius: 10px;"
                        data-bs-toggle="modal" data-bs-target="#add-course-modal">
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
                    <form action="{{ route('course.store') }}" id="form" method="POST">
                        @csrf
                        <div class="w-100 p-2">
                            <div class="form-group" id="add-course-modal-content">
                                <label class="form-label" for="course">Name</label>
                                <input type="text" class="form-control" id="course" name="course"
                                    value="{{ old('course') }}" placeholder="Enter name" required>
                            </div>

                        </div>


                        <div class="text-end pe-2">
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
