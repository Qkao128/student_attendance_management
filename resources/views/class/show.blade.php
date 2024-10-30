@php
    use Carbon\Carbon;
@endphp

@extends('layout/layout')

@section('page_title', 'Class')

@section('content')
    <div class="row mt-2">
        <div class="col text-muted">
            <ul class="breadcrumb mb-2 mb-md-1">
                <li class="breadcrumb-item">
                    Dashboard
                </li>
                <li class="breadcrumb-item">
                    Class Management
                </li>
                <li class="breadcrumb-item">
                    Class Management Details
                </li>
            </ul>

        </div>
    </div>

    <div class="row align-items-center mb-3">
        <div class="col">
            <h4 class="header-title">Class Details</h4>
        </div>

        <div class="col-12 col-md-auto mt-0 mt-md-1">
            <div class="d-flex float-end align-items-center gap-2">
                <a href="{{ route('class.index') }}" class="btn btn-dark rounded-4 text-white">
                    <i class="fa-solid fa-angle-left text-white"></i>
                    Back
                </a>

                <form method="post" action={{ route('class.destroy', ['id' => $class->id]) }}>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-4" onclick="deleteFormConfirmation(event)">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </form>

                <button class="btn btn-warning text-dark rounded-4" data-bs-toggle="modal"
                    data-bs-target="#edit-class-modal">
                    <i class="fa-solid fa-pen-nib"></i>
                    Edit
                </button>
            </div>
        </div>
    </div>


    <div class="card border-0 card-shadow px-1">
        <div class="card-body px-md-4">
            <div class="row g-2 g-md-3 align-items-center">

                <div class="col">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Name :
                        </div>

                        <div class="col-12 mt-1">
                            {{ $class->class }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Created At :
                        </div>

                        <div class="col-12 mt-1">
                            {{ Carbon::parse($class->created_at)->format('d-m-Y h:i A') }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-auto">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Created At :
                        </div>

                        <div class="col-12 mt-1">
                            {{ Carbon::parse($class->created_at)->format('d-m-Y h:i A') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row align-items-center g-2 mt-4">
        <div class="col">
            <h4 class="mb-0">Student List</h4>
        </div>
        <div class="col-12 col-md-auto mt-0 mt-md-1">
            <div class="d-flex float-end align-items-center">
                <button type="button" class="btn btn-success text-white py-2 px-3" style="border-radius: 10px;"
                    data-bs-toggle="modal" data-bs-target="#add-student-modal">
                    Add
                </button>
            </div>
        </div>
    </div>



    <div class="row align-items-center g-0 mt-4">
        <div class="col">
            <div class="search-input-group">
                <div class="search-input-icon">
                    <i class="fa fa-search"></i>
                </div>
                <input type="text" class="form-control search-input" placeholder="Search course">
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <table id="table" class="table table-bordered table-striped table-vcenter dt-responsive" style="width:100%">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Created At</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
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
