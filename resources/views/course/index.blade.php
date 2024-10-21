@extends('layout/layout')

@section('page_title', 'Dashboard')

@section('style')
    <style>
        .dashboard-menu-button {
            text-decoration: none;
        }

        .dashboard-menu-button i {
            font-size: 40px;
        }

        .dashboard-menu-button {
            font-size: 25px;
        }

        @media(max-width: 768px) {
            .dashboard-menu-button i {
                font-size: 25px;
            }

            .dashboard-menu-button {
                font-size: 18px;
            }
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col text-muted">
            <ul class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    Dashboard
                </li>
                <li class="breadcrumb-item">
                    Course Management
                </li>
            </ul>

        </div>
    </div>

    <div class="row align-items-center g-2 mt-1">
        <div class="col">
            <h4 class="mb-0">Manage Course</h4>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex float-end gap-2">
                <button type="button" class="btn btn-success text-white rounded-4" data-bs-toggle="modal"
                    data-bs-target="#add-course-modal">
                    <i class="fa fa-plus opacity-50"></i>
                    Add
                </button>
            </div>
        </div>
    </div>

    <div>
        @livewire('course-list')
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
                        <div class="w-100">
                            <div class="form-group" id="add-course-modal-content">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="Enter name" required>
                            </div>

                        </div>


                        <div class="text-end">
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
