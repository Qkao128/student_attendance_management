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
                </ul>

            </div>
        </div>

        <div class="row align-items-center g-2">
            <div class="col">
                <h4 class="header-title">Manage Class</h4>
            </div>
            <div class="col-12 col-md-auto mt-0 mt-md-1">
                <div class="d-flex float-end align-items-center">
                    <button type="button" class="btn btn-success text-white rounded-4" data-bs-toggle="modal"
                        data-bs-target="#add-class-modal">
                        Add
                    </button>
                </div>
            </div>

        </div>

        <div>
            @livewire('class-list', ['userId' => Auth()->id()])
        </div>
    </div>


    <div class="modal fade" id="add-class-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Add New Class
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('class.store') }}" id="form" method="POST">
                        @csrf
                        <div class="row w-100 p-2 g-3" id="add-class-modal-content">
                            <div class="form-group col-12 col-md-6">
                                <label class="form-label" for="course_id">Course<span class="text-danger">*</span></label>
                                <select class="form-select" id="course_id" name="course_id" required style="width:100%;">
                                </select>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label class="form-label" for="user_id">Teacher<span class="text-danger">*</span></label>
                                <select class="form-select" id="user_id" name="user_id" required style="width:100%;">
                                </select>
                            </div>

                            <div class="form-group col-12">
                                <label class="form-label" for="class">Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="class" name="name"
                                    value="{{ old('name') }}" placeholder="Enter name" required>
                            </div>

                        </div>

                        <div class="text-end pe-3">
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
                                    text: item.username,
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

    @stack('scripts')

@endsection
