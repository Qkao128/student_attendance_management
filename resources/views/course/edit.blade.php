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
                        Edit Class
                    </li>
                </ul>

            </div>
        </div>

        <div class="row align-items-center g-2">
            <div class="col">
                <h4 class="header-title">Edit Class</h4>
            </div>
            <div class="col-12 col-md-auto mt-0">
                <div class="d-flex float-end align-items-center">
                    <a href="{{ route('class.index') }}" class="btn btn-dark rounded-4 text-white">
                        <i class="fa-solid fa-angle-left text-white"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>


        <div class="container mt-4" id="edit-class-section-{{ $class->id }}">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title fw-bold mt-0 ps-2">Edit Class</h5>
                        <hr class="my-3">

                        <form action="{{ route('class.update', ['id' => $class->id]) }}" method="POST">
                            @method('PATCH')
                            @csrf
                            <div class="row w-100 p-2 g-3" id="edit-class-form-content">
                                <div class="form-group col-12 col-md-6">
                                    <label class="form-label" for="edit-course-id">Course</label>
                                    <select class="form-select" id="edit-course-id" name="course_id" required
                                        style="width:100%;">
                                        <option value="{{ $class->course_id }}">
                                            {{ $class->courseModal->course }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label class="form-label" for="edit-user-id">Teacher</label>
                                    <select class="form-select" id="edit-user-id" name="user_id" required
                                        style="width:100%;">
                                        <option value="{{ $class->user_id }}">
                                            {{ $class->userModal->name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label class="form-label" for="class">Name</label>
                                    <input type="text" class="form-control" id="class" name="class"
                                        value="{{ $class->class }}" placeholder="Enter name" required>
                                </div>
                            </div>

                            <div class="text-end pe-3 mt-2">
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

            $("#course_id").select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select course',
                dropdownParent: $('#add-class-modal'),
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
                                    text: item.course,
                                    id: item.id
                                }
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },

                }
            });


            $("#user_id").select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select user',
                dropdownParent: $('#add-class-modal'),
                ajax: {
                    url: "{{ route('user.select_search') }}",
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
            });


        });
    </script>

@endsection
