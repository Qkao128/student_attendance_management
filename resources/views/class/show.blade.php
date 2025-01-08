@php
    use Carbon\Carbon;
@endphp

@extends('layout/layout')


@section('style')
    <style>
        #student-column-container {
            overflow-x: auto;
            width: 100%;
        }

        .student-column-content {
            background-color: #F4F6FA;
            width: 100%;
        }

        @media (max-width: 992px) {
            .student-column-content {
                width: max-content;
            }
        }

        .profile_image_container {
            min-width: 260px;
        }

        .student-name {
            min-width: 250px;
        }

        .student-enrollment-date {
            min-width: 210px;
        }

        .removeButton {
            position: absolute;
            top: 6px;
            left: 6px;
            transform: translate(-50%, -50%);
            width: 21px;
            height: 21px;
            padding: 0;
        }

        .removeButton i {
            font-size: 15px;
        }
    </style>
@endsection

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
                @hasrole('Admin')
                    <!-- 如果是 Admin，這段代碼會被隱藏 -->
                @else
                    <a href="{{ route('class.edit', ['id' => $class->id]) }}" class="btn btn-warning text-dark rounded-4">
                        <i class="fa-solid fa-pen-nib"></i>
                        Edit
                    </a>
                @endhasrole

            </div>
        </div>
    </div>


    <div class="card border-0 card-shadow px-1"
        style="background-color: {{ $class->is_disabled ? '#e8e8e8' : '#F4F6FA' }};">
        <div class="card-body px-md-4">
            <div class="row g-2 g-md-3 align-items-center">
                <div class="col-12 col-sm">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Name :
                        </div>

                        <div class="col-12 mt-1">
                            {{ $class->name }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Member :
                        </div>

                        <div class="col-12 mt-1">
                            <span class="badge bg-primary">{{ $class->member_count ?? 0 }}</span>
                            <i class="fa-solid fa-user ms-2"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-auto">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Created At :
                        </div>

                        <div class="col-12 mt-1">
                            {{ Carbon::parse($class->created_at)->format('d-m-Y h:i A') }}
                        </div>
                    </div>
                </div>

                <hr class="mb-0">

                <div class="col-12 col-md">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Course :
                        </div>

                        <div class="col-12 mt-1">
                            {{ $class->course_name }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md">
                    <div class="row gap-2 d-md-block">
                        <div class="col-12 text-muted">
                            Teacher :
                        </div>

                        <div class="col-12 mt-1">
                            {{ $class->user_name }}
                        </div>
                    </div>
                </div>

                @if ($class->is_disabled)
                    <div class="col-auto ms-auto text-end">
                        <div class="badge bg-danger">
                            Disabled
                        </div>
                    </div>
                @else
                    <div class="col-auto ms-auto text-end">
                        <div class="badge bg-success">
                            Active
                        </div>
                    </div>
                @endif
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
                <input type="search" id="filter-name" class="form-control search-input" placeholder="Search name">
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
                        <th>Enrollment Date</th>
                        <th>Created At</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="add-student-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Add New Student
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('student.store', ['classId' => $class->id]) }}" id="form" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 w-sm-100 p-2" id="student-column-container">
                            <div class="d-flex student-column-content p-2" id="student-column-content-1">
                                <div class="form-group profile_image_container">
                                    <label class="form-label">Profile Image :</label>
                                    <div class="d-flex justify-content-center">
                                        <div class="circle-img-md-wrap rounded-circle border">
                                            <img src="{{ asset('img/default-student-avatar.png') }}"
                                                id="profile-image-display-1"
                                                data-initial-image="{{ asset('img/default-student-avatar.png') }}">
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center ms-2">
                                            <input type="file" class="form-control student-profile-image me-2"
                                                name="student[1][profile_image]" id="profile-image-1"
                                                accept=".png,.jpeg,.jpg" hidden>
                                            <button type="button" onclick="uploadProfileImage(1)"
                                                class="btn btn-primary">Upload</button>
                                            <button type="button" onclick="removeProfileImage(1)"
                                                id="remove-profile-image-btn-1"
                                                class="btn btn-danger ms-2 d-none">Reset</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ps-3 pe-2">
                                    <label class="form-label">Name :</label>
                                    <input type="text" class="form-control student-name" name="student[1][name]"
                                        placeholder="Enter name" value="{{ old('student[1][name]') }}" required>
                                </div>
                                <div class="form-group px-2">
                                    <label class="form-label me-2">Gender:</label>
                                    <div class="d-flex mt-2">
                                        <input type="radio" name="student[1][gender]" value="Male" id="Male-1"
                                            {{ old('student[1][gender]') == 'Male' ? 'checked' : '' }} required>
                                        <label class="ms-2" for="Male-1"> Male</label>
                                        <input type="radio" class="ms-3" name="student[1][gender]" id="Female-1"
                                            value="Female" {{ old('student[1][gender]') == 'Female' ? 'checked' : '' }}
                                            required>
                                        <label class="ms-2" for="Female-1">Female</label>
                                    </div>
                                </div>
                                <div class="form-group px-3">
                                    <label class="form-label">Enrollment Date :</label>
                                    <input type="date" class="form-control student-enrollment-date"
                                        max="{{ Carbon::now()->toDateString() }}" name="student[1][enrollment_date]"
                                        value="{{ Carbon::now()->toDateString() }}" required>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="w-100 py-2 rounded-4" id="add-student-column"
                            onclick="addNewStudentColumn()"
                            style="border: 3px dashed lightgray;background-color: transparent;">
                            <i class="fa fa-plus opacity-50 text-muted"></i>
                        </button>

                        <div class="text-end pe-3 mt-4">
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var classId = {{ $class->id }};

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                sDom: "ltipr",
                ajax: {
                    url: "{{ route('datatable') }}",
                    type: 'POST',
                    data: function(d) {
                        d.class_id = classId;
                    }
                },
                columns: [{
                        data: null,
                        name: "id",
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            var order = meta.settings.aaSorting[0];
                            var isDesc = order[1] === 'desc';
                            var totalRows = meta.settings.json ? meta.settings.json.recordsTotal :
                                0;
                            return isDesc ? totalRows - meta.row : meta.row + 1;
                        }
                    }, {
                        data: null,
                        name: "name",
                        render: function(data, type, row) {
                            var imageUrl = data.profile_image ?
                                `{{ asset('storage/profile_image') }}/${data.profile_image}` :
                                `{{ asset('img/default-student-avatar.png') }}`;
                            var name = data.name;
                            return `
                                <div class="d-flex align-items-center">
                                    <div class="circle-img-sm-wrap rounded-circle border me-3">
                                        <img src="${imageUrl}">
                                    </div>

                                    <span class="fw-bold">${name}</span>
                                </div>`;
                        }
                    },
                    {
                        data: 'gender'
                    },
                    {
                        data: 'enrollment_date',
                        name: "enrollment_date",
                        width: "200px",
                        className: 'text-sm-center',
                        render: function(data, type, row) {
                            var enrollmentDate =
                                moment(data).local().format("DD-MM-YYYY")

                            return enrollmentDate;
                        }
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        width: "220px",
                        className: 'text-sm-center',
                        render: function(data, type, row) {
                            var createdAt =
                                moment(data).local().format("DD-MM-YYYY hh:mm a")

                            return createdAt;
                        }
                    },
                    {
                        data: null,
                        width: "200px",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var editUrl =
                                `{{ route('student.edit', ['classId' => ':classId', 'id' => ':id']) }}`
                                .replace(':classId', row.class_id)
                                .replace(':id', row.id);
                            var deleteUrl =
                                `{{ route('student.destroy', ['classId' => ':classId', 'id' => ':id']) }}`
                                .replace(':classId', row.class_id)
                                .replace(':id', row.id);
                            return `<div class="d-flex justify-content-center align-items-center gap-2">
                                <a href="${editUrl}" class="btn btn-warning rounded-pill"><i class="fa-solid fa-pen-nib"></i></a>
                                <form method="POST" action="${deleteUrl}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger rounded-pill" onclick="deleteFormConfirmation(event)">
                                         <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>`;
                        },
                    }
                ],
                language: {
                    emptyTable: `
                        <div class="text-center">
                            <img class="no-data-found mt-2" src="{{ asset('img/no-data-found.png') }}">
                            <div>
                                No data found
                            </div>
                        </div>`
                }
            });


            $("#filter-name").on("keyup", function() {
                var $table = $('#table').DataTable();
                const name = $(this).val();
                $table.column(1).search(name).draw();
            });
        });

        let studentCount = 1;

        function handleImagePreview(input, rowId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    $(`#profile-image-display-${rowId}`).attr('src', event.target.result);
                    $(`#remove-profile-image-btn-${rowId}`).removeClass('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                resetProfileImage(rowId);
            }
        }

        function uploadProfileImage(rowId) {
            $(`#profile-image-${rowId}`).off('change').on('change', function() {
                handleImagePreview(this, rowId);
            }).click();
        }

        function removeProfileImage(rowId) {
            resetProfileImage(rowId);
            $(`#profile-image-${rowId}`).val('');
        }

        function resetProfileImage(rowId) {
            const defaultImage = "{{ asset('img/default-student-avatar.png') }}";
            $(`#profile-image-display-${rowId}`).attr('src', defaultImage);
            $(`#remove-profile-image-btn-${rowId}`).addClass('d-none');
        }

        let todayDate = "{{ Carbon::now()->toDateString() }}";

        function addNewStudentColumn() {
            if (studentCount > 49) {
                $('#add-student-column').addClass('d-none');
                return;
            }

            studentCount++;

            const newStudentColumn = `
                <div class="d-flex position-relative student-column-content mt-2 p-2" id="student-column-content-${studentCount}">
                    <button type="button" class="btn btn-danger rounded-pill text-center removeButton"
                            onclick="removeStudentColumn(${studentCount})">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="form-group profile_image_container mt-1 me-2">
                        <label class="form-label">Profile Image :</label>
                        <div class="d-flex justify-content-center">
                            <div class="circle-img-md-wrap rounded-circle border">
                                <img src="{{ asset('img/default-student-avatar.png') }}" class="profile-image-display" id="profile-image-display-${studentCount}"
                                    data-initial-image="{{ asset('img/default-student-avatar.png') }}">
                            </div>
                            <div class="d-flex justify-content-center align-items-center ms-2">
                                <input type="file" class="form-control student-profile-image me-2" name="student[${studentCount}][profile_image]"
                                    id="profile-image-${studentCount}" accept=".png,.jpeg,.jpg" hidden>
                                <button type="button" onclick="uploadProfileImage(${studentCount})" class="btn btn-primary">Upload</button>
                                <button type="button" onclick="removeProfileImage(${studentCount})" id="remove-profile-image-btn-${studentCount}"
                                        class="btn btn-danger ms-2 d-none">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group px-2">
                        <label class="form-label">Name :</label>
                        <input type="text" class="form-control student-name" name="student[${studentCount}][name]"
                            placeholder="Enter name" required value="{{ old('student[${studentCount}][name]') }}">
                    </div>
                    <div class="form-group px-2">
                        <label class="form-label me-2">Gender:</label>
                        <div class="d-flex mt-2">
                            <input type="radio" name="student[${studentCount}][gender]" value="Male" id="Male-${studentCount}" required>
                            <label class="ms-2" for="Male-${studentCount}"> Male</label>
                            <input type="radio" class="ms-3" name="student[${studentCount}][gender]" id="Female-${studentCount}" value="Female" required>
                            <label class="ms-2" for="Female-${studentCount}">Female</label>
                        </div>
                    </div>
                    <div class="form-group px-3">
                        <label class="form-label">Enrollment Date :</label>
                         <input type="date" class="form-control student-enrollment-date" name="student[${studentCount}][enrollment_date]" value="${todayDate}" max="${todayDate}" required>
                    </div>
                </div>
            `;

            $('#student-column-container').append(newStudentColumn);

            $(`#profile-image-${studentCount}`).change(function() {
                handleImagePreview(this, studentCount);
            });
        }

        function removeStudentColumn(id) {
            $(`#student-column-content-${id}`).remove();
        }
    </script>
@endsection
